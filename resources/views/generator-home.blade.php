<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SoftLogy- Gnerator SoftWare</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
          <!-- Vendor CSS Files -->
        <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/aos/aos.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

        <!-- Main CSS File -->
        <link href="assets/css/main.css" rel="stylesheet">
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                
            </style>
        @endif
        
    </head>

    <body class="index-page">
      
        <header id="header" class="header d-flex align-items-center sticky-top">
          <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
      
            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
              <!-- Uncomment the line below if you also wish to use an image logo -->
              <!-- <img src="assets/img/logo.png" alt=""> -->
              <h1 class="sitename">Softlogy-TOOLS</h1>
              <span>.</span>
            </a>
      
            <nav id="navmenu" class="navmenu me-auto">
              <ul>
                <li><a href="#hero" class="active">Home<br></a></li>
                <li><a href="#featured-services">Services</a></li>
                <li><a href="#about">About</a></li>                
              </ul>
              <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
      
          </div>
        </header>
      
        <main class="main">
      
          <!-- Hero Section -->
          <section id="hero" class="hero section">
      
            <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative" data-aos="zoom-out">
              <img src="{{asset('assets/img/hero-img.svg')}}" class="img-fluid animated" alt="">
              <h1>Bienvenido a <span>Softlogy Generator</span></h1>
              <p>Aquí podrás agilizar gestiones de soporte.</p>
              <div class="d-flex">
                <a href="#featured-services" class="btn-get-started scrollto">Servicios</a>                
              </div>
            </div>
      
          </section><!-- /Hero Section -->
      
          <!-- Featured Services Section -->
          <section id="featured-services" class="featured-services section">
      
            <div class="container d-flex flex-column align-items-center">
      
              <div class="col gy-4">
      
                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                  <div class="service-item position-relative">
                    <div class="icon"><i class="bi bi-activity icon"></i></div>
                    <h4>Generador de Cufes</h4>
                    <p>Herramienta para generar los Cufes de una lista de folios y prefijo específico</p>
                    <form action="{{ route('obtener.cufes') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Lista de Folios TXT</label>
                            <input class="form-control" required name="archivo"  type="file" id="formFile">
                          </div>
                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Prefijo</label>
                            <div class="col-sm-10">
                              <input type="text" name="prefijo" required class="form-control" id="prefijo" placeholder="Ejemplo: FV03">
                            </div>
                          </div>
                          <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-2 col-form-label">Nit Emisor</label>
                            <div class="col-sm-10">
                              <input type="text" name="nitEmisor" required class="form-control" id="nitEmisor" placeholder="Ejemplo: 123546879">
                            </div>
                            <div class="form-text" id="basic-addon4">Sin numero de verificacion.</div>
                          </div>
                        <button type="submit" style="background: #27303F; margin:auto;" class="btn btn-primary mb-3">Procesar TXT</button>
                    </form>
                  </div>
                </div><!-- End Service Item -->
      
              </div>
              <div class="col gy-4">
      
                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                  <div class="service-item position-relative">
                    <div class="icon"><i class="fa-solid fa-bomb"></i></div>
                    <h4>Generador de Errores - SOLO PARA FACTURAS ELECTRONICAS</h4>
                    <p>Herramienta para generar el conteo de errores de los JSON para cada BD de todas las empresas</p>
                    <form action="{{ route('obtener.errores') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="errorFile" class="form-label">Lista de Errores JSON-TXT</label>
                            <input class="form-control" required name="errorFile"  type="file" id="errorFile">
                          </div>
                        <button type="submit" style="background: #27303F; margin:auto;" class="btn btn-primary mb-3">Procesar TXT</button>
                    </form>
                  </div>
                </div><!-- End Service Item -->
      
              </div>

              <div class="col gy-4">
      
                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                  <div class="service-item position-relative">
                    <div class="icon"><i class="fa-solid fa-user"></i></div>
                    <h4>Cargue de clientes HelpDesk - SOLO PARA ADMINISTRADORES</h4>
                    <p>Su uso está pensado para la creación de usuarios para puntos de ventas de forma masiva</p>
                    <div class="d-flex flex-column align-items-center g-1" style="margin-bottom: 4em">
                      <a href="{{route('descargar.formatos')}}" style="position: relative" class="d-flex flex-column align-items-center">Descargar formato Excel 
                        <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExeDlhaWNjaWEwdmFla2tpdnYwdXE1amF4b2RzNmV2ZDZ5NTRsZ3F4dSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/24G0F8lQWYMb6jD47X/giphy.webp" 
                        alt="Click-Here" style="width: 5em; position: absolute; top:1em;"></a>

                    </div>
                    <form id="formUsersFile">                        
                        <div class="mb-3">
                            <label for="usersFile" class="form-label">Archivo Excel Formato- XLSS</label>
                            <input class="form-control" required name="usersFile"  type="file" id="usersFile">
                          </div>
                        <button type="button" id="usersFileButton" style="background: #27303F; margin:auto;" class="btn btn-primary mb-3">Procesar TXT</button>
                    </form>
                  </div>
                </div><!-- End Service Item -->
      
              </div>
      
            </div>
      
          </section><!-- /Featured Services Section -->
      
          <!-- About Section -->
          <section id="about" class="about section">
      
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
              <h2>SOFTLOGY-GENERATOR</h2>
              <p>Herramienta Integral para la Gestión de Reportes y Datos XML Relacionados con la DIAN
                SOFTLOGY-GENERATOR es una solución tecnológica diseñada para facilitar la gestión de reportes y la consulta de datos en formatos XML, 
                con un enfoque especializado en los procesos regulatorios exigidos por la Dirección de Impuestos y Aduanas Nacionales de Colombia (DIAN). 
                Este software también está orientado a facilitar el proceso de verificación de errores en la facturación electrónica.</p>
            </div><!-- End Section Title -->
      
            <div class="container" data-aos="fade-up">
      
              <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
      
                <div class="col-lg-5">
                  <div class="about-img">
                    <img src="https://www.obsbusiness.school/sites/obsbusiness.school/files/inline-images/importancia-trabajo-en-equipo.jpg" class="img-fluid" alt="">
                  </div>
                </div>
      
                <div class="col-lg-7">
                  <h3 class="pt-0 pt-lg-5">Nuestro equipo de trabajo</h3>
      
                  <!-- Tabs -->
                  <ul class="nav nav-pills mb-3">
                    <li><a class="nav-link active" data-bs-toggle="pill" href="#about-tab1">Soporte y Contabilidad</a></li>
                  </ul><!-- End Tabs -->
      
                  <!-- Tab Content -->
                  <div class="tab-content">
      
                    <div class="tab-pane fade show active" id="about-tab1">
      
                      <p class="fst-italic">Somos un equipo profesional.</p>
      
                      <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-check2"></i>
                        <h4>Repudiandae rerum velit modi et officia quasi facilis</h4>
                      </div>
                      <p>Laborum omnis voluptates voluptas qui sit aliquam blanditiis. Sapiente minima commodi dolorum non eveniet magni quaerat nemo et.</p>
      
                      <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-check2"></i>
                        <h4>Incidunt non veritatis illum ea ut nisi</h4>
                      </div>
                      <p>Non quod totam minus repellendus autem sint velit. Rerum debitis facere soluta tenetur. Iure molestiae assumenda sunt qui inventore eligendi voluptates nisi at. Dolorem quo tempora. Quia et perferendis.</p>
      
                      <div class="d-flex align-items-center mt-4">
                        <i class="bi bi-check2"></i>
                        <h4>Omnis ab quia nemo dignissimos rem eum quos..</h4>
                      </div>
                      <p>Eius alias aut cupiditate. Dolor voluptates animi ut blanditiis quos nam. Magnam officia aut ut alias quo explicabo ullam esse. Sunt magnam et dolorem eaque magnam odit enim quaerat. Vero error error voluptatem eum.</p>
      
                    </div><!-- End Tab 1 Content -->
      
                  </div>
      
                </div>
      
              </div>
      
            </div>
      
          </section><!-- /About Section -->
      
      
        </main>
      
        <footer id="footer" class="footer dark-background">
      
          <div class="footer-top">
            <div class="container">
              <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                  <a href="index.html" class="logo d-flex align-items-center">
                    <span class="sitename">Softlogy</span>
                  </a>
                  <div class="footer-contact pt-3">
                    <p>A108 Adam Street</p>
                    <p>Floridablanca, Santander</p>
                    <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
                    <p><strong>Email:</strong> <span>info@example.com</span></p>
                  </div>
                </div>
      
                <div class="col-lg-2 col-md-3 footer-links">
                  <h4>Enlaces</h4>
                  <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="#">Services</a></li>
                  </ul>
                </div>
      
                <div class="col-lg-2 col-md-3 footer-links">
                  <h4>Our Services</h4>
                  <ul>
                    <li><a href="#">Generador de Cufes</a></li>
                    <li><a href="#">Web Development</a></li>
                  </ul>
                </div>
      
              </div>
            </div>
          </div>
      
          <div class="copyright text-center">
            <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
      
              <div class="d-flex flex-column align-items-center align-items-lg-start">
                <div>
                  © Copyright <strong><span>Softlogy</span></strong>. All Rights Reserved
                </div>
                <div class="credits">
                  <!-- All the links in the footer should remain intact. -->
                  <!-- You can delete the links only if you purchased the pro version. -->
                  <!-- Licensing information: https://bootstrapmade.com/license/ -->
                  <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/herobiz-bootstrap-business-template/ -->
                  Designed by <a href="https://bootstrapmade.com/">JPDZSoftware</a> Distributed by <a href="https://themewagon.com">JPDZSoftware</a>
                </div>
              </div>
      
              <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
              </div>
      
            </div>
          </div>
      
        </footer>
        
        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
      
        <!-- Preloader -->
        <div id="preloader"></div>

        <!-- Modal Form-AdminUsers -->
        <div class="modal fade" id="adminUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="adminUserModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="adminUserModalLabel"><i style="color:#0EA2BD" class="fa-solid fa-circle-exclamation"></i> Aceso Restringido</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="adminFormModal">
              <div class="modal-body">  
                <div class="mb-3">
                  <label for="adminPassword" class="form-label">Contraseña de Administrador</label>
                  <input type="password" class="form-control" id="adminPassword" placeholder="Contraseña">
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
        
        <!-- Vendor JS Files -->
        <!-- Jquery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Swal Alerts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Bootstrap -->
        <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <!-- Vendor Scripts -->
        <script src="{{asset('assets/vendor/aos/aos.js')}}"></script>
        <script src="{{asset('assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
        <script src="{{asset('assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
        <script src="{{asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
        <script src="{{asset('assets/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
        <!-- Main JS File -->
        <script src="{{asset('assets/js/main.js')}}"></script>
        @routes
        <script>
            $('#submitAdminPassword').on('click', function () {              
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
                        data:  formData,   
                        dataType: "json", // Esperamos una respuesta JSON
                        processData: false, // Evitar que jQuery procese los datos
                        contentType: false, // Evitar que jQuery establezca el tipo de contenido
                        headers: {
                            'X-CSRF-TOKEN': token // Incluir el token CSRF en el encabezado
                        },
                        success: function (response) {
                          if (response.status) {
                            Swal.fire({
                              title: response.title,
                              text: "Se han añadido un total de: "+response.records+" Puntos de venta. Filas no Procesadas "+ response.notProcess,
                              icon: "success"
                            });                                                           
                          } else {                              
                            Swal.fire({
                              title: "Error de Integración",
                              text: "Ha sucedido un error inseperado" ,
                              icon: "warning"
                            });    
                          }
                        },
                        error: function (xhr, status, error) {
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
        </script>
      </body>
      
</html>
