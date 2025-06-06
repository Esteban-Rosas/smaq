<?php
session_start();
include '../includes/header.php';
include_once('../includes/conexion.php');

// Obtener ubicaciones desde la base de datos usando PDO
$sql_ubicaciones = "SELECT id, nombre FROM ubicaciones ORDER BY nombre ASC";
$stmt = $conexion->query($sql_ubicaciones);
$ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Equipo - SMAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registrar Nuevo Equipo</h5>
            </div>
            <div class="card-body">
                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success">
                        <?php echo $mensaje_exito; ?>
                    </div>
                <?php endif; ?>

                <form action="../includes/guardar_equipo.php" method="POST" enctype="multipart/form-data">
                    <!-- DATOS BÁSICOS -->
                    <h6 class="text-primary">Datos del Equipo</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" name="codigo" id="codigo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre del Equipo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ubicacion_id" class="form-label">Ubicación</label>
                            <select class="form-select" name="ubicacion_id" id="ubicacion_id" required>
                                <option value="">Seleccione una ubicación</option>
                                <?php foreach ($ubicaciones as $row): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" name="marca" id="marca" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" name="modelo" id="modelo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="serie" class="form-label">Serie</label>
                            <input type="text" name="serie" id="serie" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="destinado" class="form-label">Equipo destinado para</label>
                        <textarea name="destinado" id="destinado" class="form-control" rows="2" required></textarea>
                    </div>

                    <!-- REGISTRO HISTÓRICO -->
                    <h6 class="text-primary mt-4">Registro Histórico</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="forma_adquisicion" class="form-label">Forma de adquisición</label>
                            <select name="forma_adquisicion" id="forma_adquisicion" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option>Compra Directa</option>
                                <option>Arrendamiento</option>
                                <option>Donación</option>
                                <option>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="vida_util" class="form-label">Vida útil (años)</label>
                            <input type="number" name="vida_util" id="vida_util" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_adquisicion" class="form-label">Fecha de adquisición</label>
                            <input type="date" name="fecha_adquisicion" id="fecha_adquisicion" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_garantia" class="form-label">Fecha fin de garantía</label>
                            <input type="date" name="fecha_garantia" id="fecha_garantia" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_instalacion" class="form-label">Fecha de instalación</label>
                            <input type="date" name="fecha_instalacion" id="fecha_instalacion" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="fabricante" class="form-label">Fabricante</label>
                            <input type="text" name="fabricante" id="fabricante" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contacto_fabricante" class="form-label">Contacto Fabricante</label>
                            <input type="text" name="contacto_fabricante" id="contacto_fabricante" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" name="pais" id="pais" class="form-control">
                        </div>
                    </div>

                    <!-- CARACTERÍSTICAS TÉCNICAS -->
                    <h6 class="text-primary mt-4">Características Técnicas</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tecnologia" class="form-label">Tecnología</label>
                            <input type="text" name="tecnologia" id="tecnologia" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="frecuencia" class="form-label">Frecuencia (Hz)</label>
                            <input type="text" name="frecuencia" id="frecuencia" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="voltaje" class="form-label">Voltaje (V)</label>
                            <input type="text" name="voltaje" id="voltaje" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="corriente" class="form-label">Corriente (A)</label>
                            <input type="text" name="corriente" id="corriente" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="peso" class="form-label">Peso (kg)</label>
                            <input type="text" name="peso" id="peso" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="capacidad" class="form-label">Capacidad</label>
                            <input type="text" name="capacidad" id="capacidad" class="form-control">
                        </div>
                    </div>

                    <!-- FUENTE DE ALIMENTACIÓN -->
                    <div class="mb-4">
                        <h6 class="text-primary">Fuente de Alimentación</h6>
                        <select name="fuente_alimentacion" id="fuente_alimentacion" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option>ACPM</option>
                            <option>Electricidad</option>
                            <option>Energía Solar</option>
                            <option>Aire</option>
                            <option>Agua</option>
                            <option>Vapor</option>
                            <option>Otro</option>
                        </select>
                    </div>

                    <!-- FOTO DEL EQUIPO -->
                    <div class="mb-4">
                        <label for="foto" class="form-label">Foto del equipo</label>
                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                    </div>

                    <!-- MANUALES (puedes poner esto después del campo de foto, antes de los botones) -->
                    <div class="row mb-4">
    <div class="col-md-6">
        <h6 class="text-primary">Manuales</h6>
        <div class="mb-2">
            <label>Manual Operativo</label>
            <input type="file" name="manual_operativo" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Manual de Servicio</label>
            <input type="file" name="manual_servicio" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Manual de Partes</label>
            <input type="file" name="manual_partes" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Manual de Instalación</label>
            <input type="file" name="manual_instalacion" class="form-control" accept="application/pdf">
        </div>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Planos</h6>
        <div class="mb-2">
            <label>Plano Eléctrico</label>
            <input type="file" name="plano_electrico" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Plano Electrónico</label>
            <input type="file" name="plano_electronico" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Plano Hidráulico</label>
            <input type="file" name="plano_hidraulico" class="form-control" accept="application/pdf">
        </div>
        <div class="mb-2">
            <label>Plano Neumático</label>
            <input type="file" name="plano_neumatico" class="form-control" accept="application/pdf">
        </div>
    </div>
</div>

                    <!-- BOTONES -->
                    <button type="submit" class="btn btn-success">Guardar Equipo</button>
                    <a href="../dashboard.php" class="btn btn-secondary">Cancelar</a>               
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="modalManualesPlanos" tabindex="-1" aria-labelledby="modalManualesPlanosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalManualesPlanosLabel">Subir Manuales y Planos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- MANUALES -->
          <div class="col-md-6">
            <h6 class="text-primary">Manuales</h6>
            <div class="mb-2">
              <label>Manual Operativo</label>
              <input type="file" name="manual_operativo" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Manual de Servicio</label>
              <input type="file" name="manual_servicio" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Manual de Partes</label>
              <input type="file" name="manual_partes" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Manual de Instalación</label>
              <input type="file" name="manual_instalacion" class="form-control" accept="application/pdf">
            </div>
          </div>
          <!-- PLANOS -->
          <div class="col-md-6">
            <h6 class="text-primary">Planos</h6>
            <div class="mb-2">
              <label>Plano Eléctrico</label>
              <input type="file" name="plano_electrico" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Plano Electrónico</label>
              <input type="file" name="plano_electronico" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Plano Hidráulico</label>
              <input type="file" name="plano_hidraulico" class="form-control" accept="application/pdf">
            </div>
            <div class="mb-2">
              <label>Plano Neumático</label>
              <input type="file" name="plano_neumatico" class="form-control" accept="application/pdf">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>


<?php include '../includes/footer.php'; ?>