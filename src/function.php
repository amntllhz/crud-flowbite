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

    // query insert
    $query =    "INSERT INTO datamhs VALUES
                ( '$nim', '$nama', '$prodi', '$kelas', '$email')
                ";

    mysqli_query($conn, $query);

    // cek apakah data berhasil ditambahkan
    return mysqli_affected_rows($conn);

}

// function hapus
function hapus($dataNim) {

    // ambil data
    global $conn;
    $nim = htmlspecialchars($dataNim["nim"]);

    $query = "DELETE FROM datamhs WHERE nim = '$nim'";
    mysqli_query($conn, $query);

    // cek apakah data berhasil dihapus
    return mysqli_affected_rows($conn);
}


// update
function update($dataUp) {

    // ambil data form
    global $conn;
    $nim = htmlspecialchars($dataUp["nim"]);
    $nama = htmlspecialchars($dataUp["nama"]);
    $prodi = htmlspecialchars($dataUp["prodi"]);
    $kelas = htmlspecialchars($dataUp["kelas"]);
    $email = htmlspecialchars($dataUp["email"]);

    // query insert
    $query =    "UPDATE datamhs SET
                    nim = '$nim',
                    nama = '$nama',
                    prodi = '$prodi',
                    kelas = '$kelas',
                    email = '$email'
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


?>