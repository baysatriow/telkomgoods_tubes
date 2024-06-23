package mod_produk

import (
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"sort"
	"strconv"
	"strings"
	"time"

	"telkomgoods/config"
)

type Product struct {
	IDProduk        int     `json:"id_produk"`
	NamaProduk      string  `json:"nama_produk"`
	Harga           float64 `json:"harga"`
	Stok            int     `json:"stok"`
	Status          int     `json:"status"`
	Kategori        int     `json:"kategori"`
	NamaKategori    string  `json:"nama_kategori"`
	DeskripsiProduk string  `json:"deskripsi_produk"`
	KodeSKU         string  `json:"kode_sku"`
	Gambar          string  `json:"gambar"`
}

func ensureDir(dirName string) {
	err := os.MkdirAll(dirName, 0755)
	if err != nil {
		log.Fatalf("Failed to create directory %s: %v", dirName, err)
	}
}

func removeFileIfExists(filePath string) {
	if _, err := os.Stat(filePath); err == nil {
		err = os.Remove(filePath)
		if err != nil {
			log.Printf("Failed to remove file %s: %v", filePath, err)
		}
	}
}

func selectionSort(products []Product, order string) []Product {
	n := len(products)
	for i := 0; i < n-1; i++ {
		idx := i
		for j := i + 1; j < n; j++ {
			if (order == "asc" && products[j].NamaProduk < products[idx].NamaProduk) ||
				(order == "desc" && products[j].NamaProduk > products[idx].NamaProduk) {
				idx = j
			}
		}
		products[i], products[idx] = products[idx], products[i]
	}
	return products
}

func insertionSort(products []Product, order string) []Product {
	n := len(products)
	for i := 1; i < n; i++ {
		key := products[i]
		j := i - 1
		for j >= 0 && ((order == "asc" && products[j].Harga > key.Harga) ||
			(order == "desc" && products[j].Harga < key.Harga)) {
			products[j+1] = products[j]
			j = j - 1
		}
		products[j+1] = key
	}
	return products
}

func FetchProdukHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		http.Error(w, "Failed to connect to database", http.StatusInternalServerError)
		log.Fatal(err)
	}
	defer db.Close()

	sortBy := r.URL.Query().Get("sort")
	if sortBy == "" {
		sortBy = "id_produk" // default sort column
	}

	order := r.URL.Query().Get("order")
	if order != "asc" && order != "desc" {
		order = "asc"
	}

	page, err := strconv.Atoi(r.URL.Query().Get("page"))
	if err != nil || page < 1 {
		page = 1
	}
	limit, err := strconv.Atoi(r.URL.Query().Get("limit"))
	if err != nil || limit < 1 {
		limit = 10
	}
	offset := (page - 1) * limit

	query := `
		SELECT p.id_produk, p.nama_produk, p.harga, p.stok, p.status, p.kategori, k.nama_kategori,
			   p.deskripsi_produk, p.kode_sku, COALESCE(g.gambar, '') as gambar
		FROM tb_produk p
		LEFT JOIN tb_kategori_produk k ON p.kategori = k.id_kategori
		LEFT JOIN (
			SELECT id_produk, gambar
			FROM tb_produk_gambar
			WHERE id_gambar IN (
				SELECT MIN(id_gambar)
				FROM tb_produk_gambar
				GROUP BY id_produk
			)
		) g ON p.id_produk = g.id_produk`
	rows, err := db.Query(query)
	if err != nil {
		http.Error(w, "Failed to retrieve products", http.StatusInternalServerError)
		log.Printf("Failed to retrieve products: %v", err)
		return
	}
	defer rows.Close()

	products := make([]Product, 0)

	for rows.Next() {
		var product Product
		if err := rows.Scan(&product.IDProduk, &product.NamaProduk, &product.Harga, &product.Stok, &product.Status, &product.Kategori, &product.NamaKategori, &product.DeskripsiProduk, &product.KodeSKU, &product.Gambar); err != nil {
			http.Error(w, "Failed to scan product", http.StatusInternalServerError)
			log.Printf("Failed to scan product: %v", err)
			return
		}
		products = append(products, product)
	}

	// Sort products using quicksort
	quicksort(products, sortBy, order)

	// Paginate results
	start := offset
	end := offset + limit
	if start > len(products) {
		start = len(products)
	}
	if end > len(products) {
		end = len(products)
	}
	paginatedProducts := products[start:end]

	w.Header().Set("Content-Type", "application/json")
	if err := json.NewEncoder(w).Encode(paginatedProducts); err != nil {
		http.Error(w, "Failed to encode products to JSON", http.StatusInternalServerError)
		log.Printf("Failed to encode products to JSON: %v", err)
	}
}

// quicksort function to sort products
func quicksort(products []Product, sortBy string, order string) {
	sort.Slice(products, func(i, j int) bool {
		var less bool
		switch sortBy {
		case "id_produk":
			less = products[i].IDProduk < products[j].IDProduk
		case "nama_produk":
			less = products[i].NamaProduk < products[j].NamaProduk
		case "harga":
			less = products[i].Harga < products[j].Harga
		case "stok":
			less = products[i].Stok < products[j].Stok
		case "status":
			less = products[i].Status < products[j].Status
		case "kategori":
			less = products[i].Kategori < products[j].Kategori
		case "nama_kategori":
			less = products[i].NamaKategori < products[j].NamaKategori
		case "deskripsi_produk":
			less = products[i].DeskripsiProduk < products[j].DeskripsiProduk
		case "kode_sku":
			less = products[i].KodeSKU < products[j].KodeSKU
		}
		if order == "asc" {
			return less
		}
		return !less
	})
}

func AddProdukHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	currentTime := time.Now().Format("2006-01-02 15:04:05")

	data := map[string]string{
		"nama_produk":      r.FormValue("nama_produk"),
		"deskripsi_produk": r.FormValue("deskripsi_produk"),
		"harga":            r.FormValue("harga_hidden"),
		"kategori":         r.FormValue("kategori"),
		"stok":             r.FormValue("stok"),
		"created_at":       currentTime,
		"updated_at":       currentTime,
		"berat":            r.FormValue("berat_hidden"),
		"kode_sku":         r.FormValue("kode_sku"),
		"status":           r.FormValue("status"),
	}

	status := config.Insert(db, "tb_produk", data)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=8", http.StatusFound)
		log.Printf("Failed to insert data into tb_produk: %v", status)
		return
	}

	var idProduk int
	err = db.QueryRow("SELECT id_produk FROM tb_produk WHERE nama_produk = $1 ORDER BY created_at DESC LIMIT 1", r.FormValue("nama_produk")).Scan(&idProduk)
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=8", http.StatusFound)
		log.Printf("Failed to retrieve id_produk: %v", err)
		return
	}

	ensureDir("assets/img/produk")

	for i := 1; i <= 5; i++ {
		file, handler, err := r.FormFile(fmt.Sprintf("image%d", i))
		if err == nil {
			defer file.Close()
			dest := fmt.Sprintf("assets/img/produk/%d%s", idProduk, filepath.Ext(handler.Filename))

			out, err := os.Create(dest)
			if err != nil {
				http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
				log.Printf("Failed to create file %s: %v", dest, err)
				return
			}
			defer out.Close()

			_, err = io.Copy(out, file)
			if err != nil {
				http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
				log.Printf("Failed to copy file to %s: %v", dest, err)
				return
			}

			imgFile, err := os.Open(dest)
			if err != nil {
				http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
				log.Printf("Failed to open file %s: %v", dest, err)
				return
			}
			defer imgFile.Close()

			imgData, err := io.ReadAll(imgFile)
			if err != nil {
				http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
				log.Printf("Failed to read file %s: %v", dest, err)
				return
			}

			imgBase64 := base64.StdEncoding.EncodeToString(imgData)

			gambarData := map[string]string{
				"id_produk": strconv.Itoa(idProduk),
				"gambar":    imgBase64,
			}
			status := config.Insert(db, "tb_produk_gambar", gambarData)
			if status != "OK" {
				http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
				log.Printf("Failed to insert data into tb_produk_gambar: %v", status)
				return
			}
		} else if err != http.ErrMissingFile {
			log.Printf("Error retrieving file %d: %v", i, err)
		}
	}

	for variantCount := 1; variantCount <= 2; variantCount++ {
		variantType := r.FormValue(fmt.Sprintf("variant_type%d", variantCount))
		if variantType != "" {
			options := r.Form[fmt.Sprintf("variantOptions%d[]", variantCount)]
			for _, optionValue := range options {
				optionData := map[string]string{
					"id_produk":    strconv.Itoa(idProduk),
					"option_name":  variantType,
					"option_value": optionValue,
				}
				status := config.Insert(db, "tb_products_options", optionData)
				if status != "OK" {
					http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
					log.Printf("Failed to insert data into tb_products_options: %v", status)
					return
				}
			}
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/addproduk?notif=5", http.StatusFound)
	log.Println("Product added successfully")
}

func UpdateProdukHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	err = r.ParseMultipartForm(5 << 20) // 5 MB
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=7", http.StatusFound)
		log.Printf("Unable to parse form: %v", err)
		return
	}

	currentTime := time.Now().Format("2006-01-02 15:04:05")

	idProduk := r.FormValue("id_produk")

	data := map[string]string{
		"nama_produk":      r.FormValue("nama_produk"),
		"deskripsi_produk": r.FormValue("deskripsi_produk"),
		"harga":            r.FormValue("harga_hidden"),
		"kategori":         r.FormValue("kategori"),
		"stok":             r.FormValue("stok"),
		"updated_at":       currentTime,
		"berat":            r.FormValue("berat_hidden"),
		"kode_sku":         r.FormValue("kode_sku"),
		"status":           r.FormValue("status"),
	}

	status := config.Update(db, "tb_produk", data, map[string]string{"id_produk": idProduk})
	if status != "OK" {
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=8", idProduk), http.StatusFound)
		log.Printf("Failed to update data in tb_produk: %v", status)
		return
	}

	for i := 1; i <= 5; i++ {
		file, handler, err := r.FormFile(fmt.Sprintf("image%d", i))
		if err == nil {
			defer file.Close()
			dest := fmt.Sprintf("assets/img/produk/%s%s", idProduk, filepath.Ext(handler.Filename))

			out, err := os.Create(dest)
			if err != nil {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to create file %s: %v", dest, err)
				return
			}
			defer out.Close()

			_, err = io.Copy(out, file)
			if err != nil {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to copy file to %s: %v", dest, err)
				return
			}

			imgFile, err := os.Open(dest)
			if err != nil {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to open file %s: %v", dest, err)
				return
			}
			defer imgFile.Close()

			imgData, err := io.ReadAll(imgFile)
			if err != nil {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to read file %s: %v", dest, err)
				return
			}

			imgBase64 := base64.StdEncoding.EncodeToString(imgData)

			var count int
			err = db.QueryRow("SELECT COUNT(*) FROM tb_produk_gambar WHERE id_produk = $1 AND id_gambar = $2", idProduk, i).Scan(&count)
			if err != nil {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to check existing image: %v", err)
				return
			}

			if count > 0 {
				gambarData := map[string]string{
					"gambar": imgBase64,
				}
				where := map[string]string{
					"id_produk": idProduk,
					"id_gambar": strconv.Itoa(i),
				}
				status = config.Update(db, "tb_produk_gambar", gambarData, where)
			} else {
				gambarData := map[string]string{
					"id_produk": idProduk,
					"gambar":    imgBase64,
					"id_gambar": strconv.Itoa(i),
				}
				status = config.Insert(db, "tb_produk_gambar", gambarData)
			}

			if status != "OK" {
				http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
				log.Printf("Failed to insert/update data in tb_produk_gambar: %v", status)
				return
			}
		} else if err != http.ErrMissingFile {
			log.Printf("Error retrieving file %d: %v", i, err)
		}
	}

	where := map[string]string{"id_produk": idProduk}
	status = config.Delete(db, "tb_products_options", where)
	if status != "OK" {
		http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
		log.Printf("Failed to delete data from tb_products_options: %v", status)
		return
	}

	for variantCount := 1; variantCount <= 2; variantCount++ {
		variantType := r.FormValue(fmt.Sprintf("variant_type%d", variantCount))
		if variantType != "" {
			options := r.Form[fmt.Sprintf("variantOptions%d[]", variantCount)]
			for _, optionValue := range options {
				optionData := map[string]string{
					"id_produk":    idProduk,
					"option_name":  variantType,
					"option_value": optionValue,
				}
				status := config.Insert(db, "tb_products_options", optionData)
				if status != "OK" {
					http.Redirect(w, r, fmt.Sprintf("/telkomgoods/admin/editproduk?id=%s&notif=9", idProduk), http.StatusFound)
					log.Printf("Failed to insert data into tb_products_options: %v", status)
					return
				}
			}
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=5", http.StatusFound)
	log.Println("Product updated successfully")
}

func DeleteBulkProdukHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	idsParam := r.URL.Query().Get("id")
	if idsParam == "" {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=8", http.StatusFound)
		log.Printf("No product IDs provided for deletion")
		return
	}

	ids := strings.Split(idsParam, ",")
	if len(ids) == 0 {
		http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=8", http.StatusFound)
		log.Printf("No valid product IDs provided for deletion")
		return
	}

	for _, idProduk := range ids {
		log.Printf("Deleting Product ID: %v", idProduk)
		where := map[string]string{
			"id_produk": idProduk,
		}

		_, err := db.Exec("DELETE FROM tb_keranjang WHERE product_id = $1", idProduk)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
			log.Printf("Failed to delete from tb_keranjang: %v", err)
			return
		}

		rows, err := db.Query("SELECT gambar FROM tb_produk_gambar WHERE id_produk = $1", idProduk)
		if err != nil {
			http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
			log.Printf("Failed to retrieve product images: %v", err)
			return
		}
		defer rows.Close()

		for rows.Next() {
			var imgBase64 string
			if err := rows.Scan(&imgBase64); err != nil {
				log.Printf("Failed to scan image: %v", err)
				continue
			}

			imgData, err := base64.StdEncoding.DecodeString(imgBase64)
			if err != nil {
				log.Printf("Failed to decode base64 image: %v", err)
				continue
			}

			imgFilePath := fmt.Sprintf("assets/img/produk/%s", imgData)
			if err := os.Remove(imgFilePath); err != nil {
				log.Printf("Failed to remove image file %s: %v", imgFilePath, err)
			}
		}

		status := config.Delete(db, "tb_produk_gambar", where)
		log.Printf("Status Delete from tb_produk_gambar: %v", status)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
			return
		}

		status = config.Delete(db, "tb_products_options", where)
		log.Printf("Status Delete from tb_products_options: %v", status)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
			return
		}

		status = config.Delete(db, "tb_produk", where)
		log.Printf("Status Delete from tb_produk: %v", status)
		if status != "OK" {
			http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=9", http.StatusFound)
			return
		}
	}

	http.Redirect(w, r, "/telkomgoods/admin/myproduk?notif=1", http.StatusFound)
	log.Println("Selected products deleted successfully")
}

func AddHandlerKategori(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}

	err = r.ParseForm()
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}

	data := map[string]string{
		"nama_kategori": r.FormValue("nama_kategori"),
		"status":        r.FormValue("status"),
		"terbaru":       r.FormValue("terbaru"),
	}

	status := config.Insert(db, "tb_kategori_produk", data)
	log.Printf("Status : %v", status)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=4", http.StatusFound)
		return
	}

	http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=5", http.StatusFound)
}

func UpdateHandlerKategori(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}

	err = r.ParseForm()
	if err != nil {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}

	data := map[string]string{
		"nama_kategori": r.FormValue("nama_kategori"),
		"status":        r.FormValue("status"),
		"terbaru":       r.FormValue("terbaru"),
	}

	where := map[string]string{
		"id_kategori": r.FormValue("id_kategori"),
	}

	status := config.Update(db, "tb_kategori_produk", data, where)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}
	http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=2", http.StatusFound)
}

func DeleteHandlerKategori(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	cekid := r.URL.Query().Get("id")
	where := map[string]string{
		"id_kategori": cekid,
	}
	log.Printf("Get Id Kategori: %v", cekid)

	status := config.Delete(db, "tb_kategori_produk", where)
	log.Printf("Status Delete : %v", status)
	if status != "OK" {
		http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=6", http.StatusFound)
		return
	}
	http.Redirect(w, r, "/telkomgoods/admin/addkategori?notif=1", http.StatusFound)
}
