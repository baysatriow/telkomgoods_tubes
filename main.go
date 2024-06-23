package main

import (
	"log"
	"net/http"
	"telkomgoods/home/forgot_password_proses"
	"telkomgoods/home/login_proses"
	"telkomgoods/home/register_proses"
	"telkomgoods/system/admin/mod_akun"
	"telkomgoods/system/admin/mod_banner_promo"
	"telkomgoods/system/admin/mod_produk"
	"telkomgoods/system/admin/mod_setting"
	"telkomgoods/system/admin/mod_user"
	"telkomgoods/system/user/cart"
	"telkomgoods/system/user/mod_alamat"
)

func main() {
	// REegist PAge
	http.HandleFunc("/registerproses", register_proses.RegisterUserHandler)
	http.HandleFunc("/confirmotpreg", register_proses.OTPHandler)
	http.HandleFunc("/resendotpreg", register_proses.ResendOTPHandler)

	// Login Page
	http.HandleFunc("/loginproses", login_proses.LoginHandler)
	http.HandleFunc("/confirmotp", login_proses.OTPHandler)
	http.HandleFunc("/resendotp", login_proses.ResendOTPHandler)

	// Forgot Password Page
	http.HandleFunc("/forgototp", forgot_password_proses.SendOTPHandler)
	http.HandleFunc("/confirmotpfp", forgot_password_proses.ConfirmOTPHandler)
	http.HandleFunc("/resendotpfp", forgot_password_proses.ResendOTPHandler)
	http.HandleFunc("/respw", forgot_password_proses.ResetPasswordHandler)

	// Produk Page
	http.HandleFunc("/addproduks", mod_produk.AddProdukHandler)
	http.HandleFunc("/editproduks", mod_produk.UpdateProdukHandler)
	http.HandleFunc("/delproduks", mod_produk.DeleteBulkProdukHandler)
	http.HandleFunc("/delproduksbulk", mod_produk.DeleteBulkProdukHandler)
	http.HandleFunc("/fetchproduk", mod_produk.FetchProdukHandler)

	// Banner Promo Page
	http.HandleFunc("/addbannerpromo", mod_banner_promo.AddPromoHandler)
	http.HandleFunc("/editbannerpromo", mod_banner_promo.UpdatePromoHandler)
	http.HandleFunc("/delbannerpromo", mod_banner_promo.DeletePromoHandler)

	// Cart Proses
	http.HandleFunc("/addcart", cart.AddToCartHandler)

	// Alamat Page
	http.HandleFunc("/addalamat", mod_alamat.AddAddressHandler)
	http.HandleFunc("/editalamat", mod_alamat.UpdateAddressHandler)
	http.HandleFunc("/delalamat", mod_alamat.DeleteAddressHandler)

	// Kategori Page
	http.HandleFunc("/addkategori", mod_produk.AddHandlerKategori)
	http.HandleFunc("/editkategori", mod_produk.UpdateHandlerKategori)
	http.HandleFunc("/delkategori", mod_produk.DeleteHandlerKategori)

	// User Control Page
	http.HandleFunc("/adduser", mod_user.AddUserHandler)
	http.HandleFunc("/edituser", mod_user.UpdateUserHandler)
	http.HandleFunc("/deluser", mod_user.DeleteUserHandler)

	// Akun Page
	http.HandleFunc("/updateakun", mod_akun.UpdateHandlerAkun)
	http.HandleFunc("/updateakunpw", mod_akun.UpdateHandlerAkunPw)
	http.HandleFunc("/updatesetting", mod_setting.UpdateHandlerSetting)
	log.Println("API server Berjalan Pada http://localhost:8080/ Vhost Apache")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
