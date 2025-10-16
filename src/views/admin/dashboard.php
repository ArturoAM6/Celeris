<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de administrador - CELERIS</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/pan_admin_emp.css">
</head>
<body>
  <header>
    <a href="<?= BASE_URL ?>/logout" class="btn" style="background-color: #f4f3f2; color: black;">Salir</a>
    <h1>Panel de Administración - CELERIS</h1>
  </header>
  
  <section class="summary">
    <div class="card">Total Turnos: <span>2</span></div>
    <div class="card">En Espera: <span>0</span></div>
    <div class="card">En Atención: <span>1</span></div>
    <div class="card">Completados: <span>2</span></div>
    <div class="card">Empleados Activos: <span><?= htmlspecialchars(count($empleados)); ?></span></div>
    <div class="card">Total Empleados: <span><?= htmlspecialchars(count($empleados)); ?></span></div>
  </section>
  
  <section class="table-section">
    <div class="tab">
        <button class="tab-links" onclick="openTab(event, 'empleados')" id="defaultOpen">Empleados</button>
        <button class="tab-links" onclick="openTab(event, 'cajas')">Cajas</button>
        <button class="tab-links" onclick="openTab(event, 'turnos')">Turnos</button>
        <button class="tab-links" onclick="openTab(event, 'horarios')">Horarios</button>
        <button class="tab-links" onclick="openTab(event, 'descansos')">Descansos</button>
        <button class="tab-links" onclick="abrirModal('modalRegistrar')">+</button>
    </div>
    <div class="tab-content" id="empleados">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Departamento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No hay empleados registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?= htmlspecialchars($empleado->getId()) ?></td>
                            <td><?= htmlspecialchars($empleado->getNombreCompleto()) ?></td>
                            <td><?= htmlspecialchars($empleado->getEmail()) ?></td>
                            <td>
                                <?php
                                switch ($empleado->getRol()) {
                                    case 1: echo 'Administrador'; break;
                                    case 2: echo 'Operador'; break;
                                    case 3: echo 'Recepcionista'; break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                switch ($empleado->getDepartamento()) {
                                    case 1: echo 'Ventanillas'; break;
                                    case 2: echo 'Asociados'; break;
                                    case 3: echo 'Caja Fuerte'; break;
                                    case 4: echo 'Asesoramiento Financiero'; break;
                                }
                                ?>
                            </td>
                            <td class="acciones-tabla">
                                <button class="btn" onclick="abrirModalEditar(
                                    <?= htmlspecialchars(json_encode([
                                        'id' => $empleado->getId(),
                                        'nombre' => $empleado->getNombre(),
                                        'apellido_paterno' => $empleado->getApellidoPaterno(),
                                        'apellido_materno' => $empleado->getApellidoMaterno(),
                                        'password_hash' => $empleado->getPasswordHash(),
                                        'email' => $empleado->getEmail(),
                                        'id_rol' => $empleado->getRol(),
                                        'id_departamento' => $empleado->getDepartamento(),
                                        'id_tipo_turno' => $empleado->getTipoTurno()
                                    ])) 
                                    ?>
                                )">Editar</button>
                                <form action="<?= BASE_URL ?>/admin/empleados/borrar" method="post">
                                    <input type="hidden" name="id" value="<?= $empleado->getId() ?>">
                                    <button class="btn" type="submit" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">Dar de baja</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="tab-content" id="cajas">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numero de caja</th>
                    <th>Departamento</th>
                    <th>Estado</th>
                    <th>Empleado asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cajas)): ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No hay cajas registradas</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cajas as $caja): ?>
                        <tr>
                            <td><?= htmlspecialchars($caja->getId()) ?></td>
                            <td><?= htmlspecialchars($caja->getNumero()) ?></td>
                            <td>
                                <?php 
                                switch ($caja->getDepartamento()) {
                                    case 1: echo 'Ventanillas'; break;
                                    case 2: echo 'Asociados'; break;
                                    case 3: echo 'Caja Fuerte'; break;
                                    case 4: echo 'Asesoramiento Financiero'; break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                switch ($caja->getEstado()) {
                                    case 1: echo 'Abierta'; break;
                                    case 2: echo 'Cerrada'; break;
                                    case 3: echo 'Pausada'; break;
                                    case 4: echo 'Fuera de Servicio'; break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo $empleado_id = $cajaController->obtenerCajaEmpleado($caja->getId());
                                    echo " - ";
                                    echo $empleado = $empleadoController->obtenerEmpleadoPorId($empleado_id)->getNombreCompleto() 
                                ?>
                            </td>
                            <td class="acciones-tabla">
                                <?php if ($caja->getEstado() != 1): ?>
                                <button class="btn" onclick="abrirModalAsignar(
                                    <?= htmlspecialchars(json_encode([
                                        'id' => $caja->getId(),
                                        'numero' => $caja->getNumero(),
                                        'id_departamento' => $caja->getDepartamento(),
                                        'id_estado' => $caja->getEstado(),
                                        'id_empleado' => $empleado_id ?? null
                                    ])) 
                                    ?>
                                    )">Asignar Caja</button>
                                <?php endif?>
                                <?php if ($caja->getEstado() == 1): ?>
                                    <form action="<?= BASE_URL ?>/admin/cajas/cerrar" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <button type="submit" class="btn">Cerrar</button>
                                    </form>
                                    <form action="<?= BASE_URL ?>/admin/cajas/pausar" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <button type="submit" class="btn">Pausar</button>
                                    </form>
                                <?php elseif ($caja->getEstado() == 2 || $caja->getEstado() == 3): ?>
                                    <form action="<?= BASE_URL ?>/admin/cajas/abrir" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <button type="submit" class="btn">Abrir</button>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="tab-content" id="turnos">
        <table>
            <thead>
                <tr>
                    <th>Algo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Turno</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-content" id="horarios">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Turno</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miercoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sabado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php
                    echo $horario
                    ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-content" id="descansos">
        <table>
            <thead>
                <tr>
                    <th>Algo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Descanso</td>
                </tr>
            </tbody>
        </table>
    </div>
  </section>

    <!-- Modal Editar -->
    <div id="modalEditar" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2>Editar Empleado</h2>
                <span class="cerrar-modal" onclick="cerrarModal('modalEditar')">&times;</span>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>/admin/empleados/editar" id="formEditar">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_password" name="password_hash">
                
                <div class="campo">
                    <label for="edit_nombre">Nombre *</label>
                    <input type="text" id="edit_nombre" name="nombre" required>
                </div>

                <div class="campo">
                    <label for="edit_apellido_paterno">Apellido Paterno *</label>
                    <input type="text" id="edit_apellido_paterno" name="apellido_paterno" required>
                </div>

                <div class="campo">
                    <label for="edit_apellido_materno">Apellido Materno</label>
                    <input type="text" id="edit_apellido_materno" name="apellido_materno">
                </div>

                <div class="campo">
                    <label for="edit_email">Email *</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="campo">
                    <label for="edit_id_rol">Rol *</label>
                    <select id="edit_id_rol" name="id_rol" required>
                        <option value="1">Administrador</option>
                        <option value="2">Operador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="edit_id_departamento">Departamento *</label>
                    <select id="edit_id_departamento" name="id_departamento" required>
                        <option value="1">Ventanillas</option>
                        <option value="2">Asociados</option>
                        <option value="3">Caja Fuerte</option>
                        <option value="4">Asesoramiento Financiero</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="edit_id_tipo_turno">Horario *</label>
                    <select id="edit_id_tipo_turno" name="id_tipo_turno" required>
                        <option value="1">Terciado</option>
                        <option value="2">Semanal</option>
                    </select>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Actualizar</button>
                    <button type="button" onclick="cerrarModal('modalEditar')" class="boton boton-secundario">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Modal Registrar -->
    <div id="modalRegistrar" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2>Registrar Empleado</h2>
                <span class="cerrar-modal" onclick="cerrarModal('modalRegistrar')">&times;</span>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>/admin/empleados/registrar" id="formRegistrar">
                <div class="campo">
                    <label for="registrar_nombre">Nombre *</label>
                    <input type="text" id="registrar_nombre" name="nombre" required>
                </div>
                
                <div class="campo">
                    <label for="registrar_apellido_paterno">Apellido Paterno *</label>
                    <input type="text" id="registrar_apellido_paterno" name="apellido_paterno" required>
                </div>
                
                <div class="campo">
                    <label for="registrar_apellido_materno">Apellido Materno</label>
                    <input type="text" id="registrar_apellido_materno" name="apellido_materno">
                </div>
                
                <div class="campo">
                    <label for="registrar_password">Contraseña *</label>
                    <input type="password" id="registrar_password" name="password">
                </div>

                <div class="campo">
                    <label for="registrar_password2">Repita su contraseña *</label>
                    <input type="password" id="registrar_password2" name="password2">
                </div>

                <div class="campo">
                    <label for="registrar_email">Email *</label>
                    <input type="email" id="registrar_email" name="email" required>
                </div>

                <div class="campo">
                    <label for="registrar_id_rol">Rol *</label>
                    <select id="registrar_id_rol" name="id_rol" required>
                        <option value="1">Administrador</option>
                        <option value="2">Operador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="registrar_id_departamento">Departamento *</label>
                    <select id="registrar_id_departamento" name="id_departamento" required>
                        <option value="1">Ventanillas</option>
                        <option value="2">Asociados</option>
                        <option value="3">Caja Fuerte</option>
                        <option value="4">Asesoramiento Financiero</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="registrar_id_tipo_turno">Horario *</label>
                    <select id="registrar_id_tipo_turno" name="id_tipo_turno" required>
                        <option value="1">Terciado</option>
                        <option value="2">Semanal</option>
                    </select>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-primario">Registrar</button>
                    <button type="button" onclick="cerrarModal('modalRegistrar')" class="boton boton-secundario">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal asignar -->
    <div id="modalAsignar" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2>Asignar Empleado</h2>
                <span class="cerrar-modal" onclick="cerrarModal('modalAsignar')">&times;</span>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>/admin/cajas/asignar" id="formAsignar">
                <input type="hidden" id="asign_id" name="id">
                <input type="hidden" id="asign_numero" name="numero">
                <input type="hidden" id="asign_id_estado" name="id_estado">
                
                <div class="campo">
                    <label for="asign_id_departamento">Departamento *</label>
                    <select id="asign_id_departamento" name="id_departamento" required>
                        <option value="1">Ventanillas</option>
                        <option value="2">Asociados</option>
                        <option value="3">Caja Fuerte</option>
                        <option value="4">Asesoramiento Financiero</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="asign_id_empleado">Empleado *</label>
                    <?php if (empty($empleadosAsignados)): ?>
                        <p style="font-weight: bold;">Sin empleados disponibles</p>
                    <?php else: ?>
                        <select id="asign_id_empleado" name="id_empleado" required>
                            <?php foreach($empleadosAsignados as $empleado): ?>
                                    <option value="<?= $empleado->getId() ?>"><?= $empleado->getNombreCompleto() ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                </div>

                <div class="acciones-formulario">
                    <?php if (!empty($empleadosAsignados)): ?>
                        <button type="submit" class="boton boton-primario">Actualizar</button>
                    <?php endif ?>
                    <button type="button" onclick="cerrarModal('modalAsignar')" class="boton boton-secundario">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

<script>const BASE_URL = '<?= BASE_URL ?>';</script>
<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
