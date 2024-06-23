package forgot_password_proses

import (
	"encoding/base64"
	"fmt"
	"log"
	"math/rand"
	"net/http"

	"telkomgoods/config"
	"telkomgoods/config/whatsapp"

	"golang.org/x/crypto/bcrypt"
)

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

// Fungsi untuk menghasilkan angka acak
func randomNumber(length int) string {
	const numbers = "0123456789"
	result := make([]byte, length)
	for i := range result {
		result[i] = numbers[rand.Intn(len(numbers))]
	}
	return string(result)
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

// Handler untuk mengirim OTP
func SendOTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/user/forgot-password?notif=1", http.StatusFound)
		return
	}

	username := r.FormValue("username")
	nohp := r.FormValue("nohp")

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	var userID int
	var dbNoHP string
	query := "SELECT id_user, nohp FROM tb_user WHERE username = $1"
	err = conn.QueryRow(query, username).Scan(&userID, &dbNoHP)
	if err != nil {
		http.Redirect(w, r, "/user/forgot-password?notif=2", http.StatusFound)
		log.Printf("Failed to find user: %v", err)
		return
	}

	if dbNoHP != nohp {
		http.Redirect(w, r, "/user/forgot-password?notif=2", http.StatusFound)
		log.Println("No HP tidak cocok")
		return
	}

	otp := randomNumber(6)
	tokenOtp := generateToken()
	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = conn.Exec(updateQuery, otp, tokenOtp, userID)
	if err != nil {
		log.Fatal(err)
	}

	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi untuk mereset sandi Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", username, otp, "Nama Toko")
	response, err := whatsapp.SendMessage(nohp, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
	}

	http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpfp?token=%s", base64.StdEncoding.EncodeToString([]byte(tokenOtp))), http.StatusFound)
}

// Handler untuk memverifikasi OTP
func ConfirmOTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/otpfp?notif=1", http.StatusFound)
		return
	}

	otp := r.FormValue("otp")
	tokenOtp := r.FormValue("token")

	decodedToken, err := base64.StdEncoding.DecodeString(tokenOtp)
	log.Printf("Decoded Token: %s", decodedToken)
	if err != nil {
		log.Printf("Error decoding token: %v", err)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpfp?notif=1&token=%s", tokenOtp), http.StatusFound)
		return
	}

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	var userID int
	var dbTokenOtp string
	query := "SELECT id_user, token_otp FROM tb_user WHERE token_otp = $1 AND otp = $2"

	err = conn.QueryRow(query, string(decodedToken), otp).Scan(&userID, &dbTokenOtp)
	// Log print to know the user data and token comparison
	log.Printf("User Data: ID=%d, Provided TokenOtp=%s, DB TokenOtp=%s, OTP=%s", userID, string(decodedToken), dbTokenOtp, otp)
	if err != nil {
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpfp?notif=2&token=%s", tokenOtp), http.StatusFound)
		log.Printf("OTP Tidak Cocok: %v", err)
		return
	}

	http.Redirect(w, r, fmt.Sprintf("telkomgoods/respw?token=%s", tokenOtp), http.StatusFound)
}

// Handler untuk mengganti sandi
func ResetPasswordHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/respw?notif=1", http.StatusFound)
		return
	}

	password1 := r.FormValue("password1")
	password2 := r.FormValue("password2")
	tokenOtp := r.FormValue("token")

	if password1 != password2 {
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/respw?notif=2&token=%s", tokenOtp), http.StatusFound)
		log.Println("Password tidak cocok")
		return
	}

	decodedToken, err := base64.StdEncoding.DecodeString(tokenOtp)
	if err != nil {
		log.Printf("Error decoding token: %v", err)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/respw?notif=1&token=%s", tokenOtp), http.StatusFound)
		return
	}

	conn, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close()

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(password1), bcrypt.DefaultCost)
	if err != nil {
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/respw?notif=1&token=%s", tokenOtp), http.StatusFound)
		log.Printf("Failed to hash password: %v", err)
		return
	}

	var userID int
	query := "SELECT id_user FROM tb_user WHERE token_otp = $1"
	err = conn.QueryRow(query, string(decodedToken)).Scan(&userID)
	log.Printf("User Data: ID=%d, TokenOtp=%s", userID, decodedToken)
	if err != nil {
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/respw?notif=1&token=%s", tokenOtp), http.StatusFound)
		log.Printf("Token tidak valid: %v", err)
		return
	}

	updateQuery := "UPDATE tb_user SET password = $1, otp = '', token_otp = '' WHERE id_user = $2"
	_, err = conn.Exec(updateQuery, string(hashedPassword), userID)
	if err != nil {
		log.Fatalf("Failed to update password: %v", err)
	}

	http.Redirect(w, r, "/user/login?notif=1", http.StatusFound)
	log.Println("Password berhasil direset")
}

// Handler untuk mengirim ulang OTP
func ResendOTPHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Redirect(w, r, "telkomgoods/otpfp?notif=6", http.StatusFound)
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
		http.Redirect(w, r, "telkomgoods/otpfp?token="+resendOtp, http.StatusFound)
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
		http.Redirect(w, r, "telkomgoods/otpfp?token="+resendOtp, http.StatusFound)
		return
	}

	log.Printf("User Data: ID=%d, Username=%s, NoHP=%s, TokenOtp=%s", userData.ID, userData.Username, userData.NoHP, userData.TokenOtp)

	if userData.TokenOtp != decodedToken {
		log.Printf("Token mismatch: expected %s, got %s", decodedToken, userData.TokenOtp)
		http.Redirect(w, r, "telkomgoods/otpfp?token="+resendOtp, http.StatusFound)
		return
	}

	otp := randomNumber(6)
	newTokenOtp := randomString(30)

	updateQuery := "UPDATE tb_user SET otp = $1, token_otp = $2 WHERE id_user = $3"
	_, err = conn.Exec(updateQuery, otp, newTokenOtp, userData.ID)
	if err != nil {
		log.Printf("Error updating OTP and token: %v", err)
		http.Redirect(w, r, "telkomgoods/otpfp?token="+resendOtp, http.StatusFound)
		return
	}

	id_toko := "1"
	var nama_toko string
	query1 := "SELECT nama_toko FROM tb_settings WHERE id_setting = $1"
	err = conn.QueryRow(query1, id_toko).Scan(&nama_toko)
	if err != nil {
		http.Redirect(w, r, "/user/login?notif=3", http.StatusFound)
		return
	}

	// Kirim OTP ke nomor pengguna
	msg := fmt.Sprintf("%s *%s* Adalah kode verifikasi Login Anda. Demi Keamanan jangan bagikan kode ini. \n *%s*", userData.Username, otp, nama_toko)
	response, err := whatsapp.SendMessage(userData.NoHP, msg)
	if err != nil {
		log.Printf("Failed to send WhatsApp message: %v", err)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpfp?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	} else {
		log.Printf("WhatsApp message sent successfully: %s", response)
		http.Redirect(w, r, fmt.Sprintf("telkomgoods/otpfp?token=%s", base64.StdEncoding.EncodeToString([]byte(newTokenOtp))), http.StatusFound)
	}
}
