<?php
include 'verificar_sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reportes - Administración de Activos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg" href="img/gear-fill.svg">
    <link rel="icon" type="image/svg" href="https://cdn-icons-png.flaticon.com/512/10871/10871903.png">

    <link rel="stylesheet" href="css/regreson.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 30, 60, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-boxes"></i> 
                <span class="d-none d-sm-inline">REGRESIÓN LINEAL SIMPLE</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-arrow-alt-circle-down"></i> Ir a
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="fas fa-home"></i> &nbsp; Inicio</a></li>
                            <li><a class="dropdown-item" href="formulario.php"><i class="fas fa-plus-circle"></i> &nbsp Formulario</a></li>
                            <li><a class="dropdown-item" href="estado_activos.php"><i class="fas fa-chart-line"></i> &nbsp Estado Activos</a></li>
                            <li><a class="dropdown-item" href="historiales.php"><i class="fas fa-history"></i> &nbsp Historiales</a></li>
                            <li><a class="dropdown-item" href="reportes.php"><i class="fas fa-file-alt"></i> &nbsp Reportes</a></li>
                            <li><a class="dropdown-item" href="reporte_graficos.php"><i class="fas fa-chart-pie"></i> &nbsp Reportes Gráficos</a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<main>
    <div class="container">
        <div class="action-panels">
            <!-- Panel para seleccionar activo y registrar datos de regresión -->
            <div class="panel">
                <h3><i class="fas fa-chart-line"></i> Registro de Datos de Regresión</h3>
                
                <div class="form-group">
                    <label for="selectActivo">Seleccionar Activo:</label>
                    <select id="selectActivo" class="form-control" onchange="mostrarDetallesActivo()">
                        <option value="">-- Seleccione un activo --</option>
                    </select>
                </div>

                <!-- Detalles del activo seleccionado -->
                <div id="detallesActivo" style="display: none; margin-bottom: 20px; padding: 15px; background-color: #e3f2fd; border-radius: 8px; border-left: 4px solid #2196f3;">
                    <h4 style="color: #1976d2; margin-bottom: 10px;">Detalles del Activo</h4>
                    <div id="infoActivo"></div>
                </div>

                <form id="formRegresion" style="display: none;">
                    <div class="form-group">
                        <label for="valorX">Variable Independiente: meses de uso (X):</label>
                        <input type="number" step="0.01" id="valorX" class="form-control" required placeholder="Escriba cuantos meses de uso tuvo hasta ahora." oninput="validarMesesUso()">
                        <small id="ayudaValorX" style="color: #666; font-size: 0.9em;">Ingrese el valor de la variable independiente (tiempo, uso, ciclos, etc.)</small>
                        <div id="errorValorX" style="color: #dc3545; font-size: 0.9em; margin-top: 5px; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="valorY">Variable Dependiente: estado del Activo (Y):</label>
                        <select id="valorY" class="form-control" required onchange="validarEstadoActivo()">
                            <option value="">-- Seleccione el estado --</option>
                            <option value="1" id="opcionNuevo">Nuevo</option>
                            <option value="2">Usado</option>
                            <option value="3">Dañado</option>
                            <option value="4">En Reparación</option>
                            <option value="5">Necesita renovación</option>
                        </select>
                        <small id="ayudaValorY" style="color: #666; font-size: 0.9em;">Seleccione el estado actual del activo</small>
                        <div id="errorValorY" style="color: #dc3545; font-size: 0.9em; margin-top: 5px; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea id="observaciones" class="form-control" rows="3" placeholder="Contexto del registro, observaciones adicionales..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btnRegistrar">
                        <i class="fas fa-save"></i> Registrar Datos
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </form>
                <script>
                // Variables para control de validaciones
                let ultimoValorX = null;
                let esPrimerRegistro = true;

                function validarMesesUso() {
                    const valorX = parseFloat(document.getElementById('valorX').value);
                    const errorValorX = document.getElementById('errorValorX');
                    const ayudaValorX = document.getElementById('ayudaValorX');
                    const btnRegistrar = document.getElementById('btnRegistrar');
                    
                    // Obtener el activo seleccionado y sus datos existentes
                    const activoId = parseInt(document.getElementById('selectActivo').value);
                    if (!activoId) return;
                    
                    const datosActivoExistentes = datosRegresionExistentes.filter(d => d.id_activo === activoId);
                    
                    if (datosActivoExistentes.length > 0) {
                        // Encontrar el valor X más alto (último registro)
                        const maxValorX = Math.max(...datosActivoExistentes.map(d => d.valor_x));
                        ultimoValorX = maxValorX;
                        esPrimerRegistro = false;
                        
                        if (!isNaN(valorX) && valorX <= ultimoValorX || valorX > 120) {
                            errorValorX.innerHTML = `<i class="fas fa-exclamation-triangle"></i> El valor debe ser mayor a ${ultimoValorX} meses (último registro) y menor a 120 meses. No se pueden ingresar valores hacia atrás en el tiempo.`;
                            errorValorX.style.display = 'block';
                            ayudaValorX.style.display = 'none';
                            btnRegistrar.disabled = true;
                            btnRegistrar.style.opacity = '0.6';
                            document.getElementById('valorX').style.borderColor = '#dc3545';
                            return false;
                        } else if (!isNaN(valorX) && valorX > ultimoValorX || valorX > 120) {
                            errorValorX.style.display = 'none';
                            ayudaValorX.style.display = 'block';
                            ayudaValorX.innerHTML = `<i class="fas fa-check-circle" style="color: #28a745;"></i> Correcto. Último registro: ${ultimoValorX} meses.`;
                            ayudaValorX.style.color = '#28a745';
                            btnRegistrar.disabled = false;
                            btnRegistrar.style.opacity = '1';
                            document.getElementById('valorX').style.borderColor = '#28a745';
                            return true;
                        }
                    } else {
                        // Es el primer registro
                        esPrimerRegistro = true;
                        ultimoValorX = null;
                        if (!isNaN(valorX) && valorX >= 0) {
                            errorValorX.style.display = 'none';
                            ayudaValorX.style.display = 'block';
                            ayudaValorX.innerHTML = '<i class="fas fa-info-circle"></i> Primer registro para este activo.';
                            ayudaValorX.style.color = '#17a2b8';
                            btnRegistrar.disabled = false;
                            btnRegistrar.style.opacity = '1';
                            document.getElementById('valorX').style.borderColor = '#28a745';
                            return true;
                        }
                    }
                    
                    // Resetear estilos si no hay valor
                    if (isNaN(valorX) || valorX === '') {
                        errorValorX.style.display = 'none';
                        ayudaValorX.style.display = 'block';
                        ayudaValorX.innerHTML = 'Ingrese el valor de la variable independiente (tiempo, uso, ciclos, etc.)';
                        ayudaValorX.style.color = '#666';
                        document.getElementById('valorX').style.borderColor = '#ddd';
                        btnRegistrar.disabled = false;
                        btnRegistrar.style.opacity = '1';
                    }
                    
                    return true;
                }

                function validarEstadoActivo() {
                    const valorY = document.getElementById('valorY').value;
                    const errorValorY = document.getElementById('errorValorY');
                    const ayudaValorY = document.getElementById('ayudaValorY');
                    const btnRegistrar = document.getElementById('btnRegistrar');
                    
                    // Obtener el activo seleccionado y sus datos existentes
                    const activoId = parseInt(document.getElementById('selectActivo').value);
                    if (!activoId) return;
                    
                    const datosActivoExistentes = datosRegresionExistentes.filter(d => d.id_activo === activoId);
                    
                    if (datosActivoExistentes.length > 0) {
                        // No es el primer registro
                        esPrimerRegistro = false;
                        
                        if (valorY === '1') {
                            errorValorY.innerHTML = '<i class="fas fa-ban"></i> El estado "Nuevo" solo puede seleccionarse en el primer registro del activo.';
                            errorValorY.style.display = 'block';
                            ayudaValorY.style.display = 'none';
                            btnRegistrar.disabled = true;
                            btnRegistrar.style.opacity = '0.6';
                            document.getElementById('valorY').style.borderColor = '#dc3545';
                            
                            // Desmarcar la opción "Nuevo"
                            document.getElementById('valorY').value = '';
                            return false;
                        } else if (valorY !== '') {
                            errorValorY.style.display = 'none';
                            ayudaValorY.style.display = 'block';
                            ayudaValorY.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Estado válido para registro posterior.';
                            ayudaValorY.style.color = '#28a745';
                            btnRegistrar.disabled = false;
                            btnRegistrar.style.opacity = '1';
                            document.getElementById('valorY').style.borderColor = '#28a745';
                            return true;
                        }
                    } else {
                        // Es el primer registro
                        esPrimerRegistro = true;
                        if (valorY !== '') {
                            errorValorY.style.display = 'none';
                            ayudaValorY.style.display = 'block';
                            if (valorY === '1') {
                                ayudaValorY.innerHTML = '<i class="fas fa-star" style="color: #ffc107;"></i> Primer registro: Estado "Nuevo" seleccionado.';
                                ayudaValorY.style.color = '#ffc107';
                            } else {
                                ayudaValorY.innerHTML = '<i class="fas fa-info-circle"></i> Primer registro con estado inicial.';
                                ayudaValorY.style.color = '#17a2b8';
                            }
                            btnRegistrar.disabled = false;
                            btnRegistrar.style.opacity = '1';
                            document.getElementById('valorY').style.borderColor = '#28a745';
                            return true;
                        }
                    }
                    
                    // Resetear estilos si no hay selección
                    if (valorY === '') {
                        errorValorY.style.display = 'none';
                        ayudaValorY.style.display = 'block';
                        ayudaValorY.innerHTML = 'Seleccione el estado actual del activo';
                        ayudaValorY.style.color = '#666';
                        document.getElementById('valorY').style.borderColor = '#ddd';
                        btnRegistrar.disabled = false;
                        btnRegistrar.style.opacity = '1';
                    }
                    
                    return true;
                }

                // Modificar la función mostrarDetallesActivo para incluir las validaciones
                function mostrarDetallesActivoConValidaciones() {
                    const selectActivo = document.getElementById('selectActivo');
                    const detallesActivo = document.getElementById('detallesActivo');
                    const infoActivo = document.getElementById('infoActivo');
                    const formRegresion = document.getElementById('formRegresion');
                    const opcionNuevo = document.getElementById('opcionNuevo');
                    
                    const activoId = parseInt(selectActivo.value);
                    
                    if (activoId) {
                        const activo = activos.find(a => a.id === activoId);
                        const datosActivoExistentes = datosRegresionExistentes.filter(d => d.id_activo === activoId);
                        
                        if (activo) {
                            infoActivo.innerHTML = `
                                <p><strong>Nombre:</strong> ${activo.nombre}</p>
                                <p><strong>Código de Barras:</strong> ${activo.codigoBarras}</p>
                                <p><strong>Descripción:</strong> ${activo.descripcion}</p>
                                <p><strong>Categoría:</strong> ${activo.categoria}</p>
                                <p><strong>Sitio:</strong> ${activo.sitio}</p>
                                <p><strong>Registros existentes:</strong> ${datosActivoExistentes.length}</p>
                            `;
                            
                            // Configurar validaciones según si es primer registro o no
                            if (datosActivoExistentes.length > 0) {
                                esPrimerRegistro = false;
                                ultimoValorX = Math.max(...datosActivoExistentes.map(d => d.valor_x));
                                
                                // Deshabilitar opción "Nuevo"
                                opcionNuevo.disabled = true;
                                opcionNuevo.style.color = '#ccc';
                                opcionNuevo.innerHTML = 'Nuevo (Solo primer registro)';
                                
                                // Actualizar placeholder y ayuda
                                document.getElementById('valorX').placeholder = `Ingrese valor mayor a ${ultimoValorX} meses`;
                                document.getElementById('ayudaValorX').innerHTML = `<i class="fas fa-info-circle"></i> Debe ser mayor al último registro: ${ultimoValorX} meses.`;
                                document.getElementById('ayudaValorX').style.color = '#17a2b8';
                                
                            } else {
                                esPrimerRegistro = true;
                                ultimoValorX = null;
                                
                                // Habilitar opción "Nuevo"
                                opcionNuevo.disabled = false;
                                opcionNuevo.style.color = '';
                                opcionNuevo.innerHTML = 'Nuevo';
                                
                                // Resetear placeholder y ayuda
                                document.getElementById('valorX').placeholder = 'Escriba cuantos meses de uso tuvo hasta ahora.';
                                document.getElementById('ayudaValorX').innerHTML = '<i class="fas fa-star" style="color: #ffc107;"></i> Primer registro para este activo. Puede comenzar desde 0.';
                                document.getElementById('ayudaValorX').style.color = '#ffc107';
                            }
                            
                            detallesActivo.style.display = 'block';
                            formRegresion.style.display = 'block';
                            
                            // Limpiar campos y errores
                            document.getElementById('valorX').value = '';
                            document.getElementById('valorY').value = '';
                            document.getElementById('observaciones').value = '';
                            document.getElementById('errorValorX').style.display = 'none';
                            document.getElementById('errorValorY').style.display = 'none';
                            document.getElementById('valorX').style.borderColor = '#ddd';
                            document.getElementById('valorY').style.borderColor = '#ddd';
                        }
                    } else {
                        detallesActivo.style.display = 'none';
                        formRegresion.style.display = 'none';
                    }
                }

                // Sobrescribir la función original
                function mostrarDetallesActivo() {
                    mostrarDetallesActivoConValidaciones();
                }

                // Modificar el evento de envío del formulario para incluir validaciones
                document.getElementById('formRegresion').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const activoId = document.getElementById('selectActivo').value;
                    const valorX = document.getElementById('valorX').value;
                    const valorY = document.getElementById('valorY').value;
                    const observaciones = document.getElementById('observaciones').value;
                    const mensajeResultado = document.getElementById('mensajeResultado');
                    
                    // Validaciones básicas
                    if (!activoId) {
                        mostrarMensaje('Por favor seleccione un activo.', 'error');
                        return;
                    }
                    
                    if (!valorX || !valorY) {
                        mostrarMensaje('Por favor complete todos los campos requeridos.', 'error');
                        return;
                    }
                    
                    // Ejecutar validaciones específicas
                    const validacionX = validarMesesUso();
                    const validacionY = validarEstadoActivo();
                    
                    if (!validacionX || !validacionY) {
                        mostrarMensaje('Por favor corrija los errores señalados antes de continuar.', 'error');
                        return;
                    }
                    
                    // Verificar que el botón no esté deshabilitado
                    if (document.getElementById('btnRegistrar').disabled) {
                        mostrarMensaje('No se puede registrar debido a errores de validación.', 'error');
                        return;
                    }
                    
                    // Simular el registro de datos
                    const nuevoDato = {
                        id: datosRegresionExistentes.length + 1,
                        id_activo: parseInt(activoId),
                        valor_x: parseFloat(valorX),
                        valor_y: parseInt(valorY),
                        fecha_registro: new Date().toISOString(),
                        observaciones: observaciones
                    };
                    
                    datosRegresionExistentes.push(nuevoDato);
                    
                    mostrarMensaje('Datos de regresión registrados exitosamente.', 'success');
                    
                    // Limpiar formulario pero mantener el activo seleccionado
                    document.getElementById('valorX').value = '';
                    document.getElementById('valorY').value = '';
                    document.getElementById('observaciones').value = '';
                    
                    // Actualizar validaciones para el próximo registro
                    mostrarDetallesActivoConValidaciones();
                    
                    // Actualizar la vista de datos si está viendo el mismo activo
                    const selectActivoVer = document.getElementById('selectActivoVer');
                    if (selectActivoVer && selectActivoVer.value == activoId) {
                        cargarDatosRegresion();
                    }
                });

                // Función para limpiar formulario actualizada
                function limpiarFormularioValidado() {
                    document.getElementById('selectActivo').value = '';
                    document.getElementById('valorX').value = '';
                    document.getElementById('valorY').value = '';
                    document.getElementById('observaciones').value = '';
                    document.getElementById('detallesActivo').style.display = 'none';
                    document.getElementById('formRegresion').style.display = 'none';
                    document.getElementById('mensajeResultado').innerHTML = '';
                    
                    // Resetear validaciones
                    document.getElementById('errorValorX').style.display = 'none';
                    document.getElementById('errorValorY').style.display = 'none';
                    document.getElementById('ayudaValorX').innerHTML = 'Ingrese el valor de la variable independiente (tiempo, uso, ciclos, etc.)';
                    document.getElementById('ayudaValorX').style.color = '#666';
                    document.getElementById('ayudaValorY').innerHTML = 'Seleccione el estado actual del activo';
                    document.getElementById('ayudaValorY').style.color = '#666';
                    document.getElementById('valorX').style.borderColor = '#ddd';
                    document.getElementById('valorY').style.borderColor = '#ddd';
                    
                    // Rehabilitar botón y opción "Nuevo"
                    document.getElementById('btnRegistrar').disabled = false;
                    document.getElementById('btnRegistrar').style.opacity = '1';
                    document.getElementById('opcionNuevo').disabled = false;
                    document.getElementById('opcionNuevo').style.color = '';
                    document.getElementById('opcionNuevo').innerHTML = 'Nuevo';
                    
                    esPrimerRegistro = true;
                    ultimoValorX = null;
                }

                // Sobrescribir la función original de limpiar
                function limpiarFormulario() {
                    limpiarFormularioValidado();
                }
                </script>

                <div id="mensajeResultado"></div>
            </div>

            <!-- Panel para visualizar datos existentes -->
            <div class="panel">
                <h3><i class="fas fa-table"></i> Datos de Regresión Existentes</h3>
                
                <div class="form-group">
                    <label for="selectActivoVer">Ver datos del activo:</label>
                    <select id="selectActivoVer" class="form-control" onchange="cargarDatosRegresion()">
                        <option value="">-- Seleccione un activo --</option>
                    </select>
                </div>

                <div id="tablaRegresion" style="display: none;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <thead>
                                <tr style="background-color: #001e3c; color: white;">
                                    <th style="padding: 10px; border: 1px solid #ddd;">Fecha Registro</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Variable X</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Estado (Y)</th>
                                    <th style="padding: 10px; border: 1px solid #ddd;">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTablaRegresion">
                                <!-- Los datos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="mensajeSinDatos" style="display: none; text-align: center; color: #666; padding: 20px;">
                    No hay datos de regresión para este activo.
                </div>
            </div>

            
            <!-- Panel para análisis de regresión lineal -->
            <div class="panel">
                <h3><i class="fas fa-chart-line"></i> Análisis de Regresión Lineal Simple</h3>
                
                <div class="form-group">
                    <label for="selectActivoRegresion">Seleccionar Activo para Análisis:</label>
                    <select id="selectActivoRegresion" class="form-control" onchange="verificarDatosRegresion()">
                        <option value="">-- Seleccione un activo --</option>
                    </select>
                </div>

                <!-- Información del activo seleccionado -->
                <div id="infoActivoRegresion" style="display: none; margin-bottom: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h4 style="color: #155724; margin-bottom: 10px;">Información del Activo</h4>
                    <div id="detalleActivoRegresion"></div>
                    <div id="resumenDatos" style="margin-top: 10px; font-weight: bold; color: #155724;"></div>
                </div>

                <!-- Botón para realizar análisis -->
                <div id="botonAnalisis" style="display: none; text-align: center; margin: 20px 0;">
                    <button type="button" class="btn btn-primary" onclick="realizarRegresionLineal()" style="padding: 15px 30px; font-size: 16px;">
                        <i class="fas fa-calculator"></i> Realizar Análisis de Regresión
                    </button>
                </div>

                <!-- Resultados del análisis -->
                <div id="resultadosRegresion" style="display: none;">
                    <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-top: 20px;">
                        <h4 style="color: #001e3c; margin-bottom: 15px; border-bottom: 2px solid #FFD700; padding-bottom: 8px;">
                            <i class="fas fa-analytics"></i> Resultados del Análisis
                        </h4>
                        
                        <!-- Ecuación de regresión -->
                        <div id="ecuacionRegresion" style="background-color: #e3f2fd; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                            <h5 style="color: #1976d2; margin-bottom: 10px;">Ecuación de Regresión:</h5>
                            <div id="formulaRegresion" style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; color: #333;"></div>
                        </div>

                        <!-- Estadísticas -->
                        <div id="estadisticasRegresion" style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                            <h5 style="color: #856404; margin-bottom: 10px;">Estadísticas del Modelo:</h5>
                            <div id="statsRegresion"></div>
                        </div>

                        <!-- Predicción -->
                        <div id="prediccionRegresion" style="background-color: #d4edda; padding: 20px; border-radius: 6px; border-left: 4px solid #28a745;">
                            <h5 style="color: #155724; margin-bottom: 15px;">
                                <i class="fas fa-crystal-ball"></i> Predicción para la Siguiente Gestión:
                            </h5>
                            <div id="resultadoPrediccion" style="font-size: 16px; font-weight: bold;"></div>
                            <div id="conclusionPrediccion" style="margin-top: 15px; padding: 15px; background-color: #155724; color: white; border-radius: 6px;">
                                <h6 style="margin-bottom: 10px; color: #fff;">Conclusión y Recomendaciones:</h6>
                                <div id="textoConclusion"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico visual (opcional) -->
                <div id="graficoRegresion" style="display: none; margin-top: 20px;">
                    <h4 style="color: #001e3c; margin-bottom: 15px;">Visualización de Datos y Tendencia</h4>
                    <canvas id="canvasRegresion" width="600" height="400" style="border: 1px solid #ddd; border-radius: 6px; background-color: white; width: 100%; max-width: 600px;"></canvas>
                </div>

                <div id="mensajeErrorRegresion"></div>
            </div>
        </div>
    </div>

    <script>
        // Datos simulados de activos (en una implementación real, estos vendrían de la base de datos)
        const activos = [
            { id: 1, nombre: 'Balón de Fútbol', codigoBarras: 'DEP001', descripcion: 'Balón de fútbol tamaño oficial marca Adidas', categoria: 'Deporte', sitio: 'Aula 101' },
            { id: 2, nombre: 'Libro de Matemáticas', codigoBarras: 'LIB002', descripcion: 'Libro de álgebra avanzada, edición 2024', categoria: 'Libro', sitio: 'Laboratorio de Computación' },
            { id: 3, nombre: 'Silla de Oficina', codigoBarras: 'MOB003', descripcion: 'Silla ergonómica con respaldo ajustable', categoria: 'Mobiliario', sitio: 'Laboratorio de Computación' },
            { id: 4, nombre: 'Raqueta de Tenis', codigoBarras: 'DEP004', descripcion: 'Raqueta profesional marca Wilson', categoria: 'Deporte', sitio: 'Almacen 1' },
            { id: 5, nombre: 'Enciclopedia', codigoBarras: 'LIB005', descripcion: 'Enciclopedia universal, 10 volúmenes', categoria: 'Libro', sitio: 'Biblioteca' },
            { id: 6, nombre: 'Mesa de Reuniones', codigoBarras: 'MOB006', descripcion: 'Mesa rectangular para 8 personas', categoria: 'Mobiliario', sitio: 'Aula 202' },
            { id: 7, nombre: 'Balón de Baloncesto', codigoBarras: 'DEP007', descripcion: 'Balón oficial NBA, tamaño 7', categoria: 'Deporte', sitio: 'Aula 101' },
            { id: 8, nombre: 'Diccionario Inglés-Español', codigoBarras: 'LIB008', descripcion: 'Edición especial con ejemplos de uso', categoria: 'Libro', sitio: 'Aula 101' },
            { id: 9, nombre: 'Estantería Metálica', codigoBarras: 'MOB009', descripcion: 'Estantería de 5 niveles, color negro', categoria: 'Mobiliario', sitio: 'Almacen 1' },
            { id: 10, nombre: 'Kit de Ping Pong', codigoBarras: 'DEP010', descripcion: 'Incluye 2 raquetas, red y 3 pelotas', categoria: 'Deporte', sitio: 'Aula 202' },
            { id: 21, nombre: 'Monitor 24"', codigoBarras: 'TEC011', descripcion: 'Monitor LED Full HD', categoria: 'Tecnología', sitio: 'Laboratorio de Computación' },
            { id: 22, nombre: 'Teclado inalámbrico', codigoBarras: 'TEC012', descripcion: 'Teclado ergonómico con conexión Bluetooth', categoria: 'Tecnología', sitio: 'Aula 101' },
            { id: 23, nombre: 'Impresora láser', codigoBarras: 'TEC013', descripcion: 'Impresora multifunción a color', categoria: 'Tecnología', sitio: 'Aula 202' },
            { id: 24, nombre: 'Router WiFi', codigoBarras: 'TEC014', descripcion: 'Router dual band 300Mbps', categoria: 'Tecnología', sitio: 'Biblioteca' },
            { id: 25, nombre: 'Disco duro externo', codigoBarras: 'TEC015', descripcion: '1TB USB 3.0', categoria: 'Tecnología', sitio: 'Almacen 1' },
            { id: 26, nombre: 'Silla', codigoBarras: '', descripcion: 'de madera', categoria: 'Mobiliario', sitio: 'Aula 101' },
            { id: 27, nombre: 'Libro de Matemáticas Avanzadas 1', codigoBarras: 'LIB101', descripcion: 'Libro de álgebra y cálculo avanzado, edición 2024.', categoria: 'Libro', sitio: 'Biblioteca' },
            { id: 28, nombre: 'Libro de Matemáticas Aplicadas', codigoBarras: 'LIB102', descripcion: 'Aplicaciones prácticas de matemáticas en ingeniería.', categoria: 'Libro', sitio: 'Aula 101' },
            { id: 29, nombre: 'Historia Universal I', codigoBarras: 'LIB103', descripcion: 'Introducción a la historia mundial antigua.', categoria: 'Libro', sitio: 'Laboratorio de Computación' },
            { id: 30, nombre: 'Historia Mundial II', codigoBarras: 'LIB104', descripcion: 'Historia mundial moderna y contemporánea.', categoria: 'Libro', sitio: 'Laboratorio de Computación' },
            { id: 31, nombre: 'Biología Molecular', codigoBarras: 'LIB105', descripcion: 'Fundamentos de biología molecular y genética.', categoria: 'Libro', sitio: 'Aula 101' },
            { id: 32, nombre: 'Química Orgánica', codigoBarras: 'LIB106', descripcion: 'Principios y aplicaciones de la química orgánica.', categoria: 'Libro', sitio: 'Biblioteca' },
            { id: 33, nombre: 'Física Cuántica', codigoBarras: 'LIB107', descripcion: 'Conceptos básicos de la física cuántica.', categoria: 'Libro', sitio: 'Aula 101' },
            { id: 34, nombre: 'Literatura Española', codigoBarras: 'LIB108', descripcion: 'Obras clásicas y modernas de la literatura española...', categoria: 'Libro', sitio: 'Almacen 1' },
            { id: 35, nombre: 'Literatura Indiesa', codigoBarras: 'LIB109', descripcion: 'Principales autores y obras de la literatura indie', categoria: 'Libro', sitio: 'Almacen 1' }
        ];

        const datosRegresionExistentes = [
            { id: 1, id_activo: 1, valor_x: 0.00, valor_y: 1, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Junio 2025: El activo apenas se compro' },
            { id: 2, id_activo: 1, valor_x: 1.00, valor_y: 2, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Julio 2025: Esta siendo usado con normalidad, presenta signos de desgaste, pero leves' },
            { id: 3, id_activo: 1, valor_x: 1.00, valor_y: 3, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Agosto 2025: El balon se reventó, tiene solución' },
            { id: 4, id_activo: 1, valor_x: 1.00, valor_y: 4, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Septiembre 2025: Está siendo parchado' },
            { id: 5, id_activo: 1, valor_x: 2.00, valor_y: 2, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Octubre 2025: Despues de su reparcion continua siendo usado de manera normal' },
            { id: 6, id_activo: 1, valor_x: 3.00, valor_y: 2, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Noviembre 2025: Sigue siendo usado con normalidad, pero ya presenta signos de desgaste avanzados' },
            { id: 7, id_activo: 1, valor_x: 4.00, valor_y: 3, fecha_registro: '2025-06-01 02:43:04', observaciones: 'Diciembre 2025: En los ultimos dias de clase el balon presento desgaste bastante avanzado' }
        ];


        const estadosActivos = {
            1: 'Nuevo',
            2: 'Usado', 
            3: 'Dañado',
            4: 'En Reparación',
            5: 'Necesita renovación'
        };

        // Cargar activos en los select al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarActivos();
        });

        function cargarActivos() {
            const selectActivo = document.getElementById('selectActivo');
            const selectActivoVer = document.getElementById('selectActivoVer');
            
            // Limpiar opciones existentes
            selectActivo.innerHTML = '<option value="">-- Seleccione un activo --</option>';
            selectActivoVer.innerHTML = '<option value="">-- Seleccione un activo --</option>';
            
            // Agregar activos a ambos select
            activos.forEach(activo => {
                const option1 = document.createElement('option');
                option1.value = activo.id;
                option1.textContent = `${activo.nombre} (${activo.codigoBarras})`;
                selectActivo.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = activo.id;
                option2.textContent = `${activo.nombre} (${activo.codigoBarras})`;
                selectActivoVer.appendChild(option2);
            });
        }

        function mostrarDetallesActivo() {
            const selectActivo = document.getElementById('selectActivo');
            const detallesActivo = document.getElementById('detallesActivo');
            const infoActivo = document.getElementById('infoActivo');
            const formRegresion = document.getElementById('formRegresion');
            
            const activoId = parseInt(selectActivo.value);
            
            if (activoId) {
                const activo = activos.find(a => a.id === activoId);
                if (activo) {
                    infoActivo.innerHTML = `
                        <p><strong>Nombre:</strong> ${activo.nombre}</p>
                        <p><strong>Código de Barras:</strong> ${activo.codigoBarras}</p>
                        <p><strong>Descripción:</strong> ${activo.descripcion}</p>
                        <p><strong>Categoría:</strong> ${activo.categoria}</p>
                        <p><strong>Sitio:</strong> ${activo.sitio}</p>
                    `;
                    detallesActivo.style.display = 'block';
                    formRegresion.style.display = 'block';
                }
            } else {
                detallesActivo.style.display = 'none';
                formRegresion.style.display = 'none';
            }
        }

        function cargarDatosRegresion() {
            const selectActivoVer = document.getElementById('selectActivoVer');
            const tablaRegresion = document.getElementById('tablaRegresion');
            const cuerpoTabla = document.getElementById('cuerpoTablaRegresion');
            const mensajeSinDatos = document.getElementById('mensajeSinDatos');
            
            const activoId = parseInt(selectActivoVer.value);
            
            if (activoId) {
                const datosActivo = datosRegresionExistentes.filter(d => d.id_activo === activoId);
                
                if (datosActivo.length > 0) {
                    cuerpoTabla.innerHTML = '';
                    datosActivo.forEach(dato => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td style="padding: 8px; border: 1px solid #ddd;">${new Date(dato.fecha_registro).toLocaleString()}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${dato.valor_x}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${estadosActivos[dato.valor_y]}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${dato.observaciones || '-'}</td>
                        `;
                        cuerpoTabla.appendChild(fila);
                    });
                    tablaRegresion.style.display = 'block';
                    mensajeSinDatos.style.display = 'none';
                } else {
                    tablaRegresion.style.display = 'none';
                    mensajeSinDatos.style.display = 'block';
                }
            } else {
                tablaRegresion.style.display = 'none';
                mensajeSinDatos.style.display = 'none';
            }
        }

        document.getElementById('formRegresion').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const activoId = document.getElementById('selectActivo').value;
            const valorX = document.getElementById('valorX').value;
            const valorY = document.getElementById('valorY').value;
            const observaciones = document.getElementById('observaciones').value;
            const mensajeResultado = document.getElementById('mensajeResultado');
            
            // Validaciones
            if (!activoId) {
                mostrarMensaje('Por favor seleccione un activo.', 'error');
                return;
            }
            
            if (!valorX || !valorY) {
                mostrarMensaje('Por favor complete todos los campos requeridos.', 'error');
                return;
            }
            
            // Simular el registro de datos (aquí harías la llamada al servidor)
            const nuevoDato = {
                id: datosRegresionExistentes.length + 1,
                id_activo: parseInt(activoId),
                valor_x: parseFloat(valorX),
                valor_y: parseInt(valorY),
                fecha_registro: new Date().toISOString(),
                observaciones: observaciones
            };
            
            datosRegresionExistentes.push(nuevoDato);
            
            mostrarMensaje('Datos de regresión registrados exitosamente.', 'success');
            
            // Limpiar formulario
            document.getElementById('valorX').value = '';
            document.getElementById('valorY').value = '';
            document.getElementById('observaciones').value = '';
            
            // Actualizar la vista de datos si está viendo el mismo activo
            const selectActivoVer = document.getElementById('selectActivoVer');
            if (selectActivoVer.value == activoId) {
                cargarDatosRegresion();
            }
        });

        function limpiarFormulario() {
            document.getElementById('selectActivo').value = '';
            document.getElementById('valorX').value = '';
            document.getElementById('valorY').value = '';
            document.getElementById('observaciones').value = '';
            document.getElementById('detallesActivo').style.display = 'none';
            document.getElementById('formRegresion').style.display = 'none';
            document.getElementById('mensajeResultado').innerHTML = '';
        }

        function mostrarMensaje(mensaje, tipo) {
            const mensajeResultado = document.getElementById('mensajeResultado');
            const clase = tipo === 'success' ? 'success-message' : 'error-message';
            mensajeResultado.innerHTML = `<div class="${clase}">${mensaje}</div>`;
            
            // Ocultar mensaje después de 5 segundos
            setTimeout(() => {
                mensajeResultado.innerHTML = '';
            }, 5000);
        }
    </script>
    <script>
    // Cargar activos en el select de regresión
    document.addEventListener('DOMContentLoaded', function() {
        cargarActivosRegresion();
    });

    function cargarActivosRegresion() {
        const selectActivoRegresion = document.getElementById('selectActivoRegresion');
        selectActivoRegresion.innerHTML = '<option value="">-- Seleccione un activo --</option>';
        
        activos.forEach(activo => {
            const option = document.createElement('option');
            option.value = activo.id;
            option.textContent = `${activo.nombre} (${activo.codigoBarras})`;
            selectActivoRegresion.appendChild(option);
        });
    }

    function verificarDatosRegresion() {
        const selectActivoRegresion = document.getElementById('selectActivoRegresion');
        const infoActivoRegresion = document.getElementById('infoActivoRegresion');
        const detalleActivoRegresion = document.getElementById('detalleActivoRegresion');
        const resumenDatos = document.getElementById('resumenDatos');
        const botonAnalisis = document.getElementById('botonAnalisis');
        const resultadosRegresion = document.getElementById('resultadosRegresion');
        const graficoRegresion = document.getElementById('graficoRegresion');
        
        const activoId = parseInt(selectActivoRegresion.value);
        
        if (activoId) {
            const activo = activos.find(a => a.id === activoId);
            const datosActivo = datosRegresionExistentes.filter(d => d.id_activo === activoId);
            
            if (activo && datosActivo.length >= 2) {
                detalleActivoRegresion.innerHTML = `
                    <p><strong>Nombre:</strong> ${activo.nombre}</p>
                    <p><strong>Código:</strong> ${activo.codigoBarras}</p>
                    <p><strong>Categoría:</strong> ${activo.categoria}</p>
                    <p><strong>Ubicación:</strong> ${activo.sitio}</p>
                `;
                
                resumenDatos.innerHTML = `Datos disponibles: ${datosActivo.length} registros | Rango X: ${Math.min(...datosActivo.map(d => d.valor_x))} - ${Math.max(...datosActivo.map(d => d.valor_x))} | Estados: ${Math.min(...datosActivo.map(d => d.valor_y))} - ${Math.max(...datosActivo.map(d => d.valor_y))}`;
                
                infoActivoRegresion.style.display = 'block';
                botonAnalisis.style.display = 'block';
            } else if (datosActivo.length < 2) {
                mostrarMensajeErrorRegresion('Se necesitan al menos 2 registros de datos para realizar el análisis de regresión.', 'error');
                infoActivoRegresion.style.display = 'none';
                botonAnalisis.style.display = 'none';
            }
            
            // Ocultar resultados anteriores
            resultadosRegresion.style.display = 'none';
            graficoRegresion.style.display = 'none';
        } else {
            infoActivoRegresion.style.display = 'none';
            botonAnalisis.style.display = 'none';
            resultadosRegresion.style.display = 'none';
            graficoRegresion.style.display = 'none';
        }
    }

    function realizarRegresionLineal() {
        const activoId = parseInt(document.getElementById('selectActivoRegresion').value);
        const datosActivo = datosRegresionExistentes.filter(d => d.id_activo === activoId);
        
        if (datosActivo.length < 2) {
            mostrarMensajeErrorRegresion('Se necesitan al menos 2 registros para realizar el análisis.', 'error');
            return;
        }
        
        // Extraer valores X e Y
        const valoresX = datosActivo.map(d => d.valor_x);
        const valoresY = datosActivo.map(d => d.valor_y);
        const n = datosActivo.length;
        
        // Calcular estadísticas básicas
        const sumaX = valoresX.reduce((a, b) => a + b, 0);
        const sumaY = valoresY.reduce((a, b) => a + b, 0);
        const sumaXY = valoresX.reduce((sum, x, i) => sum + (x * valoresY[i]), 0);
        const sumaX2 = valoresX.reduce((sum, x) => sum + (x * x), 0);
        const sumaY2 = valoresY.reduce((sum, y) => sum + (y * y), 0);
        
        const mediaX = sumaX / n;
        const mediaY = sumaY / n;
        
        // Calcular coeficientes de regresión
        const b1 = (n * sumaXY - sumaX * sumaY) / (n * sumaX2 - sumaX * sumaX);
        const b0 = mediaY - b1 * mediaX;
        
        // Calcular coeficiente de correlación (R)
        const numeradorR = n * sumaXY - sumaX * sumaY;
        const denominadorR = Math.sqrt((n * sumaX2 - sumaX * sumaX) * (n * sumaY2 - sumaY * sumaY));
        const r = numeradorR / denominadorR;
        const r2 = r * r;
        
        // Calcular predicción para el próximo período
        const maxX = Math.max(...valoresX);
        const siguienteX = maxX + 6; // Asumiendo próximos 6 meses
        const prediccionY = b0 + b1 * siguienteX;
        
        // Determinar estado predicho
        let estadoPredicho;
        let colorEstado;
        let recomendacion;
        
        const estadoRedondeado = Math.round(Math.max(1, Math.min(5, prediccionY)));
        
        switch(estadoRedondeado) {
            case 1:
                estadoPredicho = "Nuevo";
                colorEstado = "#28a745";
                recomendacion = "El activo se mantendrá en excelente estado. Continuar con el mantenimiento preventivo regular.";
                break;
            case 2:
                estadoPredicho = "Usado";
                colorEstado = "#17a2b8";
                recomendacion = "El activo mostrará signos normales de uso. Monitorear regularmente y realizar mantenimiento según cronograma.";
                break;
            case 3:
                estadoPredicho = "Dañado";
                colorEstado = "#ffc107";
                recomendacion = "Se prevé deterioro del activo. Programar inspección detallada y considerar reparaciones preventivas.";
                break;
            case 4:
                estadoPredicho = "En Reparación";
                colorEstado = "#fd7e14";
                recomendacion = "El activo requerirá reparaciones. Preparar presupuesto para mantenimiento correctivo y buscar respaldo temporal.";
                break;
            case 5:
                estadoPredicho = "Necesita renovación";
                colorEstado = "#dc3545";
                recomendacion = "El activo requerirá renovación o reemplazo. Iniciar proceso de adquisición de nuevo equipo.";
                break;
        }
        
        // Mostrar resultados
        document.getElementById('formulaRegresion').innerHTML = `Y = ${b0.toFixed(4)} + ${b1.toFixed(4)}X`;
        
        document.getElementById('statsRegresion').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div><strong>Coeficiente de Correlación (R):</strong> ${r.toFixed(4)}</div>
                <div><strong>Coeficiente de Determinación (R²):</strong> ${r2.toFixed(4)}</div>
                <div><strong>Pendiente (β₁):</strong> ${b1.toFixed(4)}</div>
                <div><strong>Intercepto (β₀):</strong> ${b0.toFixed(4)}</div>
                <div><strong>Número de observaciones:</strong> ${n}</div>
                <div><strong>Bondad del ajuste:</strong> ${r2 > 0.7 ? 'Buena' : r2 > 0.5 ? 'Moderada' : 'Baja'} (${(r2*100).toFixed(1)}%)</div>
            </div>
        `;
        
        document.getElementById('resultadoPrediccion').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                <div><strong>Período de predicción:</strong> ${siguienteX} meses</div>
                <div><strong>Valor predicho (Y):</strong> ${prediccionY.toFixed(2)}</div>
            </div>
            <div style="font-size: 18px; text-align: center; padding: 15px; background-color: ${colorEstado}; color: white; border-radius: 6px; margin-top: 10px;">
                <strong>Estado Predicho: ${estadoPredicho}</strong>
            </div>
        `;
        
        document.getElementById('textoConclusion').innerHTML = `
            <p><strong>Análisis de Tendencia:</strong> ${b1 > 0 ? 'El estado del activo tiende a deteriorarse con el tiempo' : 'El estado del activo se mantiene estable o mejora'} 
            (pendiente: ${b1 > 0 ? 'positiva' : 'negativa'}).</p>
            <p><strong>Confiabilidad del Modelo:</strong> ${r2 > 0.7 ? 'Alta' : r2 > 0.5 ? 'Moderada' : 'Baja'} - El modelo explica el ${(r2*100).toFixed(1)}% de la variabilidad en los datos.</p>
            <p><strong>Recomendación:</strong> ${recomendacion}</p>
            <p><strong>Próxima Evaluación:</strong> Se recomienda evaluar el activo en ${siguienteX} meses para validar la predicción.</p>
        `;
        
        // Mostrar resultados
        document.getElementById('resultadosRegresion').style.display = 'block';
        
        // Dibujar gráfico
        dibujarGraficoRegresion(valoresX, valoresY, b0, b1, siguienteX, prediccionY);
        document.getElementById('graficoRegresion').style.display = 'block';
        
        mostrarMensajeErrorRegresion('Análisis de regresión completado exitosamente.', 'success');
    }

    function dibujarGraficoRegresion(valoresX, valoresY, b0, b1, prediccionX, prediccionY) {
        const canvas = document.getElementById('canvasRegresion');
        const ctx = canvas.getContext('2d');
        
        // Limpiar canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Configurar dimensiones del gráfico
        const margen = 60;
        const ancho = canvas.width - 2 * margen;
        const alto = canvas.height - 2 * margen;
        
        // Encontrar rangos de datos
        const minX = Math.min(...valoresX, 0);
        const maxX = Math.max(...valoresX, prediccionX);
        const minY = Math.min(...valoresY, 1);
        const maxY = Math.max(...valoresY, prediccionY, 5);
        
        // Función para convertir coordenadas de datos a canvas
        function escalarX(x) {
            return margen + (x - minX) * ancho / (maxX - minX);
        }
        
        function escalarY(y) {
            return canvas.height - margen - (y - minY) * alto / (maxY - minY);
        }
        
        // Dibujar ejes
        ctx.strokeStyle = '#333';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(margen, margen);
        ctx.lineTo(margen, canvas.height - margen);
        ctx.lineTo(canvas.width - margen, canvas.height - margen);
        ctx.stroke();
        
        // Etiquetas de ejes
        ctx.fillStyle = '#333';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Tiempo (meses)', canvas.width / 2, canvas.height - 10);
        
        ctx.save();
        ctx.translate(15, canvas.height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Estado del Activo', 0, 0);
        ctx.restore();
        
        // Dibujar línea de regresión
        ctx.strokeStyle = '#007bff';
        ctx.lineWidth = 2;
        ctx.beginPath();
        const x1 = minX;
        const y1 = b0 + b1 * x1;
        const x2 = maxX;
        const y2 = b0 + b1 * x2;
        ctx.moveTo(escalarX(x1), escalarY(y1));
        ctx.lineTo(escalarX(x2), escalarY(y2));
        ctx.stroke();
        
        // Dibujar puntos de datos
        ctx.fillStyle = '#28a745';
        valoresX.forEach((x, i) => {
            ctx.beginPath();
            ctx.arc(escalarX(x), escalarY(valoresY[i]), 6, 0, 2 * Math.PI);
            ctx.fill();
        });
        
        // Dibujar punto de predicción
        ctx.fillStyle = '#dc3545';
        ctx.beginPath();
        ctx.arc(escalarX(prediccionX), escalarY(prediccionY), 8, 0, 2 * Math.PI);
        ctx.fill();
        
        // Etiqueta de predicción
        ctx.fillStyle = '#dc3545';
        ctx.font = 'bold 12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Predicción', escalarX(prediccionX), escalarY(prediccionY) - 15);
        
        // Leyenda
        ctx.fillStyle = '#333';
        ctx.font = '10px Arial';
        ctx.textAlign = 'left';
        ctx.fillText('● Datos reales', margen, 30);
        ctx.fillStyle = '#dc3545';
        ctx.fillText('● Predicción', margen + 100, 30);
        ctx.strokeStyle = '#007bff';
        ctx.beginPath();
        ctx.moveTo(margen + 200, 27);
        ctx.lineTo(margen + 250, 27);
        ctx.stroke();
        ctx.fillStyle = '#333';
        ctx.fillText('— Línea de regresión', margen + 260, 30);
    }

    function mostrarMensajeErrorRegresion(mensaje, tipo) {
        const mensajeError = document.getElementById('mensajeErrorRegresion');
        const clase = tipo === 'success' ? 'success-message' : 'error-message';
        mensajeError.innerHTML = `<div class="${clase}">${mensaje}</div>`;
        
        setTimeout(() => {
            mensajeError.innerHTML = '';
        }, 5000);
    }
    </script>
</main>
    

    <footer>
        <p>© 2025 Luz a las Naciones</p>
    </footer>

    
</body>
</html>