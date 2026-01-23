<?php

$server = "localhost";
$user = "root";
$password = "";
$database = "iqgym";
$port = "3307";

// Cambié $coneccion por $conn para que coincida con el resto del código
$conn = mysqli_connect($server, $user, $password, $database, $port);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");

?>