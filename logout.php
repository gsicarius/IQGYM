<?php
session_start();
session_destroy();
header('Location: index.php?msg=Sesión cerrada correctamente');
exit;
?>