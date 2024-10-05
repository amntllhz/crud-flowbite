<?php

// session start
session_start();

// require
require 'function.php';

// cek cookie
if (isset($_COOKIE['key'])) {

    $key = $_COOKIE['key'];        

    // ambil username berdasarkan key
    $result = mysqli_query($conn, "SELECT username FROM users");
    $row = mysqli_fetch_assoc($result);

    // cek apakah cookie dan username cocok
    if ($key === hash('sha256', $row['username'])) {
        $_SESSION['login'] = true;
    }
    
}


// cek apakah user sudah login
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}


// ambil data dari form
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // cek username
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($result) === 1) {

        // cek password
        $row = mysqli_fetch_assoc($result);    

        if (password_verify($password, $row['password'])) {            

            // session
            $_SESSION['login'] = true;

            // cek remember me
            if (isset($_POST['remember'])) {

                // buat cookie
                setcookie('key', hash('sha256', $row['username']), time() + 60);
            }
            
            header("Location: index.php");
            exit;
        }
    }

    $error = true;

    // jika error
    if ($error) {
        echo "<script>
                    alert('Username / Password salah!');                    
                </script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="output.css">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
</head>
<body class="font-in font-feature-settings-cv11">
    <section class="max-w-md mt-16 p-6 mx-auto bg-white rounded-md shadow-md dark:bg-gray-800">
        <div class="">
            <h2 class="text-2xl text-center mb-12 font-semibold text-gray-700 capitalize dark:text-white">Login with your Account</h2>
            <form action="" method="post" class="max-w-sm mx-auto mb-4">                
                <div class="mb-5">
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                    <input name="username" type="text" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your Username" required />
                </div>               
                <div class="mb-5">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <input name="password" type="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                </div>              
                <div class="flex items-start mb-5">
                    <div class="flex items-center h-5">
                    <input name="remember" id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Remember Me</label>
                </div>
                <button name="login" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
            </form>
            <p class="text-sm text-center text-gray-500 dark:text-gray-300">Not registered? <a href="reg.php" class="text-blue-700 hover:underline dark:text-blue-500">Create account</a></p>
        </div>
    </section>

    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
</body>
</html>