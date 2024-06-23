package mod_setting

import (
	"encoding/base64"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"path/filepath"

	"telkomgoods/config"
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

func UpdateHandlerSetting(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "telkomgoods/admin/setting?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Cek apakah ukuran file yang di upload melebihi 5 MB
	for _, fileHeaders := range r.MultipartForm.File {
		for _, fileHeader := range fileHeaders {
			if fileHeader.Size > 5<<20 {
				http.Redirect(w, r, "/telkomgoods/admin/setting?notif=7", http.StatusFound)
				log.Printf("File %s exceeds 5MB limit", fileHeader.Filename)
				return
			}
		}
	}

	data := map[string]string{
		"nama_toko":    r.FormValue("nama_toko"),
		"alamat":       r.FormValue("alamat"),
		"kota":         r.FormValue("kota"),
		"provinsi":     r.FormValue("provinsi"),
		"email":        r.FormValue("email"),
		"no_telp":      ("0") + r.FormValue("no_telp"),
		"nama_pemilik": r.FormValue("nama_pemilik"),
		"api_wa":       r.FormValue("api_wa"),
		"no_rek":       r.FormValue("no_rek"),
		"nama_rek":     r.FormValue("nama_rek"),
		"nama_bank":    r.FormValue("nama_bank"),
	}
	where := map[string]string{
		"id_setting": "1",
	}

	log.Println("Updating settings data in the database...")
	status := config.Update(db, "tb_settings", data, where)
	if status != "OK" {
		http.Redirect(w, r, "telkomgoods/admin/setting?notif=3", http.StatusFound)
		log.Printf("Failed to update data in tb_settings: %v", status)
		return
	}

	ensureDir("assets/img/logo")
	ensureDir("assets/img/favicon")

	if file, handler, err := r.FormFile("logo"); err == nil {
		defer file.Close()

		var nama_toko string
		err = db.QueryRow("SELECT nama_toko FROM tb_settings WHERE id_setting = $1", "1").Scan(&nama_toko)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to retrieve nama_toko: %v", err)
			return
		}

		nama_toko += "_logo"
		dest := fmt.Sprintf("assets/img/toko/%s%s", nama_toko, filepath.Ext(handler.Filename))

		var existingImage string
		err = db.QueryRow("SELECT nama_toko FROM tb_settings WHERE id_setting = $1", "1").Scan(&existingImage)

		existingImage += "_logo"
		if err == nil && existingImage != "" {
			existingImagePath := filepath.Join("assets/img/toko", existingImage)
			removeFileIfExists(existingImagePath)
			log.Printf("Removed existing image: %s", existingImagePath)
		}

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)
		data2 := map[string]string{"logo": imgBase64}
		log.Println("Updating logo in the database...")
		status = config.Update(db, "tb_settings", data2, where)
		if status != "OK" {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=4", http.StatusFound)
			log.Printf("Failed to update logo in tb_settings: %v", status)
			return
		}
	}

	if file, handler, err := r.FormFile("favicon"); err == nil {
		defer file.Close()

		var nama_toko string
		err = db.QueryRow("SELECT nama_toko FROM tb_settings WHERE id_setting = $1", "1").Scan(&nama_toko)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to retrieve nama_toko: %v", err)
			return
		}

		nama_toko += "_favicon"
		dest := fmt.Sprintf("assets/img/toko/%s%s", nama_toko, filepath.Ext(handler.Filename))

		var existingImage string
		err = db.QueryRow("SELECT nama_toko FROM tb_settings WHERE id_setting = $1", "1").Scan(&existingImage)

		existingImage += "_favicon"
		if err == nil && existingImage != "" {
			existingImagePath := filepath.Join("assets/img/toko", existingImage)
			removeFileIfExists(existingImagePath)
			log.Printf("Removed existing favicon: %s", existingImagePath)
		}

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=6", http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)
		data3 := map[string]string{"favicon": imgBase64}
		log.Println("Updating favicon in the database...")
		status = config.Update(db, "tb_settings", data3, where)
		if status != "OK" {
			http.Redirect(w, r, "telkomgoods/admin/setting?notif=4", http.StatusFound)
			log.Printf("Failed to update favicon in tb_settings: %v", status)
			return
		}
	}

	http.Redirect(w, r, "telkomgoods/admin/setting?notif=5", http.StatusFound)
	log.Println("Settings updated successfully")
}
