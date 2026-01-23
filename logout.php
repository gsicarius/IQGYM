<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también se debe borrar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al login con mensaje
header('Location: index.php?msg=Sesión cerrada correctamente');
exit;
?>