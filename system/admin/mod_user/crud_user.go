package mod_user

import (
	"encoding/base64"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"strconv"
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

// Fungsi untuk Encode Gambbar ke Base64
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

func AddUserHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Mengambil Waktu Realtime
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	// Hash password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(r.FormValue("password")), bcrypt.DefaultCost)
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("Failed to hash password: %v", err)
		return
	}

	data := map[string]string{
		"username":  r.FormValue("username"),
		"name":      r.FormValue("name"),
		"email":     r.FormValue("email"),
		"nohp":      "0" + r.FormValue("nohp"),
		"password":  string(hashedPassword),
		"level":     r.FormValue("level"),
		"status":    r.FormValue("status"),
		"create_at": currentTime,
		"update_at": currentTime,
	}

	// Insert data ke tb_user
	status := config.Insert(db, "tb_user", data)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("Failed to insert data into tb_user: %v", status)
		return
	}

	var idUser int
	err = db.QueryRow("SELECT id_user FROM tb_user WHERE username = $1 ORDER BY create_at DESC LIMIT 1", r.FormValue("username")).Scan(&idUser)
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("Failed to retrieve id_user: %v", err)
		return
	}

	// Buat Folder jika belum ada
	ensureDir("assets/img/user")

	// Handle file upload
	file, handler, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		fileName := base64.StdEncoding.EncodeToString([]byte(fmt.Sprintf("%d%s", idUser, filepath.Ext(handler.Filename))))
		dest := fmt.Sprintf("assets/img/akun/%s", fileName)

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)
		data["image"] = imgBase64
	} else if err == http.ErrMissingFile {
		// Set Gambar Default untuk user baru!
		defaultImagePath := "assets/admin/img/avatars/1.png"
		defaultImgBase64, err := encodeImageToBase64(defaultImagePath)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/register?notif=4", http.StatusFound)
			log.Printf("Failed to encode default image to base64: %v", err)
			return
		}
		data["image"] = defaultImgBase64
	} else {
		log.Printf("Error retrieving file: %v", err)
	}

	// Update data untuk mengisi kolom gambar
	status = config.Update(db, "tb_user", data, map[string]string{"id_user": strconv.Itoa(idUser)})
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=3", http.StatusFound)
		log.Printf("Failed to update data in tb_user: %v", status)
		return
	}

	http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=5", http.StatusFound)
	log.Println("User added successfully")
}

// Fungsi untuk Edit data!
func UpdateUserHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // Maksimal Ukuran 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Mengambil Waktu Realtime
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	idUser := r.FormValue("id_user")

	data := map[string]string{
		"username":  r.FormValue("username"),
		"name":      r.FormValue("name"),
		"email":     r.FormValue("email"),
		"nohp":      r.FormValue("nohp"),
		"level":     r.FormValue("level"),
		"status":    r.FormValue("status"),
		"update_at": currentTime,
	}

	// Update Passowrd jika kolom password di isi!
	if r.FormValue("password") != "" {
		hashedPassword, err := bcrypt.GenerateFromPassword([]byte(r.FormValue("password")), bcrypt.DefaultCost)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
			log.Printf("Failed to hash password: %v", err)
			return
		}
		data["password"] = string(hashedPassword)
	}

	// Update tb_user
	status := config.Update(db, "tb_user", data, map[string]string{"id_user": idUser})
	if status != "OK" {
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=3", idUser), http.StatusFound)
		log.Printf("Failed to update data in tb_user: %v", status)
		return
	}

	// Handle file upload
	file, handler, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		fileName := base64.StdEncoding.EncodeToString([]byte(fmt.Sprintf("%s%s", idUser, filepath.Ext(handler.Filename))))
		dest := fmt.Sprintf("assets/img/akun/%s", fileName)

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=4", idUser), http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=4", idUser), http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=4", idUser), http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=4", idUser), http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)

		// Update image in tb_user
		status := config.Update(db, "tb_user", map[string]string{"image": imgBase64}, map[string]string{"id_user": idUser})
		if status != "OK" {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/userdata?id=%s&notif=4", idUser), http.StatusFound)
			log.Printf("Failed to update image in tb_user: %v", status)
			return
		}
	} else if err != http.ErrMissingFile {
		log.Printf("Error retrieving file: %v", err)
	}

	http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=2", http.StatusFound)
	log.Println("User updated successfully")
}

// Fungsi Hapus data user!
func DeleteUserHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	ids := r.URL.Query()["id"]
	if len(ids) == 0 {
		http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=6", http.StatusFound)
		log.Printf("id_user is required")
		return
	}

	for _, idUser := range ids {
		log.Printf("Get Id User: %v", idUser)
		where := map[string]string{
			"id_user": idUser,
		}

		// Menghapus Gambar Sebelumnya
		var imgBase64 string
		err := db.QueryRow("SELECT image FROM tb_user WHERE id_user = $1", idUser).Scan(&imgBase64)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=3", http.StatusFound)
			log.Printf("Failed to retrieve user image: %v", err)
			return
		}

		// Decode base64 untuk mengambil path file
		imgFileName, err := base64.StdEncoding.DecodeString(imgBase64)
		if err != nil {
			log.Printf("Failed to decode base64 image: %v", err)
			continue
		}

		// Menghapus file yang sudah ada!
		imgFilePath := fmt.Sprintf("assets/img/akun/%s", imgFileName)
		removeFileIfExists(imgFilePath)

		// Hapus data dari tb_user
		status := config.Delete(db, "tb_user", where)
		log.Printf("Status Delete from tb_user: %v", status)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=3", http.StatusFound)
			return
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/userdata?notif=8", http.StatusFound)
	log.Println("Users deleted successfully")
}
