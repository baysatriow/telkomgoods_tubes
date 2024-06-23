<?php

require("../config/function_whatsapp.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image = file_get_contents($_FILES['image']['tmp_name']); // Get the uploaded image
    $image_base64 = base64_encode($image); // Convert the image to base64

    $product = array(
        'id' => $_POST['id'],
        'name' => $_POST['name'],
        'price' => (float) $_POST['price'],
        'description' => $_POST['description'],
        'image' => $image_base64 // Add the base64 image to the product array
    );
    $data_string = json_encode($product);
    
    $ch = curl_init('http://localhost:8080/api/products');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $result = curl_exec($ch);
    $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $pesanSukses = 
                            "*Selamat datang di TelkomGoods!* 🎉\n\n" .
                            "Kami sangat senang Anda telah bergabung dengan kami. Di sini, Anda dapat menemukan berbagai produk yang Anda butuhkan, dari elektronik hingga pakaian, semua dalam satu tempat.\n\n" . 
                            "Untuk memulai, silakan jelajahi katalog kami atau gunakan fitur pencarian untuk menemukan apa yang Anda cari. Jika Anda memerlukan bantuan atau memiliki pertanyaan, tim dukungan pelanggan kami siap membantu Anda.\n\n" .
                            "Terima kasih telah memilih TelkomGoods. Selamat berbelanja!\n\n" .
                            "Salam hangat,\n\n" .
                            "Tim TelkomGoods!!! ";
    if ($response == 201) {
        header('Location: index.php'); // Redirect back to the index page on success
        send('081377754080',$pesanSukses);
    } else {
        echo "Failed to add product";
    }
} else {
    header('Location: index.php'); // Redirect back if the method is not POST
}

?>
