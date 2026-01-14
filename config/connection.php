
<?php

include ('config.php');

$coneccion = mysqli_connect($server, $user, $password, $database, $port);
if (!$coneccion) {
    die("Connection failed: " . mysqli_connect_error());
}else{
    
}

?>