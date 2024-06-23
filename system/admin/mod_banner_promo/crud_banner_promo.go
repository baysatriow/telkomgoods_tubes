package mod_banner_promo

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

	_ "github.com/lib/pq"
)

// Helper function to create directory if it doesn't exist
func ensureDir(dirName string) {
	err := os.MkdirAll(dirName, 0755)
	if err != nil {
		log.Fatalf("Failed to create directory %s: %v", dirName, err)
	}
}

// Helper function to remove file if it exists
func removeFileIfExists(filePath string) {
	if _, err := os.Stat(filePath); err == nil {
		err = os.Remove(filePath)
		if err != nil {
			log.Printf("Failed to remove file %s: %v", filePath, err)
		}
	}
}

// AddPromoHandler handles adding a new promo
func AddPromoHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Get current time
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	data := map[string]string{
		"nama_promo":  r.FormValue("nama_promo"),
		"harga":       r.FormValue("harga"),
		"diskon":      r.FormValue("diskon"),
		"tgl_selesai": r.FormValue("tgl_selesai"),
		"created_at":  currentTime,
		"updated_at":  currentTime,
		"status":      r.FormValue("status"),
	}

	// Insert into tb_banner_promo
	status := config.Insert(db, "tb_banner_promo", data)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=8", http.StatusFound)
		log.Printf("Failed to insert data into tb_banner_promo: %v", status)
		return
	}

	var idPromo int
	err = db.QueryRow("SELECT id_banner_promo FROM tb_banner_promo WHERE nama_promo = $1 ORDER BY created_at DESC LIMIT 1", r.FormValue("nama_promo")).Scan(&idPromo)
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=8", http.StatusFound)
		log.Printf("Failed to retrieve id_banner_promo: %v", err)
		return
	}

	// Create directory if not exists
	ensureDir("assets/img/promo")

	// Handle file upload
	file, handler, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		dest := fmt.Sprintf("assets/img/promo/%d%s", idPromo, filepath.Ext(handler.Filename))

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)

		// Update tb_banner_promo with image data
		updateData := map[string]string{
			"image": imgBase64,
		}
		where := map[string]string{
			"id_banner_promo": strconv.Itoa(idPromo),
		}
		status = config.Update(db, "tb_banner_promo", updateData, where)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			log.Printf("Failed to update image data in tb_banner_promo: %v", status)
			return
		}
	} else if err != http.ErrMissingFile {
		log.Printf("Error retrieving file: %v", err)
	}

	http.Redirect(w, r, "/telkomgoods/admin/addpromo?notif=5", http.StatusFound)
	log.Println("Promo added successfully")
}

// UpdatePromoHandler handles updating an existing promo
func UpdatePromoHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	// Get current time
	currentTime := time.Now().Format("2006-01-02 15:04:05")

	idPromo := r.FormValue("id_banner_promo")

	data := map[string]string{
		"nama_promo":  r.FormValue("nama_promo"),
		"harga":       r.FormValue("harga"),
		"diskon":      r.FormValue("diskon"),
		"tgl_selesai": r.FormValue("tgl_selesai"),
		"updated_at":  currentTime,
	}

	// Update tb_banner_promo
	status := config.Update(db, "tb_banner_promo", data, map[string]string{"id_banner_promo": idPromo})
	if status != "OK" {
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=8", idPromo), http.StatusFound)
		log.Printf("Failed to update data in tb_banner_promo: %v", status)
		return
	}

	// Handle file upload
	file, handler, err := r.FormFile("image")
	if err == nil {
		defer file.Close()
		dest := fmt.Sprintf("assets/img/promo/%s%s", idPromo, filepath.Ext(handler.Filename))

		// Remove existing image if exists
		var existingImage string
		err = db.QueryRow("SELECT image FROM tb_banner_promo WHERE id_banner_promo = $1", idPromo).Scan(&existingImage)
		if err == nil && existingImage != "" {
			existingImagePath := filepath.Join("assets/img/promo", existingImage)
			removeFileIfExists(existingImagePath)
		}

		out, err := os.Create(dest)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=9", idPromo), http.StatusFound)
			log.Printf("Failed to create file %s: %v", dest, err)
			return
		}
		defer out.Close()

		_, err = io.Copy(out, file)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=9", idPromo), http.StatusFound)
			log.Printf("Failed to copy file to %s: %v", dest, err)
			return
		}

		imgFile, err := os.Open(dest)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=9", idPromo), http.StatusFound)
			log.Printf("Failed to open file %s: %v", dest, err)
			return
		}
		defer imgFile.Close()

		imgData, err := io.ReadAll(imgFile)
		if err != nil {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=9", idPromo), http.StatusFound)
			log.Printf("Failed to read file %s: %v", dest, err)
			return
		}

		imgBase64 := base64.StdEncoding.EncodeToString(imgData)

		// Update tb_banner_promo with image data
		updateData := map[string]string{
			"image": imgBase64,
		}
		where := map[string]string{
			"id_banner_promo": idPromo,
		}
		status = config.Update(db, "tb_banner_promo", updateData, where)
		if status != "OK" {
			http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editpromo?id=%s&notif=9", idPromo), http.StatusFound)
			log.Printf("Failed to update image data in tb_banner_promo: %v", status)
			return
		}
	} else if err != http.ErrMissingFile {
		log.Printf("Error retrieving file: %v", err)
	}

	http.Redirect(w, r, "/telkomgoods/admin/promos?notif=5", http.StatusFound)
	log.Println("Promo updated successfully")
}

// DeletePromoHandler handles deleting promos
func DeletePromoHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	ids := r.URL.Query()["id"]
	if len(ids) == 0 {
		http.Redirect(w, r, "/telkomgoods/admin/promos?notif=8", http.StatusFound)
		log.Printf("id_banner_promo is required")
		return
	}

	for _, idPromo := range ids {
		log.Printf("Get Id Promo: %v", idPromo)
		where := map[string]string{
			"id_banner_promo": idPromo,
		}

		// Remove existing image if exists
		var existingImage string
		err := db.QueryRow("SELECT image FROM tb_banner_promo WHERE id_banner_promo = $1", idPromo).Scan(&existingImage)
		if err == nil && existingImage != "" {
			existingImagePath := filepath.Join("assets/img/promo", existingImage)
			removeFileIfExists(existingImagePath)
		}

		// Delete promo
		status := config.Delete(db, "tb_banner_promo", where)
		log.Printf("Status Delete from tb_banner_promo: %v", status)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/promos?notif=9", http.StatusFound)
			return
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/promos?notif=1", http.StatusFound)
	log.Println("Promos deleted successfully")
}
