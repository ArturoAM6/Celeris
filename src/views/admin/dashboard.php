<?php header("Refresh: 20") ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de administrador - CELERIS</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/pan_admin_emp.css">
  <script src="https://kit.fontawesome.com/d4435a1b16.js" crossorigin="anonymous"></script>
</head>
<body>
  <header>
    <form action="<?= BASE_URL ?>/logout" method="post">
        <button type="submit" name="logout" style="background-color: #f4f3f2; color: black;" class="btn">Salir</button>
    </form>
    <h1><img src="<?= BASE_URL ?>/img/logo_celeris_blanco.png" class="rayo "alt="Logo"> Panel de Control</h1>
  </header>
  
  <section class="summary">
    <div class="card">Total Turnos: <span><?= htmlspecialchars(count($turnosActivos)); ?></span></div>
    <div class="card">En Espera: <span><?= htmlspecialchars(count($turnosEspera)); ?></span></div>
    <div class="card">En Atención: <span><?= htmlspecialchars(count($turnosAtencion)); ?></span></div>
    <div class="card">Completados: <span><?= htmlspecialchars(count($turnosCompletados)); ?></span></div>
    <div class="card">Empleados Activos: <span><?= htmlspecialchars(count($empleadosActivos)); ?></span></div>
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
                <?php if (empty($empleadosPaginados)): ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No hay empleados registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleadosPaginados as $empleado): ?>
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
                                <div class="tooltip">
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
                                    )"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <span class="tooltiptext">Editar Empleado</span>
                                </div>
                                <form action="<?= BASE_URL ?>/admin/empleados/borrar" method="post">
                                    <input type="hidden" name="id" value="<?= $empleado->getId() ?>">
                                    <div class="tooltip">
                                        <button class="btn" type="submit" onclick="return confirm('¿Estás seguro de eliminar este empleado?')"><i class="fa-solid fa-user-minus"></i></button>
                                        <span class="tooltiptext">Dar de baja empleado</span>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ($totalPaginasEmpleados > 1): ?>
    <div>
        <?php if ($paginaActualEmpleados > 1): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Empleados=<?= $paginaActualEmpleados - 1 ?>">Anterior</a>
        <?php endif; ?>
        
        Página <?= $paginaActualEmpleados ?> de <?= $totalPaginasEmpleados ?>
        
        <?php if ($paginaActualEmpleados < $totalPaginasEmpleados): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Empleados=<?= $paginaActualEmpleados + 1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
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
                <?php $cajasPaginadas = array_map(null, $cajasPaginadas["cajas"], $cajasPaginadas["empleados"]); ?>
                <?php if (empty($cajasPaginadas)): ?>
                    <tr>
                        <td colspan="6" class="texto-centrado">No hay cajas registradas</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cajasPaginadas as [$caja, $empleado]): ?>
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
                            <td class="<?php switch ($caja->getEstado()) {
                                    case 1: echo 'estado-abierto'; break;
                                    case 2: echo 'estado-cerrado'; break;
                                    case 3: echo 'estado-pausado'; break;
                                    case 4: echo 'estado-fuera-servicio'; break;
                                } ?>">
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
                                <?= $empleado->getNombreCompleto() ?>
                            </td>
                            <td class="acciones-tabla">
                                <?php if ($caja->getEstado() == 2): ?>
                                <div class="tooltip">
                                <button class="btn" onclick="abrirModalAsignar(
                                    <?= htmlspecialchars(json_encode([
                                        'id' => $caja->getId(),
                                        'numero' => $caja->getNumero(),
                                        'id_departamento' => $caja->getDepartamento(),
                                        'id_estado' => $caja->getEstado(),
                                        'id_empleado' => $empleado_id ?? null
                                    ])) 
                                    ?>
                                    )"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <span class="tooltiptext">Asignar Caja</span>
                                </div>
                                <form action="<?= BASE_URL ?>/admin/cajas/cambiar-estado" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <input type="hidden" name="id_estado" value="4">
                                        <div class="tooltip">
                                            <button type="submit" class="btn"><i class="fa-solid fa-triangle-exclamation"></i></button>
                                            <span class="tooltiptext">Caja Fuera de Servicio</span>
                                        </div>
                                </form>
                                <?php endif?>
                                <?php if ($caja->getEstado() == 1): ?>
                                    <form action="<?= BASE_URL ?>/admin/cajas/cambiar-estado" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <input type="hidden" name="id_estado" value="2">
                                        <div class="tooltip">
                                            <button type="submit" class="btn"><i class="fa-solid fa-power-off"></i></button>
                                            <span class="tooltiptext">Cerrar Caja</span>
                                        </div>
                                    </form>
                                    <form action="<?= BASE_URL ?>/admin/cajas/cambiar-estado" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <input type="hidden" name="id_estado" value="3">
                                        <div class="tooltip">
                                            <button type="submit" class="btn"><i class="fa-solid fa-circle-pause"></i></button>
                                            <span class="tooltiptext">Pausar Caja</span>
                                        </div>
                                    </form>
                                <?php elseif ($caja->getEstado() == 2 || $caja->getEstado() == 3): ?>
                                    <form action="<?= BASE_URL ?>/admin/cajas/cambiar-estado" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <input type="hidden" name="id_estado" value="1">
                                        <div class="tooltip">
                                            <button type="submit" class="btn"><i class="fa-solid fa-circle-play"></i></button>
                                            <span class="tooltiptext">Abrir Caja</span>
                                        </div>
                                    </form>
                                <?php elseif ($caja->getEstado() == 4): ?>
                                    <form action="<?= BASE_URL ?>/admin/cajas/cambiar-estado" method="post">
                                        <input type="hidden" name="id" value="<?= $caja->getId() ?>">
                                        <input type="hidden" name="id_estado" value="1">
                                        <div class="tooltip">
                                            <button type="submit" class="btn"><i class="fa-solid fa-circle-play"></i></button>
                                            <span class="tooltiptext">Abrir Caja</span>
                                        </div>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ($totalPaginasCajas > 1): ?>
    <div>
        <?php if ($paginaActualCajas > 1): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Caja=<?= $paginaActualCajas - 1 ?>">Anterior</a>
        <?php endif; ?>
        
        Página <?= $paginaActualCajas ?> de <?= $totalPaginasCajas ?>
        
        <?php if ($paginaActualCajas < $totalPaginasCajas): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Caja=<?= $paginaActualCajas + 1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
    </div>
    <div class="tab-content" id="turnos">
        <section class="filtros-simple">
    <form method="GET" action="<?= BASE_URL ?>/admin" class="form-filtros-inline">
      <span class="filtro-label">Filtrar:</span>
      
      <select name="departamento" class="filtro-input">
        <option value="">Departamento</option>
        <option value="1" <?= ($datosPaginacion['filtros']['id_departamento'] ?? '') == '1' ? 'selected' : '' ?>>Ventanillas</option>
        <option value="2" <?= ($datosPaginacion['filtros']['id_departamento'] ?? '') == '2' ? 'selected' : '' ?>>Asociados</option>
        <option value="3" <?= ($datosPaginacion['filtros']['id_departamento'] ?? '') == '3' ? 'selected' : '' ?>>Caja Fuerte</option>
        <option value="4" <?= ($datosPaginacion['filtros']['id_departamento'] ?? '') == '4' ? 'selected' : '' ?>>Asesoramiento Financiero</option>
      </select>

      <select name="estado" class="filtro-input">
        <option value="">Estado</option>
        <option value="1" <?= ($datosPaginacion['filtros']['id_estado'] ?? '') == '1' ? 'selected' : '' ?>>Llamado</option>
        <option value="2" <?= ($datosPaginacion['filtros']['id_estado'] ?? '') == '2' ? 'selected' : '' ?>>En espera</option>
        <option value="3" <?= ($datosPaginacion['filtros']['id_estado'] ?? '') == '3' ? 'selected' : '' ?>>En atencion</option>
        <option value="4" <?= ($datosPaginacion['filtros']['id_estado'] ?? '') == '4' ? 'selected' : '' ?>>Cancelado</option>
        <option value="5" <?= ($datosPaginacion['filtros']['id_estado'] ?? '') == '5' ? 'selected' : '' ?>>Finalizado</option>
      </select>

      <input 
        type="number" 
        name="caja" 
        placeholder="Número de Caja" 
        class="filtro-input"
        value="<?= htmlspecialchars($datosPaginacion['filtros']['id_caja'] ?? '') ?>"
      >

      <input 
        type="date" 
        name="fecha" 
        class="filtro-input"
        value="<?= htmlspecialchars($datosPaginacion['filtros']['fecha'] ?? '') ?>"
      >

      <input 
        type="number" 
        name="numero" 
        placeholder="Número de Turno" 
        class="filtro-input"
        value="<?= htmlspecialchars($datosPaginacion['filtros']['numero_turno'] ?? '') ?>"
      >

      <button type="submit" class="btn-filtrar-simple">Buscar</button>
      <a style = "color : black;" href="<?= BASE_URL ?>/admin" class="btn-limpiar-simple">Limpiar</a>
    </form>
  </section>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Numero de Turno</th>
                <th>Cliente</th>
                <th>Departamento</th>
                <th>Caja</th>
                <th>Estado</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
            </tr>
        </thead>
        <tbody>
            <?php $turnosPaginados = array_map(null, $turnosPaginados["turnos"], $turnosPaginados["departamentos"]); ?>
            <?php if (empty($turnosPaginados)): ?>
                <tr>
                    <td colspan="8" class="texto-centrado">No hay turnos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($turnosPaginados as $turno): ?>
                    <tr>
                        <td><?= htmlspecialchars($turno[0]['id']) ?></td>
                        <td><?= htmlspecialchars($turno[0]['numero']) ?></td>
                        <?php if (!empty($turno[0]['id_cliente'])): ?>
                            <td><?= htmlspecialchars($turno[0]['id_cliente'] . " - " . $turno[0]['cliente_nombre'] . " " . $turno[0]['cliente_apellido_paterno']) ?></td>
                        <?php else: ?>
                            <td><?= htmlspecialchars('N/A') ?></td>
                        <?php endif; ?>
                        <td><?php switch ($turno[1]) {
                            case 1:
                                echo "Ventanillas";
                                break;
                            case 2:
                                echo "Asociados";
                                break;
                            case 3:
                                echo "Caja Fuerte";
                                break;
                            case 4:
                                echo "Asesoramiento Financiero";
                                break;
                        }
                        ?></td>
                        <td><?= htmlspecialchars($turno[0]['numero_caja']) ?></td>
                        <td class="<?php switch ($turno[0]['id_estado']) {
                                    case 1: echo 'estado-llamado'; break;
                                    case 2: echo 'estado-espera'; break;
                                    case 3: echo 'estado-inicio-atencion'; break;
                                    case 4: echo 'estado-cancelado'; break;
                                } ?>">
                            <?php 
                            switch ($turno[0]['id_estado']) {
                                case 1: echo 'Llamado'; break;
                                case 2: echo 'En Espera'; break;
                                case 3: echo 'En Atención'; break;
                                case 4: echo 'Cancelado'; break;
                                case 5: echo 'Finalizado'; break;
                                default: echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars(date("d-m-Y H:i:s", strtotime($turno[0]["timestamp_solicitud"]))) ?></td>
                        <?php if ($turno[0]["timestamp_fin_atencion"]): ?>
                            <td><?= htmlspecialchars(date("d-m-Y H:i:s", strtotime($turno[0]["timestamp_fin_atencion"]))) ?></td>
                        <?php else: ?>
                            <td><?= "N/A" ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
        <div>
            <?php 
            $urlBase = BASE_URL . '/admin?pagina_Turno=';
            $filtrosUrl = '';
            if (!empty($datosPaginacion['filtros']['id_departamento'])) $filtrosUrl .= '&departamento=' . urlencode($datosPaginacion['filtros']['id_departamento']);
            if (!empty($datosPaginacion['filtros']['id_estado'])) $filtrosUrl .= '&estado=' . urlencode($datosPaginacion['filtros']['id_estado']);
            if (!empty($datosPaginacion['filtros']['id_caja'])) $filtrosUrl .= '&caja=' . urlencode($datosPaginacion['filtros']['id_caja']);
            if (!empty($datosPaginacion['filtros']['fecha'])) $filtrosUrl .= '&fecha=' . urlencode($datosPaginacion['filtros']['fecha']);
            if (!empty($datosPaginacion['filtros']['numero_turno'])) $filtrosUrl .= '&numero=' . urlencode($datosPaginacion['filtros']['numero_turno']);
            ?>
            
            <?php if ($paginaActual > 1): ?>
                <a style="color: black;" href="<?= $urlBase . ($paginaActual - 1) . $filtrosUrl ?>">Anterior</a>
            <?php endif; ?>
            
            Página <?= $paginaActual ?> de <?= $totalPaginas ?>
            
            <?php if ($paginaActual < $totalPaginas): ?>
                <a style="color: black;" href="<?= $urlBase . ($paginaActual + 1) . $filtrosUrl ?>">Siguiente</a>
            <?php endif; ?>
        </div>
</div>
    <div class="tab-content" id="horarios">
        <form action="<?= BASE_URL ?>/admin/horario/asignar" method="post">
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
                    <?php $tiposTurno = [1 => 'Terciado', 2 => 'Semanal'];?>
                    <?php if (empty($horariosPaginados)): ?>
                        <tr>
                            <td colspan="10" class="texto-centrado">No hay horarios registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($horariosPaginados as $horario): ?>
                            <tr>
                                <td><?= htmlspecialchars($horario['id'] ." - ". $horario['nombre']. " ". $horario['apellido_paterno']) ?></td>
                                <td><?= htmlspecialchars($horario['hora_entrada']) ?></td>
                                <td><?= htmlspecialchars($horario['hora_salida']) ?></td>
                                <td>
                                    <select name="id_tipo_turno[<?= $horario['id'] ?>]">
                                        <?php foreach ($tiposTurno as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $horario['tipo_turno'] == $key ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <?php if ($horario['tipo_turno'] == 1): ?>
                                    <td class= "texto-centrado">✓</td>
                                    <td class= "texto-centrado">🗙</td>
                                    <td class= "texto-centrado">✓</td>
                                    <td class= "texto-centrado">🗙</td>
                                    <td class= "texto-centrado">✓</td>
                                    <td class= "texto-centrado">🗙</td>
                                <?php else: ?>
                                    <td class="texto-centrado">✓</td>
                                    <td class="texto-centrado">✓</td>
                                    <td class="texto-centrado">✓</td>
                                    <td class="texto-centrado">✓</td>
                                    <td class="texto-centrado">✓</td>
                                    <td class="texto-centrado">✓</td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div>
                <button type='submit' class="btn" style="margin-top: 10px;">Guardar Cambios</button>
            </div>
        </form>
        <?php if ($totalPaginasHorarios > 1): ?>
    <div>
        <?php if ($paginaActualHorarios > 1): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Horario=<?= $paginaActualHorarios - 1 ?>">Anterior</a>
        <?php endif; ?>
        
        Página <?= $paginaActualHorarios ?> de <?= $totalPaginasHorarios ?>
        
        <?php if ($paginaActualHorarios < $totalPaginasHorarios): ?>
            <a style = "color : black;" href="<?= BASE_URL ?>/admin?pagina_Horario=<?= $paginaActualHorarios + 1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>
    <div class="tab-content" id="descansos">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($descansosPaginados)): ?>
                <tr>
                    <td colspan="4" class="texto-centrado">No hay descansos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($descansosPaginados as $descanso): ?>
                    <tr>
                        <td><?= $descanso->getId() ?></td>
                        <td><?= $descanso->getNombreCompleto() ?></td>
                        <td><?= $descanso->getEmail() ?></td>
                        <td><?= $descanso->getRol() ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        
    
    <?php if ($totalPaginasDescansos > 1): ?>
        <div>
            <?php if ($paginaActualDescansos > 1): ?>
                <a style="color: black;" href="<?= BASE_URL ?>/admin?pagina_Descanso=<?= $paginaActualDescansos - 1 ?>">Anterior</a>
            <?php endif; ?>
            
            Página <?= $paginaActualDescansos ?> de <?= $totalPaginasDescansos ?>
            
            <?php if ($paginaActualDescansos < $totalPaginasDescansos): ?>
                <a style="color: black;" href="<?= BASE_URL ?>/admin?pagina_Descanso=<?= $paginaActualDescansos + 1 ?>">Siguiente</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
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
                    <input type="password" id="registrar_password" name="password" require>
                </div>

                <div class="campo">
                    <label for="registrar_password2">Repita su contraseña *</label>
                    <input type="password" id="registrar_password2" name="password2" require>
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
                <input type="hidden" id="asign_id" name="id_caja">
                
                <div class="campo">
                    <label for="asign_id_departamento">Departamento *</label>
                    <input type="text" id="asign_id_departamento" disabled>
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
            </form>
        </div>
    </div>

<script>const BASE_URL = '<?= BASE_URL ?>';</script>
<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>