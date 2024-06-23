<?php

// Server Online Telkom Goods
$host     = "localhost";
$port     = "5432"; // Port default PostgreSQL
$dbname   = "telkomgoods";
$username = "postgres";
$password = "bayu99";

// koneksi database
$conn_string = "host=$host port=$port dbname=$dbname user=$username password=$password";
$conn = pg_connect($conn_string);

// cek koneksi
if (!$conn) {
    die('Koneksi Database Gagal : ');
} else {
    // echo "database terhubung";
}


// SETTING WAKTU
date_default_timezone_set("Asia/Jakarta");

$uri = "http://localhost/telkomgoods";

define('BASEPATH', str_replace("config", "", dirname(__FILE__)));
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
