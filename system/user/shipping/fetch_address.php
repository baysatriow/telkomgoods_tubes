<?php
require('config/database.php');

if (isset($_POST['id_alamat'])) {
    $id_alamat = pg_escape_string($_POST['id_alamat']);

    // Fetch the selected address details
    $address_query = pg_query($conn, "SELECT * FROM tb_alamat WHERE id_alamat = '$id_alamat'");
    $address = pg_fetch_assoc($address_query);

    if ($address) {
        echo "
            <div>
                <strong>Rumah: </strong>{$address['nama_penerima']}<br>
                {$address['alamat']}<br>
                {$address['kota']}, {$address['provinsi']}<br>
                {$address['kode_pos']}<br>
                {$address['nohp_penerima']}<br>
            </div>
            <button type='button' class='btn btn-secondary mt-3' data-toggle='modal' data-target='#addressModal'>Ganti Alamat</button>
        ";
    } else {
        echo "<p>Alamat tidak ditemukan.</p>";
    }
}
?>
