<?php
// Este archivo se incluye desde planes/index.php cuando se debe mostrar el formulario.
// Variables esperadas: $plan_seleccionado
?>

<section class="bg-gray-800 rounded-xl p-8 max-w-2xl mx-auto border border-gray-700">

    <div class="mb-6">
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Regresar
        </a>
    </div>

    <h2 class="text-white text-2xl font-semibold mb-8">
        <?= $plan_seleccionado ? 'Editar Plan' : 'Nuevo Plan' ?>
    </h2>

    <form method="POST" action="index.php">

        <div class="grid grid-cols-2 gap-x-6 gap-y-5 mb-8">

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Nombre</label>
                <input type="text" name="txtnombre" id="txtnombre"
                    value="<?= htmlspecialchars($plan_seleccionado['nombre_plan'] ?? '') ?>"
                    placeholder="Ej. Plan Mensual"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Descripción</label>
                <input type="text" name="txtdescripcion" id="txtdescripcion"
                    value="<?= htmlspecialchars($plan_seleccionado['descripcion'] ?? '') ?>"
                    placeholder="Breve descripción"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Precio</label>
                <input type="number" step="0.01" min="0" name="precio" id="precio"
                    value="<?= htmlspecialchars($plan_seleccionado['precio'] ?? '') ?>"
                    placeholder="0.00"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Duración (días)</label>
                <input type="number" min="1" name="txtduracion" id="txtduracion"
                    value="<?= htmlspecialchars($plan_seleccionado['duracion_dias'] ?? '') ?>"
                    placeholder="30"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Estado</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-white text-sm">
                        <input type="radio" name="chkactivo" value="1"
                            <?= ($plan_seleccionado['activo'] ?? 0) == 1 ? 'checked' : '' ?>
                            class="accent-blue-500 w-4 h-4">
                        Activo
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-white text-sm">
                        <input type="radio" name="chkactivo" value="0"
                            <?= ($plan_seleccionado['activo'] ?? 0) == 0 ? 'checked' : '' ?>
                            class="accent-blue-500 w-4 h-4">
                        Inactivo
                    </label>
                </div>
            </div>

        </div>

        <input type="hidden" name="idActual" value="<?= $plan_seleccionado['id_plan'] ?? '' ?>">

        <div class="flex gap-3 pt-4 border-t border-gray-700">
            <button type="submit" name="accion" value="guardar"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-save"></i>
                <?= $plan_seleccionado ? 'Actualizar' : 'Guardar' ?>
            </button>

            <?php if ($plan_seleccionado): ?>
                <button type="submit" name="accion" value="eliminar"
                    onclick="return confirm('¿Eliminar este plan?')"
                    class="px-6 bg-transparent hover:bg-red-600/10 border border-gray-700 hover:border-red-600 text-red-500 font-semibold py-3 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Eliminar
                </button>
            <?php endif; ?>
        </div>

    </form>
</section>