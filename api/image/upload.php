<?php
    // header hasil berbentuk json
    header("Content-Type:application/json");

    // tangkap metode akses
    $method = $_SERVER['REQUEST_METHOD'];

    // variable hasil
    $result = array();

    // cek metode
    if($method=='POST'){

        // pengecekan parameter
        if(isset($_POST['nim']) AND isset($_POST['nama']) AND isset($_POST['alamat'])){
            // tangkap parameter
            $nim = $_POST['nim'];
            $nama = $_POST['nama'];
            $alamat = $_POST['alamat'];

            // tangkap foto
            $stringfoto = $_POST['stringfoto']; // foto dalam bentuk string
            $extfoto = $_POST['extfoto'];
            // string foto kita ubah jadi gambar
            $foto = base64_decode($stringfoto);
            // simpan gambar hasil decode base64
            file_put_contents('foto/'.$nim.'.'.$extfoto, $foto);
            // membuat nama foto
            $nama_foto = $nim.'.'.$extfoto;

            // jika metode sesuai
            $result['status'] = [
                "code" => 200,
                "description" => '1 Data Inserted'
            ];

            // // S:koneksi database
            // $servername = "localhost";
            // $username = "root";
            // $password = "";
            // $dbname = "apitubes";
            // // Create connection
            // $conn = new mysqli($servername, $username, $password, $dbname);

            // // buat query
            // $sql = "INSERT INTO image (foto) VALUES ('$nama_foto')";

            // // eksekusi query
            // $conn->query(sql);

        }
    }


?>