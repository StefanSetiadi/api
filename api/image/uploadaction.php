<?php
    if(isset($_FILES['foto'])){
        // data biasa
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];

        // data gambar
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];

        // ubah gambar string
        $data_gambar = file_get_contents($file_tmp);
        $data_parts = pathinfo($file_name);
        $data_extension = $data_parts['extension'];
        // ubah gambar menjadi string
        $gambar_base64 = base64_encode($data_gambar);

        // var_dump($gambar_base64);die;

        $inputPost = array(
            'nim' => $nim,
            'nama' => $nama,
            'alamat' => $alamat,
            'stringfoto' => $gambar_base64,
            'extfoto' => $data_extension
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost/api/api/image/upload.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $inputPost
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err){
            echo $err;
        } else {
            echo $response;
        }

    }
?>