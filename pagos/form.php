<?php
// Este archivo se incluye desde pagos/index.php cuando se debe mostrar el formulario de pago.
// Variables esperadas: $cliente_seleccionado, $planes
?>

<section class="bg-gray-800 rounded-lg p-6 max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="index.php" class="text-blue-400 hover:text-blue-300 flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Regresar a la lista
        </a>
    </div>

    <h2 class="text-white text-2xl font-semibold text-center mb-6">
        <?= $cliente_seleccionado
            ? 'Formulario de Pago <br><br> ' . htmlspecialchars($cliente_seleccionado['nombre'] . ' ' . $cliente_seleccionado['apellido'])
            : 'Registrar Pago' ?>
    </h2>

    <form method="POST" action="index.php">
        <div class="flex flex-col md:flex-row gap-6 items-center">

            <div class="flex flex-col items-center justify-center min-w-[160px]">
                <img src="../assets/IMAGES/logo.png" alt="FotoCliente"
                    class="w-40 h-40 object-contain drop-shadow-2xl">
            </div>

            <div class="flex flex-col md:flex-row gap-4 w-full max-w-2xl mb-4">
                <div class="flex-1">
                    <h3 class="mb-2 text-white">Nombre:</h3>
                    <input type="text" name="txtnombre" id="txtnombre"
                        value="<?= htmlspecialchars($cliente_seleccionado['nombre'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4" required>

                    <h3 class="mb-2 text-white">Teléfono:</h3>
                    <input type="text" name="txttelefono" id="txttelefono"
                        value="<?= htmlspecialchars($cliente_seleccionado['telefono'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" required>
                </div>

                <div class="flex-1">
                    <h3 class="mb-2 text-white">Apellido:</h3>
                    <input type="text" name="txtapellido" id="txtapellido"
                        value="<?= htmlspecialchars($cliente_seleccionado['apellido'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-4" required>

                    <h3 class="mb-2 text-white">Email:</h3>
                    <input type="email" name="txtemail" id="txtemail"
                        value="<?= htmlspecialchars($cliente_seleccionado['email'] ?? '') ?>" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" required>
                </div>

                <div class="flex-1 mt-2">
                    <h3 class="mb-2 text-white">Vencimiento:</h3>
                    <?php if ($cliente_seleccionado && $cliente_seleccionado['fecha_vencimiento']): ?>
                        <?php
                        $dias_restantes = floor((strtotime($cliente_seleccionado['fecha_vencimiento']) - time()) / 86400);
                        $color = $dias_restantes < 0 ? 'text-red-400' : ($dias_restantes <= 7 ? 'text-yellow-400' : 'text-gray-300');
                        ?>
                        <span class="<?= $color ?>">
                            <?= date('d/m/Y', strtotime($cliente_seleccionado['fecha_vencimiento'])) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-gray-500">Sin plan</span>
                    <?php endif; ?>

                    <?php if ($cliente_seleccionado && $cliente_seleccionado['nombre_plan']): ?>
                        <h3 class="mb-2 mt-4 text-white">Plan actual:</h3>
                        <span class="text-blue-400"><?= htmlspecialchars($cliente_seleccionado['nombre_plan']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" name="idActual" value="<?= $cliente_seleccionado['id_cliente'] ?? '' ?>">
        </div>

        <!-- SELECT DE PLANES -->
        <div>
            <select name="plan" id="plan" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mb-3">
                <option value="">Seleccionar Plan</option>
                <?php foreach ($planes as $p): ?>
                    <option value="<?= $p['id_plan'] ?>" data-precio="<?= $p['precio'] ?>"
                        data-duracion="<?= $p['duracion_dias'] ?>"
                        <?= ($cliente_seleccionado['id_plan_actual'] == $p['id_plan']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_plan']) ?> - $<?= number_format($p['precio'], 2) ?>
                        (<?= $p['duracion_dias'] ?> días)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- FORMATO PARA PAGOS -->
        <div>
            <h2 class="text-center text-white font-semibold text-xl mb-3">
                Total a Pagar: $<span id="total_pagar">
                    <?= $cliente_seleccionado['precio'] ? number_format($cliente_seleccionado['precio'], 2) : '0.00' ?>
                </span>
            </h2>

            <select name="formato_pago" id="formato_pago" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition">
                <option value="opciones">Método de pago</option>
                <option value="tarjetaC">Tarjeta de Crédito</option>
                <option value="tarjetaD">Tarjeta de Débito</option>
                <option value="efectivo">Efectivo</option>
            </select>

            <div id="credito" class="mt-3">
                <input type="number" name="NumeroTarjeta" id="NumeroTarjeta" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" placeholder="Número de la tarjeta">
                <div class="flex mt-3">
                    <input type="number" name="CVV" id="CVV" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition mr-2" placeholder="CVV">
                    <input type="date" name="FechaVencimiento" id="FechaVencimiento" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition">
                </div>
            </div>

            <div id="efectivo">
                <label class="text-semibold text-white">Ingreso:</label>
                <input type="number" name="TotalIngreso" id="TotalIngreso" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg 
                        text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 
                        focus:border-transparent transition" placeholder="Total Ingresado">
                <p class="text-white text-semibold mt-2">Cambio: $<span id="cambio">0.00</span></p>
            </div>

            <div class="flex justify-center mt-4" id="btn_pago">
                <button type="submit" name="accion" value="procesar_pago"
                    class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                        text-white font-semibold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    <i class="fa-solid fa-save mr-2"></i>
                    Procesar Pago
                </button>
            </div>
        </div>

    </form>
</section>

<script>
    const credito = document.getElementById('credito')
    const efectivo = document.getElementById('efectivo')
    const metodos_pago = document.getElementById('formato_pago')
    const btn_pago = document.getElementById('btn_pago')
    const select_plan = document.getElementById('plan')
    const total_pagar = document.getElementById('total_pagar')
    const input_ingreso = document.getElementById('TotalIngreso')
    const span_cambio = document.getElementById('cambio')

    input_ingreso.addEventListener('input', function () {
        const total = parseFloat(total_pagar.textContent) || 0
        const ingreso = parseFloat(this.value) || 0
        const cambio = ingreso - total
        span_cambio.textContent = cambio >= 0 ? cambio.toFixed(2) : '0.00'
    })

    // ── Estado inicial: ocultar todo ──────────────────────────────────────
    window.addEventListener('DOMContentLoaded', () => {
        credito.classList.add('hidden')
        efectivo.classList.add('hidden')
        btn_pago.classList.add('hidden')
    })

    // ── Actualizar total cuando cambia el plan ────────────────────────────
    if (select_plan) {
        select_plan.addEventListener('change', function () {
            const opcion = this.options[this.selectedIndex]
            const precio = opcion.getAttribute('data-precio') ?? '0.00'
            total_pagar.textContent = parseFloat(precio).toFixed(2)
        })
    }

    // ── Mostrar/ocultar sección según método de pago ──────────────────────
    function mostrarFormularioPago(opc) {
        credito.classList.add('hidden')
        efectivo.classList.add('hidden')

        if (opc === 'opciones') {
            btn_pago.classList.add('hidden')
        } else if (opc === 'tarjetaC' || opc === 'tarjetaD') {
            credito.classList.remove('hidden')
            btn_pago.classList.remove('hidden')
        } else if (opc === 'efectivo') {
            efectivo.classList.remove('hidden')
            btn_pago.classList.remove('hidden')
        }
    }

    if (metodos_pago) {
        metodos_pago.addEventListener('change', function () {
            mostrarFormularioPago(metodos_pago.value)
        })
    }
</script>
