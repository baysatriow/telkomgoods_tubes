package mod_akun

import (
	"database/sql"
	"encoding/base64"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"telkomgoods/config"

	"golang.org/x/crypto/bcrypt"
)

// Fungsi untuk membuat folder
func ensureDir(dirName string) {
	err := os.MkdirAll(dirName, 0755)
	if err != nil {
		log.Fatalf("Failed to create directory %s: %v", dirName, err)
	}
}

// Fungsi untuk menghapus file
func removeFileIfExists(filePath string) {
	if _, err := os.Stat(filePath); err == nil {
		err = os.Remove(filePath)
		if err != nil {
			log.Printf("Failed to remove file %s: %v", filePath, err)
		}
	}
}

func UpdateHandlerAkun(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
		// http.Error(w, "Invalid request method", http.StatusMethodNotAllowed)
		return
	}

	err = r.ParseMultipartForm(2 << 20) // 2 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/akun?notif=7", http.StatusFound)
		// http.Error(w, "Unable to parse form", http.StatusInternalServerError)
		return
	}

	// Cek ukuran file apakah lebih dari 2MB
	for _, fileHeaders := range r.MultipartForm.File {
		for _, fileHeader := range fileHeaders {
			if fileHeader.Size > 2<<20 {
				http.Redirect(w, r, "/telkomgoods/admin/akun?notif=7", http.StatusFound)
				return
			}
		}
	}

	// Ambil Waktu Realtime!
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	data := map[string]string{
		"name":      r.FormValue("name"),
		"email":     r.FormValue("email"),
		"nohp":      ("0") + r.FormValue("nohp"),
		"update_at": currentTime,
	}
	where := map[string]string{
		"id_user": r.FormValue("id_user"),
	}

	// Update database with new data
	status := config.Update(db, "tb_user", data, where)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/akun?notif=3", http.StatusFound)
		return
	}

	// Buat Folder jika belum ada
	ensureDir("assets/img/akun")

	// Handle file upload
	if file, handler, err := r.FormFile("image"); err == nil {
		defer file.Close()

		var username string
		err = db.QueryRow("SELECT username FROM tb_user WHERE id_user = $1", r.FormValue("id_user")).Scan(&username)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
			return
		}

		// Buat tujuan penyimpanan
		dest := fmt.Sprintf("assets/img/akun/%s%s", username, filepath.Ext(handler.Filename))

		var existingImage string
		err = db.QueryRow("SELECT image FROM tb_user WHERE id_user = $1", r.FormValue("id_user")).Scan(&existingImage)
		if err == nil && existingImage != "" {
			existingImagePath := filepath.Join("assets/img/akun", existingImage)
			removeFileIfExists(existingImagePath)
		}

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
			// http.Error(w, fmt.Sprintf("Failed to create file: %v", err), http.StatusInternalServerError)
			return
		}
		defer out.Close()

		// Copy file to destination
		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
			// http.Error(w, fmt.Sprintf("Failed to copy file: %v", err), http.StatusInternalServerError)
			return
		}

		// Open file to read its content
		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
			// http.Error(w, fmt.Sprintf("Failed to open file: %v", err), http.StatusInternalServerError)
			return
		}
		defer imgFile.Close()

		// Read file content
		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=6", http.StatusFound)
			// http.Error(w, fmt.Sprintf("Failed to read file: %v", err), http.StatusInternalServerError)
			return
		}

		// Encode file content to base64
		imgBase64 := base64.StdEncoding.EncodeToString(imgData)
		data["image"] = imgBase64
		// Update database with new data
		status := config.Update(db, "tb_user", data, where)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/akun?notif=4", http.StatusFound)
			return
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/akun?notif=5", http.StatusFound)
}

func UpdateHandlerAkunPw(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
		// http.Error(w, "Invalid request method", http.StatusMethodNotAllowed)
		return
	}

	err = r.ParseForm()
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
		// http.Error(w, "Unable to parse form", http.StatusInternalServerError)
		return
	}

	// Ambil waktu Realtime
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	idUser := r.FormValue("id_user")
	passwordOld := r.FormValue("password_old")
	passwordNew := r.FormValue("password_new")

	// Check if id_user is obtained from form and not empty
	if idUser == "" {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
		// http.Error(w, "User ID is required", http.StatusBadRequest)
		return
	}

	// Retrieve user by ID
	var user struct {
		ID       uint   `db:"id_user"`
		Password string `db:"password"`
	}
	query := ("SELECT id_user, password FROM tb_user WHERE id_user = $1")
	err = db.QueryRow(query, idUser).Scan(&user.ID, &user.Password)
	if err != nil {
		if err == sql.ErrNoRows {
			http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
			// http.Error(w, "User not found", http.StatusBadRequest)
		} else {
			http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
			// http.Error(w, "Database query error", http.StatusInternalServerError)
		}
		fmt.Println(idUser)
		fmt.Println(err)
		return
	}

	// Cek Password lama dengan data dari form password lama
	err = bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(passwordOld))
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=3", http.StatusFound)
		return
	}

	// Hash the new password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(passwordNew), bcrypt.DefaultCost)
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=6", http.StatusFound)
		// http.Error(w, "Failed to hash new password", http.StatusInternalServerError)
		return
	}

	// Prepare data for update
	data := map[string]string{
		"password":  string(hashedPassword),
		"update_at": currentTime,
	}
	where := map[string]string{
		"id_user": idUser,
	}

	// Update database with new data
	status := config.Update(db, "tb_user", data, where)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/password?notif=4", http.StatusFound)
		return
	}

	http.Redirect(w, r, "/telkomgoods/admin/password?notif=5", http.StatusFound)
}
