<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', __('general.title'))
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    @stack('styles')
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif    
</head>

<body class="index-page">
    @include('components.errors.errors')
    @livewire('components.header')
    {{ $slot }}
    @include('components.footer.footer')
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <!-- Preloader -->
    <div id="preloader"></div>
    @stack('modals')
    <!-- Vendor JS Files -->
    <!-- Jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Swal Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select 2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Vendor Scripts -->
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
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
</body>

</html>
