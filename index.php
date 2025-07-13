<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de Padrón de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        .firma-true {
            background-color: #d4edda !important;
        }
        .firma-false {
            background-color: #f8d7da !important;
        }
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Menú superior común y responsivo -->
        <?php include_once('util/menu.php'); ?>
        <!-- ...existing code... -->
        <h1 class="text-center mb-4">Padrón de Sindicato SOMUVES</h1>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por apellido o nombre...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">Buscar</button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-success" id="addPersonBtn">Agregar Persona</button>
                <button class="btn btn-primary" id="exportExcelBtn">Exportar a Excel</button>
            </div>
        </div>

        <div class="table-container">
            <table id="personasTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- Modal para agregar/editar persona -->
        <div class="modal fade" id="personModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Agregar Persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="personForm">
                            <input type="hidden" id="personId">
                            <div class="mb-3">
                                <label for="paterno" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="paterno" required>
                            </div>
                            <div class="mb-3">
                                <label for="materno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="materno" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre(s)</label>
                                <input type="text" class="form-control" id="nombre" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="savePersonBtn">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            const table = $('#personasTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "paterno" },
                    { "data": "materno" },
                    { "data": "nombre" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}">Editar</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Eliminar</button>
                                
                            `;
                        }
                    }
                ]
            });

            // Cargar datos iniciales
            loadPersonas();

            // Función para cargar personas desde la API
            function loadPersonas() {
                $.ajax({
                    url: 'api/personas.php?action=getAll',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            table.clear().rows.add(response.data).draw();
                        } else {
                            alert('Error al cargar los datos: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la conexión: ' + error);
                    }
                });
            }

            // Abrir modal para agregar persona
            $('#addPersonBtn').click(function() {
                $('#modalTitle').text('Agregar Persona');
                $('#personForm')[0].reset();
                $('#personId').val('');
                $('#personModal').modal('show');
            });

            // Guardar persona (agregar o editar)
            $('#savePersonBtn').click(function() {
                const formData = {
                    id: $('#personId').val(),
                    paterno: $('#paterno').val(),
                    materno: $('#materno').val(),
                    nombre: $('#nombre').val()
                };

                const action = formData.id ? 'update' : 'create';
                
                $.ajax({
                    url: `api/personas.php?action=${action}`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#personModal').modal('hide');
                            loadPersonas();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la conexión: ' + error);
                    }
                });
            });

            // Editar persona
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                
                $.ajax({
                    url: `api/personas.php?action=getOne&id=${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#modalTitle').text('Editar Persona');
                            $('#personId').val(response.data.id);
                            $('#paterno').val(response.data.paterno);
                            $('#materno').val(response.data.materno);
                            $('#nombre').val(response.data.nombre);
                            $('#personModal').modal('show');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la conexión: ' + error);
                    }
                });
            });

            // Eliminar persona
            $(document).on('click', '.delete-btn', function() {
                if (confirm('¿Está seguro de eliminar esta persona?')) {
                    const id = $(this).data('id');
                    
                    $.ajax({
                        url: `api/personas.php?action=delete&id=${id}`,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                loadPersonas();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error en la conexión: ' + error);
                        }
                    });
                }
            });

            // Buscar personas
            $('#searchBtn').click(function() {
                const searchTerm = $('#searchInput').val().trim();
                
                $.ajax({
                    url: `api/personas.php?action=search&term=${searchTerm}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            table.clear().rows.add(response.data).draw();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la conexión: ' + error);
                    }
                });
            });

            // Exportar a Excel
            $('#exportExcelBtn').click(function() {
                $.ajax({
                    url: 'api/personas.php?action=getAll',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            exportToExcel(response.data);
                        } else {
                            alert('Error al exportar: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error en la conexión: ' + error);
                    }
                });
            });


            function exportToExcel(data) {
                // Obtener fecha actual formateada
                const fechaActual = new Date();
                const opcionesFecha = { day: '2-digit', month: 'long', year: 'numeric' };
                const fechaFormateada = fechaActual.toLocaleDateString('es-ES', opcionesFecha);
                
                // Crear libro de trabajo
                const wb = XLSX.utils.book_new();
                
                // Preparar los datos para Excel
                const excelData = [
                    // Fila 1: Título
                    
                    // Fila 2: Vacía
                    [""],
                    ["PADRON ACTUALIZADO PARA ASISTENCIA DE MARCHA DE FECHA " + fechaFormateada],
                    // Fila 3: Vacía
                    [""],
                    // Fila 4: Encabezados
                    ['PATERNO', 'MATERNO', 'NOMBRE', 'FIRMA'],
                    // Filas siguientes: Datos
                    ...data.map(person => [
                        person.paterno,
                        person.materno,
                        person.nombre,
                        '' // Columna Firma siempre vacía
                    ])
                ];
                
                // Crear hoja de trabajo
                const ws = XLSX.utils.aoa_to_sheet(excelData);
                
                // Aplicar formato al título (fila 1)
                if (!ws['A1'].s) ws['A1'].s = {};
                ws['A1'].s = {
                    font: { bold: true, sz: 16 },
                    alignment: { horizontal: 'center' }
                };
                
                // Combinar celdas para el título (A1:D1)
                ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 3 } }];
                
                // Aplicar formato a los encabezados (fila 4)
                for (let col = 0; col < 4; col++) {
                    const cellRef = XLSX.utils.encode_cell({ r: 3, c: col });
                    if (!ws[cellRef].s) ws[cellRef].s = {};
                    ws[cellRef].s = {
                        font: { bold: true },
                        fill: { fgColor: { rgb: "D9D9D9" } },
                        border: {
                            top: { style: "thin" },
                            bottom: { style: "thin" },
                            left: { style: "thin" },
                            right: { style: "thin" }
                        }
                    };
                }
                
                // Ajustar anchos de columnas
                ws['!cols'] = [
                    { wch: 20 }, // Paterno
                    { wch: 20 }, // Materno
                    { wch: 30 }, // Nombre
                    { wch: 10 }  // Firma (vacía)
                ];
                
                // Agregar hoja al libro
                XLSX.utils.book_append_sheet(wb, ws, "PadronAsistencia");
                
                // Generar nombre de archivo con fecha
                const fechaArchivo = fechaActual.toISOString().split('T')[0];
                XLSX.writeFile(wb, `Padron_Asistencia_${fechaArchivo}.xlsx`);
            }

        });
    </script>
</body>
</html>