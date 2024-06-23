package mod_alamat

import (
	"log"
	"net/http"
	"strconv"

	"telkomgoods/config"
)

// Handler untuk menambah alamat
func AddAddressHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/user/addresses?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	idUser, err := strconv.Atoi(r.FormValue("id_user"))
	if err != nil {
		http.Redirect(w, r, "/user/addresses?notif=7", http.StatusFound)
		log.Printf("Invalid user ID: %v", err)
		return
	}

	var count int
	err = db.QueryRow("SELECT COUNT(*) FROM tb_alamat WHERE id_user = $1", idUser).Scan(&count)
	if err != nil {
		http.Redirect(w, r, "/user/addresses?notif=6", http.StatusFound)
		log.Printf("Failed to count addresses: %v", err)
		return
	}

	if count >= 3 {
		http.Redirect(w, r, "/user/addresses?notif=8", http.StatusFound)
		log.Println("User already has 3 addresses")
		return
	}

	data := map[string]string{
		"id_user":       strconv.Itoa(idUser),
		"alamat":        r.FormValue("alamat"),
		"kota":          r.FormValue("kota"),
		"provinsi":      r.FormValue("provinsi"),
		"kode_pos":      r.FormValue("kode_pos"),
		"label":         r.FormValue("label"),
		"nama_penerima": r.FormValue("nama_penerima"),
		"nohp_penerima": r.FormValue("nohp_penerima"),
	}

	status := config.Insert(db, "tb_alamat", data)
	if status != "OK" {
		http.Redirect(w, r, "/user/addresses?notif=3", http.StatusFound)
		log.Printf("Failed to add address: %v", status)
		return
	}

	http.Redirect(w, r, "/user/addresses?notif=5", http.StatusFound)
	log.Println("Address added successfully")
}

// Handler untuk memperbarui alamat
func UpdateAddressHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodPost {
		http.Redirect(w, r, "/user/addresses?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	idAlamat, err := strconv.Atoi(r.FormValue("id_alamat"))
	if err != nil {
		http.Redirect(w, r, "/user/addresses?notif=7", http.StatusFound)
		log.Printf("Invalid address ID: %v", err)
		return
	}

	data := map[string]string{
		"alamat":        r.FormValue("alamat"),
		"kota":          r.FormValue("kota"),
		"provinsi":      r.FormValue("provinsi"),
		"kode_pos":      r.FormValue("kode_pos"),
		"label":         r.FormValue("label"),
		"nama_penerima": r.FormValue("nama_penerima"),
		"nohp_penerima": r.FormValue("nohp_penerima"),
	}
	where := map[string]string{
		"id_alamat": strconv.Itoa(idAlamat),
	}

	log.Println("Updating address data in the database...")
	status := config.Update(db, "tb_alamat", data, where)
	if status != "OK" {
		http.Redirect(w, r, "/user/addresses?notif=3", http.StatusFound)
		log.Printf("Failed to update address: %v", status)
		return
	}

	http.Redirect(w, r, "/user/addresses?notif=5", http.StatusFound)
	log.Println("Address updated successfully")
}

// Handler untuk menghapus alamat
func DeleteAddressHandler(w http.ResponseWriter, r *http.Request) {
	db, err := config.ConnectDB()
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if r.Method != http.MethodGet {
		http.Redirect(w, r, "/user/addresses?notif=6", http.StatusFound)
		log.Printf("Invalid request method: %s", r.Method)
		return
	}

	idAlamat, err := strconv.Atoi(r.URL.Query().Get("id"))
	if err != nil {
		http.Redirect(w, r, "/user/addresses?notif=7", http.StatusFound)
		log.Printf("Invalid address ID: %v", err)
		return
	}

	_, err = db.Exec("DELETE FROM tb_alamat WHERE id_alamat = $1", idAlamat)
	if err != nil {
		http.Redirect(w, r, "/user/addresses?notif=3", http.StatusFound)
		log.Printf("Failed to delete address: %v", err)
		return
	}

	http.Redirect(w, r, "/user/addresses?notif=5", http.StatusFound)
	log.Println("Address deleted successfully")
}
