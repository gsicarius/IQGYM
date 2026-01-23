
<?php

$server = "sql5.freesqldatabase.com";
$user = "sql5814300";
$password = "KcRts6F5MF";
$database = "sql5814300";
$port = "3306";

$coneccion = mysqli_connect($server, $user, $password, $database, $port);
if (!$coneccion) {
    die("Connection failed: " . mysqli_connect_error());
}else{
    
}

?>