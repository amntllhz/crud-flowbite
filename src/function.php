<?php

// koneksi database
$conn = mysqli_connect("localhost", "root", "", "mahasiswa");

// function query
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data) {

    // ambil data form
    global $conn;
    $nim = htmlspecialchars($data["nim"]);
    $nama = htmlspecialchars($data["nama"]);
    $prodi = htmlspecialchars($data["prodi"]);
    $kelas = htmlspecialchars($data["kelas"]);
    $email = htmlspecialchars($data["email"]);

    // apakah data sudah ada di database
    $result = mysqli_query($conn, "SELECT * FROM datamhs WHERE nim = '$nim'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                alert('data sudah ada!');
                </script>";
        return false;
    }

    $gambar = upload();
    if (!$gambar) {
        return false;
    }

    // query insert
    $query =    "INSERT INTO datamhs VALUES
                ( '$nim', '$nama', '$prodi', '$kelas', '$email', '$gambar' )
                ";

    mysqli_query($conn, $query);

    // cek apakah data berhasil ditambahkan
    return mysqli_affected_rows($conn);

}


// function upload
function upload() {
    
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        echo "<script>
                alert('pilih gambar terlebih dahulu!');
                </script>";
        return false;
    }

    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>
                alert('File yang anda unggah bukan gambar!');
                </script>";
        return false;
    }

    // cek jika ukurannya terlalu besar
    if ($ukuranFile > 1000000) {
        echo "<script>
                alert('Ukuran gambar terlalu besar!');
                </script>";
        return false;
    }


    // lolos pengecekan, gambar siap diupload
    // generate nama gambar baru
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    // lolos pengecekan, gambar siap diupload
    move_uploaded_file($tmpName, '../img/' . $namaFileBaru);

    return $namaFileBaru;
}



// function hapus
function hapus($dataNim) {

    // ambil data
    global $conn;
    $nim = htmlspecialchars($dataNim["nim"]);
    $gambar = htmlspecialchars($dataNim["gambar"]);
    $file = "../img/" . $gambar;

    // Hapus gambar hanya jika data berhasil dihapus
    if (file_exists($file)) {
        unlink($file);
    } else {
        echo "
                <script>
                     alert('data gagal dihapus!');
                </script>";

        return false;        
    }

    $query = "DELETE FROM datamhs WHERE nim = '$nim'";
    mysqli_query($conn, $query);

    // cek apakah data berhasil dihapus
    return mysqli_affected_rows($conn);

}


//function update
function update($dataUp) {

    // ambil data form
    global $conn;
    $nim = htmlspecialchars($dataUp["nim"]);
    $nama = htmlspecialchars($dataUp["nama"]);
    $prodi = htmlspecialchars($dataUp["prodi"]);
    $kelas = htmlspecialchars($dataUp["kelas"]);
    $email = htmlspecialchars($dataUp["email"]);
    $gambarLama = htmlspecialchars($dataUp["gambarLama"]);

    // cek apakah user pilih gambar baru
    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();

        // hapus gambar lama
        $file = "../img/" . $gambarLama;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // query insert
    $query =    "UPDATE datamhs SET
                    nim = '$nim',
                    nama = '$nama',
                    prodi = '$prodi',
                    kelas = '$kelas',
                    email = '$email',
                    gambar = '$gambar'
                WHERE nim = '$nim'
                ";

    mysqli_query($conn, $query);

    // cek apakah data berhasil diupdate
    return mysqli_affected_rows($conn);

}


// filter data
function filter($dataFilter) {
    if ($dataFilter['kelas'] === 'All') {
        $query = "SELECT * FROM datamhs"; // Ambil semua data
    } else {
        $query = "SELECT * FROM datamhs WHERE kelas = '" . htmlspecialchars($dataFilter['kelas']) . "'";
    }
    return query($query);
}

// search data
function search($dataSearch) {
    $query = "SELECT * FROM datamhs WHERE

    nim LIKE '%" . htmlspecialchars($dataSearch['search']) . "%' OR
    nama LIKE '%" . htmlspecialchars($dataSearch['search']) . "%' OR
    prodi LIKE '%" . htmlspecialchars($dataSearch['search']) . "%'
        
     ";


    // return query
    return query($query);
}

// function register
function register($dataReg) {

    global $conn;
    $username = strtolower(stripslashes($dataReg['username']));
    $email = htmlspecialchars($dataReg['email']);
    $password = mysqli_real_escape_string($conn, $dataReg['password']);
    $password2 = mysqli_real_escape_string($conn, $dataReg['passwordVer']);

    // cek username
    $result = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                alert('username sudah terdaftar!');
                </script>";

        return false;
    }

    // cek konfirmasi password  
    if ($password !== $password2) {
        echo "<script>
                alert('konfirmasi password tidak sesuai!');
                </script>";

        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan user baru ke database
    mysqli_query($conn, "INSERT INTO users VALUES('$username','$email','$password')");

    // cek apakah data berhasil ditambahkan
    return mysqli_affected_rows($conn);
}   

?>