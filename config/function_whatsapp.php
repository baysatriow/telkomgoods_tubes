<?php 
    function send($nomor, $msg)
    {   
        include 'database.php';
        $result = pg_query($conn, "SELECT * FROM tb_settings WHERE id_setting = 1");
        $api = pg_fetch_array($result);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.fonnte.com/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('target' => $nomor,'message' => $msg),
          CURLOPT_HTTPHEADER => array(
            'Authorization: '.$api['api_wa']
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    // function picture($nomor, $msg, $url)
    // {
    //     $api = \DB::table('setting_webs')->where('id',1)->first();
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //       CURLOPT_URL => 'https://api.fonnte.com/send',
    //       CURLOPT_RETURNTRANSFER => true,
    //       CURLOPT_ENCODING => '',
    //       CURLOPT_MAXREDIRS => 10,
    //       CURLOPT_TIMEOUT => 0,
    //       CURLOPT_FOLLOWLOCATION => true,
    //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //       CURLOPT_CUSTOMREQUEST => 'POST',
    //       CURLOPT_POSTFIELDS => array('target' => $nomor,'message' => $msg, 'url' => $url),
    //       CURLOPT_HTTPHEADER => array(
    //         'Authorization: '.$api->wa_key
    //       ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return $response;
    // }
    // function qr()
    // {
    //     $api = \DB::table('setting_webs')->where('id',1)->first();
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //       CURLOPT_URL => 'https://api.fonnte.com/qr',
    //       CURLOPT_RETURNTRANSFER => true,
    //       CURLOPT_ENCODING => '',
    //       CURLOPT_MAXREDIRS => 10,
    //       CURLOPT_TIMEOUT => 0,
    //       CURLOPT_FOLLOWLOCATION => true,
    //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //       CURLOPT_CUSTOMREQUEST => 'POST',
    //       CURLOPT_HTTPHEADER => array(
    //         'Authorization: '.$api->wa_key
    //       ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return $response;
    // }
?>