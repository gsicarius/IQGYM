<?php
// form.php — se incluye desde index.php (clases.php)
// Requiere: $plan_seleccionado (array|null), $entrenadores (array), $dias (array)
$c = $plan_seleccionado ?? null;
?>

<section class="bg-gray-800 rounded-xl p-8 max-w-2xl mx-auto border border-gray-700">

    <div class="mb-6">
        <a href="index.php"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Regresar
        </a>
    </div>

    <h2 class="text-white text-2xl font-semibold mb-8">
        <?= $c ? 'Editar clase' : 'Nueva clase' ?>
    </h2>

    <form method="POST" action="index.php">

        <div class="grid grid-cols-2 gap-x-6 gap-y-5 mb-8">

            <!-- Nombre -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Nombre</label>
                <input type="text" name="txtnombre"
                    value="<?= htmlspecialchars($c['nombre_clase'] ?? '') ?>"
                    placeholder="Ej. Yoga Matutino"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <!-- Descripción -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Descripción</label>
                <input type="text" name="txtdescripcion"
                    value="<?= htmlspecialchars($c['descripcion'] ?? '') ?>"
                    placeholder="Breve descripción"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <!-- Entrenador -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Entrenador</label>
                <select name="txtentrenador"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
                    <option value="">Seleccionar entrenador</option>
                    <?php foreach ($entrenadores as $e): ?>
                        <option value="<?= $e['id_usuario'] ?>"
                            <?= isset($c['id_entrenador']) && $c['id_entrenador'] == $e['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Día de la semana -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Día de la semana</label>
                <select name="txtdia_semana"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
                    <option value="">Seleccionar día</option>
                    <?php foreach ($dias as $num => $nombre_dia): ?>
                        <?php if ($num === 0) continue; ?>
                        <option value="<?= $num ?>"
                            <?= isset($c['dia_semana']) && $c['dia_semana'] == $num ? 'selected' : '' ?>>
                            <?= $nombre_dia ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Hora inicio -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Hora inicio</label>
                <input type="time" name="txthora_inicio"
                    value="<?= htmlspecialchars($c['hora_inicio'] ?? '') ?>"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <!-- Hora fin -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Hora fin</label>
                <input type="time" name="txthora_fin"
                    value="<?= htmlspecialchars($c['hora_fin'] ?? '') ?>"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required>
            </div>

            <!-- Cupo máximo -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Cupo máximo</label>
                <input type="number" min="1" name="txtcupo_maximo"
                    value="<?= htmlspecialchars($c['cupo_maximo'] ?? '20') ?>"
                    placeholder="20"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <!-- Fecha creación -->
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Fecha de creación</label>
                <input type="datetime-local" name="txtfecha_creacion"
                    value="<?= $c && $c['fecha_creacion'] ? date('Y-m-d\TH:i', strtotime($c['fecha_creacion'])) : date('Y-m-d\TH:i') ?>"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <!-- Estado -->
            <div class="col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Estado</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer text-white text-sm">
                        <input type="radio" name="chkactivo" value="1"
                            <?= ($c['activa'] ?? 1) == 1 ? 'checked' : '' ?>
                            class="accent-blue-500 w-4 h-4">
                        Activa
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-white text-sm">
                        <input type="radio" name="chkactivo" value="0"
                            <?= ($c['activa'] ?? 1) == 0 ? 'checked' : '' ?>
                            class="accent-blue-500 w-4 h-4">
                        Inactiva
                    </label>
                </div>
            </div>

        </div>

        <input type="hidden" name="idActual" value="<?= $c['id_clase'] ?? '' ?>">

        <div class="flex gap-3 pt-4 border-t border-gray-700">
            <button type="submit" name="accion" value="guardar"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-save"></i>
                <?= $c ? 'Actualizar' : 'Guardar' ?>
            </button>

            <?php if ($c): ?>
                <button type="submit" name="accion" value="eliminar"
                    onclick="return confirm('¿Eliminar esta clase?')"
                    class="px-6 bg-transparent hover:bg-red-600/10 border border-gray-700 hover:border-red-600 text-red-500 font-semibold py-3 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Eliminar
                </button>
            <?php endif; ?>
        </div>

    </form>
</section>
