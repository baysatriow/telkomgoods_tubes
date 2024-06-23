package login_proses

import (
	"database/sql"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"log"
	"math/rand"
	"net/http"
	"strconv"
	"time"

	"telkomgoods/config"
	"telkomgoods/config/whatsapp"

	"golang.org/x/crypto/bcrypt"
)

// Fungsi untuk menghasilkan secret key acak untuk cookie store
// func generateSecretKey() (string, error) {
// 	key := make([]byte, 32)
// 	_, err := rand.Read(key)
// 	if err != nil {
// 		return "", err
// 	}
// 	return base64.StdEncoding.EncodeToString(key), nil
// }

// Fungsi untuk menghasilkan token acak
func generateToken() string {
	const characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
	token := make([]byte, 30)
	for i := range token {
		token[i] = characters[rand.Intn(len(characters))]
	}
	encodedToken := base64.StdEncoding.EncodeToString([]byte(token))
	return encodedToken
}

// Fungsi untuk menyisipkan token ke database
func insertToken(db *sql.DB, userID int) (string, error) {
	if userID == 0 {
		return "", fmt.Errorf("invalid user ID")
	}

	token := generateToken()
	var idToken int
	query := "INSERT INTO tb_token (token) VALUES ($1) RETURNING id_token"
	err := db.QueryRow(query, token).Scan(&idToken)
	if err != nil {
		return "", err
	}

	updateQuery := "UPDATE tb_user SET token_id = $1 WHERE id_user = $2"
	_, err = db.Exec(updateQuery, idToken, userID)
	if err != nil {
		return "", err
	}

	return token, nil
}

// Fungsi untuk menghasilkan string acak
func randomString(length int) string {
	const characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
	result := make([]byte, length)
	for i := range result {
		result[i] = characters[rand.Intn(len(characters))]
	}
	return string(result)
}

// Fungsi untuk menghasilkan angka acak
func randomNumber(length int) string {
	const numbers = "0123456789"
	result := make([]byte, length)
	for i := range result {
		result[i] = numbers[rand.Intn(len(numbers))]
	}
	return string(result)
}

// Handler untuk login
func LoginHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/login/?notif=1", http.StatusFound)
		return
	}

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	user := r.FormValue("username")
	pass := r.FormValue("password")

	if user == "" || pass == "" {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}

	var userData struct {
		ID       int
		Username string
		Password string
		Level    string
		NoHP     string
	}

	id_toko := "1"
	var nama_toko string
	query := "SELECT id_user, username, password, level, nohp FROM tb_user WHERE username = $1"
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query, user).Scan(&userData.ID, &userData.Username, &userData.Password, &userData.Level, &userData.NoHP)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}

	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}

	err = bcrypt.CompareHashAndPassword([]byte(userData.Password), []byte(pass))
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}

	otp := randomNumber(6)
	tokenOtp := randomString(30)
	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = conn.Exec(updateQuery, otp, tokenOtp, userData.ID)
	if err != nil {
		log.Fatal(err)
	}

	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi Login Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", userData.Username, otp, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
	}

	http.Redirect(w, r, fmt.Sprintf("/telkomgoods/otp?token=%s", base64.StdEncoding.EncodeToString([]byte(tokenOtp))), http.StatusFound)
}

// Handler untuk memverifikasi OTP
func OTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/otp?notif=6", http.StatusFound)
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
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/otp?notif=6&token=%s", tokenOtp), http.StatusFound)
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

	query := "SELECT id_user, username, noHP, otp, token_otp, level FROM tb_user WHERE token_otp = $1 AND otp = $2"
	err = conn.QueryRow(query, string(decodedToken), otp).Scan(&userData.ID, &userData.Username, &userData.NoHP, &userData.Otp, &userData.TokenOtp, &userData.Level)

	if err != nil {
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/otp?notif=3&token=%s", tokenOtp), http.StatusFound)
		log.Printf("Token Tidak Sama: %v", err)
		return
	}

	lastLogin := time.Now().Format("2006-01-02 15:04:05")
	updateQuery := "UPDATE tb_user SET status = 1, otp = '', last_login = $1, token_otp = '' WHERE id_user = $2"
	_, err = conn.Exec(updateQuery, lastLogin, userData.ID)
	if err != nil {
		log.Fatal(err)
	}

	sessionData := map[string]string{
		"user_id":  strconv.Itoa(userData.ID),
		"username": userData.Username,
		"level":    userData.Level,
	}
	sessionJSON, err := json.Marshal(sessionData)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	// Encode session JSON to base64
	encodedSession := base64.StdEncoding.EncodeToString(sessionJSON)

	cookie := &http.Cookie{
		Name:    "session-name",
		Value:   encodedSession,
		Expires: time.Now().Add(24 * time.Hour),
		Path:    "/",
	}

	http.SetCookie(w, cookie)

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}
	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("Selamat %s Anda Berhasil login *%s*", userData.Username, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
	}

	http.Redirect(w, r, "/telkomgoods/", http.StatusFound)
}

// Handler untuk mengirim ulang OTP
func ResendOTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Redirect(w, r, "/telkomgoods/otp?notif=6", http.StatusFound)
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
		http.Redirect(w, r, "/telkomgoods/otp?token="+resendOtp, http.StatusFound)
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
		http.Redirect(w, r, "/telkomgoods/otp?token="+resendOtp, http.StatusFound)
		return
	}

	log.Printf("User Data: ID=%d, Username=%s, NoHP=%s, TokenOtp=%s", userData.ID, userData.Username, userData.NoHP, userData.TokenOtp)

	if userData.TokenOtp != decodedToken {
		log.Printf("Token mismatch: expected %s, got %s", decodedToken, userData.TokenOtp)
		http.Redirect(w, r, "/telkomgoods/otp?token="+resendOtp, http.StatusFound)
		return
	}

	otp := randomNumber(6)
	newTokenOtp := randomString(30)

	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = conn.Exec(updateQuery, otp, newTokenOtp, userData.ID)
	if err != nil {
		log.Printf("Error updating OTP and token: %v", err)
		http.Redirect(w, r, "/telkomgoods/otp?token="+resendOtp, http.StatusFound)
		return
	}

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		http.Redirect(w, r, "telkomgoods/login/?notif=3", http.StatusFound)
		return
	}

	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi Login Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", userData.Username, otp, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/otp?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/otp?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	}
}
