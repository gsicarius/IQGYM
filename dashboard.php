<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IQGYM</title>
</head>
<body>
    <div>
        <div>
            <h1>¡Bienvenido!</h1>
            <p>
                <strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </p>
            <p>
                <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?>
            </p>
            <p>
                <strong>Rol:</strong> 
                <span>
                    <?php echo ucfirst($_SESSION['rol']); ?>
                </span>
            </p>
            
            <a href="logout.php">
                Cerrar Sesión
            </a>
        </div>
    </div>
</body>
</html>