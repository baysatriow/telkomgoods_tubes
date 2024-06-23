package register_proses

import (
	"encoding/base64"
	"fmt"
	"io"
	"log"
	"math/rand"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
	"time"

	"telkomgoods/config"
	"telkomgoods/config/whatsapp"

	"golang.org/x/crypto/bcrypt"
)

func ensureDir(dirName string) {
	err := os.MkdirAll(dirName, 0755)
	if err != nil {
		log.Fatalf("Failed to create directory %s: %v", dirName, err)
	}
}

func randomNumber(length int) string {
	const numbers = "0123456789"
	result := make([]byte, length)
	for i := range result {
		result[i] = numbers[rand.Intn(len(numbers))]
	}
	return string(result)
}

func randomString(length int) string {
	const characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
	result := make([]byte, length)
	for i := range result {
		result[i] = characters[rand.Intn(len(characters))]
	}
	return string(result)
}

func encodeImageToBase64(filePath string) (string, error) {
	file, err := os.Open(filePath)
	if err != nil {
		return "", fmt.Errorf("failed to open file: %v", err)
	}
	defer file.Close()

	fileContent, err := io.ReadAll(file)
	if err != nil {
		return "", fmt.Errorf("failed to read file: %v", err)
	}

	base64Encoding := base64.StdEncoding.EncodeToString(fileContent)
	return base64Encoding, nil
}

func RegisterUserHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "telkomgoods/register?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Get current time for create_at and update_at fields
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	// Hash password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(r.FormValue("password")), bcrypt.DefaultCost)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
		log.Printf("Failed to hash password: %v", err)
		return
	}

	username := r.FormValue("username")
	nohp := "0" + r.FormValue("nohp")

	// Check if username or nohp already exists
	var existingID int
	err = db.QueryRow("SELECT id_user FROM tb_user WHERE username = $1 OR nohp = $2", username, nohp).Scan(&existingID)
	if err == nil {
		// Username or nohp already exists
		http.Redirect(w, r, "telkomgoods/register?notif=8", http.StatusFound)
		log.Printf("Username or nohp already exists: %s, %s", username, nohp)
		return
	}

	data := map[string]string{
		"username":  username,
		"name":      r.FormValue("name"),
		"email":     r.FormValue("email"),
		"nohp":      nohp,
		"password":  string(hashedPassword),
		"level":     "4", // Set default level to "member"
		"status":    "0", // Set status to inactive (0)
		"create_at": currentTime,
		"update_at": currentTime,
	}

	log.Println("Inserting user data into database...")
	// Insert into tb_user
	status := config.Insert(db, "tb_user", data)
	if status != "OK" {
		http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
		log.Printf("Failed to insert data into tb_user: %v", status)
		return
	}

	var idUser int
	err = db.QueryRow("SELECT id_user FROM tb_user WHERE username = $1 ORDER BY create_at DESC LIMIT 1", username).Scan(&idUser)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
		log.Printf("Failed to retrieve id_user: %v", err)
		return
	}

	log.Println("Ensuring directory exists for user images...")
	// Create directory if not exists
	ensureDir("assets/img/user")

	// Handle file upload
	file, handler, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		fileName := base64.StdEncoding.EncodeToString([]byte(fmt.Sprintf("%d%s", idUser, filepath.Ext(handler.Filename))))
		dest := fmt.Sprintf("assets/img/akun/%s", fileName)

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)
		data["image"] = imgBase64
	} else if err == http.ErrMissingFile {
		// Set default image if no image uploaded
		log.Println("No image uploaded, using default image...")
		defaultImagePath := "assets/admin/img/avatars/1.png"
		defaultImgBase64, err := encodeImageToBase64(defaultImagePath)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
			log.Printf("Failed to encode default image to base64: %v", err)
			return
		}
		data["image"] = defaultImgBase64
	} else {
		log.Printf("Error retrieving file: %v", err)
	}

	log.Println("Updating user data in the database with image...")
	status = config.Update(db, "tb_user", data, map[string]string{"id_user": strconv.Itoa(idUser)})
	if status != "OK" {
		http.Redirect(w, r, "telkomgoods/register?notif=6", http.StatusFound)
		log.Printf("Failed to update data in tb_user: %v", status)
		return
	}

	// Generate OTP
	otp := randomNumber(6)
	tokenOtp := randomString(30)
	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = db.Exec(updateQuery, otp, tokenOtp, idUser)
	if err != nil {
		log.Fatalf("Failed to update OTP: %v", err)
	}

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = db.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login?notif=6", http.StatusFound)
		return
	}

	// Send OTP to user via WhatsApp
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", r.FormValue("username"), otp, nama_toko)
	response, err := whatsapp.SendMessage(r.FormValue("nohp"), msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
	}

	http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpreg/?token=%s", base64.StdEncoding.EncodeToString([]byte(tokenOtp))), http.StatusFound)
	log.Println("User registered successfully")
}

func OTPHandler(w http.ResponseWriter, r *http.Request) {

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6", http.StatusFound)
		return
	}

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	otp := r.FormValue("kode")
	tokenOtp := r.FormValue("token")

	// Decode token dari Base64
	decodedToken, err := base64.StdEncoding.DecodeString(tokenOtp)
	if err != nil {
		log.Printf("Error decoding token: %v", err)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpreg/?notif=6&token=%s", tokenOtp), http.StatusFound)
		return
	}

	var userData struct {
		ID       int
		Username string
		NoHP     string
		Otp      string
		TokenOtp string
		Level    string
	}

	query := "SELECT id_user, username, noHp, otp, token_otp, level FROM tb_user WHERE token_otp = $1 AND otp = $2"
	err = conn.QueryRow(query, string(decodedToken), otp).Scan(&userData.ID, &userData.Username, &userData.NoHP, &userData.Otp, &userData.TokenOtp, &userData.Level)

	if err != nil {
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpreg/?notif=3&token=%s", tokenOtp), http.StatusFound)
		log.Printf("Token Tidak Sama: %v", err)
		return
	}

	lastLogin := time.Now().Format("2006-01-02 15:04:05")
	updateQuery := "UPDATE tb_user SET status = '1', otp = '', last_login = $1, token_otp = '' WHERE id_user = $2"
	_, err = conn.Exec(updateQuery, lastLogin, userData.ID)
	if err != nil {
		log.Fatal(err)
	}

	http.Redirect(w, r, "/telkomgoods/login/?notif=8", http.StatusFound)

	log.Println("OTP verified successfully, user activated")

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		// http.Redirect(w, r, "telkomgoods/register?notif=3", http.StatusFound)
		return
	}

	msg := fmt.Sprintf("Selamat %s Anda Berhasil Registrasi \n Akun Telah Aktif \n > *%s*", userData.Username, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
	}
}

func ResendOTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6", http.StatusFound)
		return
	}

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	resendOtp := r.URL.Query().Get("token")
	log.Printf("Received Token from URL: %s", resendOtp)

	resendOtpDecoded, err := base64.StdEncoding.DecodeString(resendOtp)
	if err != nil {
		log.Printf("Error decoding token: %v", err)
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6&token="+resendOtp, http.StatusFound)
		return
	}
	decodedToken := string(resendOtpDecoded)
	log.Printf("Decoded Token: %s", decodedToken)

	var userData struct {
		ID       int
		Username string
		NoHP     string
		TokenOtp string
	}
	query := "SELECT id_user, username, nohp, token_otp FROM tb_user WHERE token_otp = $1"

	err = conn.QueryRow(query, decodedToken).Scan(&userData.ID, &userData.Username, &userData.NoHP, &userData.TokenOtp)
	if err != nil {
		log.Printf("Error querying user data: %v", err)
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6&token="+resendOtp, http.StatusFound)
		return
	}

	log.Printf("User Data: ID=%d, Username=%s, NoHP=%s, TokenOtp=%s", userData.ID, userData.Username, userData.NoHP, userData.TokenOtp)

	if userData.TokenOtp != decodedToken {
		log.Printf("Token mismatch: expected %s, got %s", decodedToken, userData.TokenOtp)
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6&token="+resendOtp, http.StatusFound)
		return
	}

	otp := randomNumber(6)
	newTokenOtp := randomString(30)

	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = conn.Exec(updateQuery, otp, newTokenOtp, userData.ID)
	if err != nil {
		log.Printf("Error updating OTP and token: %v", err)
		http.Redirect(w, r, "telkomgoods/otpreg/?notif=6&token="+resendOtp, http.StatusFound)
		return
	}

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		// http.Redirect(w, r, "telkomgoods/login?notif=3", http.StatusFound)
		return
	}

	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi registrasi Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", userData.Username, otp, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpreg/?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpreg/?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	}
}
