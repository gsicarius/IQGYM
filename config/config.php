<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gray-900">
    <?php include '../includes/sidebar.php'; ?>

    <div class="lg:ml-64">
        <header>
            <h1 class="font-semibold text-white text-3xl ml-3 text-center">
                Configuración
            </h1>
        </header>

        <main>

            <section class="flex flex-wrap">
                <div class="flex items-center justify-between w-full px-6 py-3 bg-gray-800 mt-3">
                    <h2 class="font-semibold text-white text-xl">
                        <i class="fa-solid fa-users mr-2"></i> Gestion de usuarios
                    </h2>
                    <button onclick="toggleContent()" class="text-white text-xl hover:text-blue-500 transition-all">
                        <i class="fa-solid fa-chevron-right transition-transform duration-300" id="arrow"></i>
                    </button>
                </div>

                <!-- Contenido desplegable -->
                <!-- Contenido desplegable -->
<div id="content" class="w-full flex justify-center">
    <div class="w-full max-w-4xl px-6">
        <hr class="border-t-2 border-gray-700 w-full my-4">
        <div class="text-white pb-4">
            <h2 class="mb-2">Usuarios Registrados</h2>
            <select name="usurios" id="usuariosCheackbox" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                focus:border-transparent transition mb-4">
                <!-- datos dinamicos-->
            </select>

            <div class="flex flex-col items-center justify-center">

                <div class="mb-4">
                    <img src="../assets/IMAGES/logo.png" alt="FotoUsuario" class="w-40 h-40 object-contain drop-shadow-2xl">
                </div>

                <div class="flex gap-4 w-full max-w-2xl mb-4">
                    <div class="flex-1">
                        <h3 class="mb-2">Nombre:</h3>
                        <input type="text" id="txtnombre" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4">

                        <h3 class="mb-2">Telefono:</h3>
                        <input type="text" id="txttelefono" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition">
                    </div>

                    <div class="flex-1">
                        <h3 class="mb-2">Apellido:</h3>
                        <input type="text" id="txtapellido" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4">

                        <h3 class="mb-2">Email:</h3>
                        <input type="text" id="txtemail" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition">
                    </div>
                </div>
                
                <button type="submit"
                    class="w-full max-w-2xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                    text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    Ingresar
                </button>
            </div>
        </div>
    </div>
</div>
            </section>

        </main>

    </div>

    <!-- Overlay para móviles -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    <script src="../assets/js/sidebar.js"></script>

    <script>
        function toggleContent() {
            const content = document.getElementById('content');
            const arrow = document.getElementById('arrow');

            content.classList.toggle('hidden');
            arrow.classList.toggle('rotate-90');
        }
    </script>


</body>

</html>