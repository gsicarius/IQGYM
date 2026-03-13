<?php
// Este archivo se incluye desde client/index.php cuando se debe mostrar el formulario de cliente.
// Variables esperadas: $cliente_seleccionado
?>

<section class="bg-gray-800 rounded-lg p-6 max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="index.php" class="text-blue-400 hover:text-blue-300 flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Regresar a la lista
        </a>
    </div>

    <h2 class="text-white text-2xl font-semibold text-center mb-6">
        <?= $cliente_seleccionado ? 'Editar Cliente' : 'Nuevo Cliente' ?>
    </h2>

    <form method="POST" action="index.php">
        <div class="flex flex-col items-center">
            <div class="mb-6">
                <img src="../assets/IMAGES/logo.png" alt="FotoCliente" class="w-40 h-40 object-contain drop-shadow-2xl">
            </div>

            <div class="flex gap-4 w-full max-w-2xl mb-4">
                <div class="flex-1">
                    <h3 class="mb-2 text-white">Nombre:</h3>
                    <input type="text" name="txtnombre" id="txtnombre"
                        value="<?= htmlspecialchars($cliente_seleccionado['nombre'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4" required>

                    <h3 class="mb-2 text-white">Telefono:</h3>
                    <input type="text" name="txttelefono" id="txttelefono"
                        value="<?= htmlspecialchars($cliente_seleccionado['telefono'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" required>
                </div>
                <div class="flex-1">
                    <h3 class="mb-2 text-white">Apellido:</h3>
                    <input type="text" name="txtapellido" id="txtapellido"
                        value="<?= htmlspecialchars($cliente_seleccionado['apellido'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4" required>

                    <h3 class="mb-2 text-white">Email:</h3>
                    <input type="email" name="txtemail" id="txtemail"
                        value="<?= htmlspecialchars($cliente_seleccionado['email'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" required>
                </div>
            </div>

            <?php if ($cliente_seleccionado && $cliente_seleccionado['fecha_vencimiento']): ?>
                <div class="w-full max-w-2xl mb-4 p-4 bg-gray-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">
                            <i class="fa-solid fa-calendar-alt mr-2"></i>
                            Fecha de vencimiento:
                        </span>
                        <span class="text-white font-semibold">
                            <?= date('d/m/Y', strtotime($cliente_seleccionado['fecha_vencimiento'])) ?>
                        </span>
                    </div>
                    <p class="text-gray-400 text-xs mt-2">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        La fecha se actualiza automáticamente al registrar un pago
                    </p>
                </div>
            <?php endif; ?>

            <input type="hidden" name="idActual" value="<?= $cliente_seleccionado['id_cliente'] ?? '' ?>">

            <div class="flex gap-4 w-full max-w-2xl">
                <button type="submit" name="accion" value="guardar"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                    text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    <i class="fa-solid fa-save mr-2"></i>
                    <?= $cliente_seleccionado ? 'Actualizar' : 'Guardar' ?>
                </button>

                <?php if ($cliente_seleccionado): ?>
                    <button type="submit" name="accion" value="eliminar"
                        onclick="return confirm('¿Eliminar este cliente?')"
                        class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 
                        text-white font-semibold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        <i class="fa-solid fa-trash mr-2"></i> Eliminar
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</section>
