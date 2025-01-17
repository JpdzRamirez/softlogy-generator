<x-layout-app>
  <main class="main">
      @livewire('pages.softlogy-tools')    
  </main>
</x-layout-app>
@push('modals')
      <!-- Modal Form-AdminUsers -->
      <div class="modal fade" id="adminUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="adminUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h1 class="modal-title fs-5" id="adminUserModalLabel"><i style="color:#0EA2BD"
                          class="fa-solid fa-circle-exclamation"></i> Aceso Restringido</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="adminFormModal">
                  <div class="modal-body">
                      <div class="mb-3">
                          <label for="adminPassword" class="form-label">Contraseña de Administrador</label>
                          <input type="password" autocomplete="new-password" class="form-control"
                              id="adminPassword" placeholder="Contraseña">
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                      <button type="button" id="submitAdminPassword" class="btn btn-primary">Enviar</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
@endpush
@push('scripts')
    @routes
    <script>
        $('#submitAdminPassword').on('click', function() {
            let formData = new FormData(); // Crear objeto FormData
            let fileInput = $('#usersFile')[0].files[0]; // Obtener el archivo seleccionado
            let passwordInput = $('#adminPassword').val(); // Obtener la contraseña ingresada

            // Agregar el archivo y la contraseña al FormData
            formData.append('usersFile', fileInput);
            formData.append('adminPassword', passwordInput);

            // Obtener el token CSRF
            let token = $('meta[name="csrf-token"]').attr('content');

            // Comparamos la contraseña                
            $.ajax({
                type: "POST",
                url: route('cargar.clientes'), // Generar la ruta correctamente con Ziggy
                data: formData,
                dataType: "json", // Esperamos una respuesta JSON
                processData: false, // Evitar que jQuery procese los datos
                contentType: false, // Evitar que jQuery establezca el tipo de contenido
                headers: {
                    'X-CSRF-TOKEN': token // Incluir el token CSRF en el encabezado
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: response.title,
                            text: "Se han añadido un total de: " + response.records +
                                " Puntos de venta. Filas no Procesadas " + response.notProcess,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: "Error de Integración",
                            text: "Ha sucedido un error inseperado",
                            icon: "warning"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = "Ocurrió un error inesperado."; // Mensaje genérico por defecto
                    let title = "Error de Integración";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        // Obtener el mensaje específico desde el backend
                        errorMessage = xhr.responseJSON.message;
                    }

                    // Manejar errores específicos según el código HTTP
                    if (xhr.status === 401) {
                        title = xhr.responseJSON.title;
                    } else if (xhr.status === 500) {
                        title = xhr.responseJSON.title;
                    }

                    // Mostrar el mensaje con SweetAlert2
                    Swal.fire({
                        icon: "error",
                        title: title,
                        text: errorMessage,
                        confirmButtonText: "Entendido"
                    });
                }
            });
        });
        $(document).ready(function() {
            $('#paises').select2();

            $(".icon-button").click(function() {
                $(".modal-errors").addClass("fade-out");
                setTimeout(() => {
                    $(".modal-errors").remove(); // Elimina el modal del DOM
                }, 1000);
            });

            // Eliminar el modal cuando se haga clic en el botón "Accept"
            $(".button-errors").click(function() {
                $(".modal-errors").addClass("fade-out");
                setTimeout(() => {
                    $(".modal-errors").remove(); // Elimina el modal del DOM
                }, 1000);

            });


            $('#inlineFormCustomSelect').change(function() {
                let selectedValue = $(this).val();

                // Si se selecciona "Nit Extrangeria" (valor 2) o "Nit Empresa" (valor 3)
                if (selectedValue == '2' || selectedValue == '3') {
                    // Cambiar el label de "Primer Nombre" a "Nombre de empresa"
                    $('#firstName').attr('placeholder', 'Nombre de empresa');
                    $('#firstName').siblings('label').text('Nombre de empresa');

                    // Ocultar los campos de Apellidos y Segundo Nombre
                    $('#lastName').closest('.form-group').hide();
                    $('#secondName').closest('.form-group').hide();

                    // Mostrar el mensaje de advertencia para "primer nombre"
                    $('#digitVerification').show();
                } else {
                    // Restaurar los valores por defecto del label y el placeholder
                    $('#firstName').attr('placeholder', 'Nombre');
                    $('#firstName').siblings('label').text('Primer Nombre');

                    // Mostrar los campos de Apellidos y Segundo Nombre
                    $('#lastName').closest('.form-group').show();
                    $('#secondName').closest('.form-group').show();

                    // Ocultar el mensaje de advertencia
                    $('#digitVerification').hide();
                }
            });
        });
    </script>
@endpush
