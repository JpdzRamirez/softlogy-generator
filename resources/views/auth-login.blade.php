<!DOCTYPE html>
<html style="height:100%;" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SoftLogyDesk</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>

        </style>
    @endif

</head>

<body class="index-page">

    @if ($errors->any())
        <div class="modal-errors">
            <article class="modal-errors-container">
                <header class="modal-errors-container-header">
                    <h1 class="modal-errors-container-title">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                            aria-hidden="true">
                            <path fill="none" d="M0 0h24v24H0z" />
                            <path fill="currentColor"
                                d="M14 9V4H5v16h6.056c.328.417.724.785 1.18 1.085l1.39.915H3.993A.993.993 0 0 1 3 21.008V2.992C3 2.455 3.449 2 4.002 2h10.995L21 8v1h-7zm-2 2h9v5.949c0 .99-.501 1.916-1.336 2.465L16.5 21.498l-3.164-2.084A2.953 2.953 0 0 1 12 16.95V11zm2 5.949c0 .316.162.614.436.795l2.064 1.36 2.064-1.36a.954.954 0 0 0 .436-.795V13h-5v3.949z" />
                        </svg>
                        Errores de Validación
                    </h1>
                    <button class="icon-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="none" d="M0 0h24v24H0z" />
                            <path fill="currentColor"
                                d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z" />
                        </svg>
                    </button>
                </header>
                <section class="modal-errors-container-body rtf">
                    <h2 style="text-align:center;">¡Se debe verificar correctamente los datos ingresados!</h2>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </section>
                <footer class="modal-errors-container-footer">
                    <button class="button-errors is-primary">Accept</button>
                </footer>
            </article>
        </div>
    @endif
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid position-relative d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">SoftlogyDesk</h1>
                <span>.</span>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#login" class="active">Login<br></a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="login" class="hero section">

            <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
                data-aos="zoom-out">
                <img src="{{ asset('assets/img/clients/softlogy-logo.png') }}" class="img-fluid animated" alt="">
                <h1>Bienvenido a <span>SoftlogyDesk</span></h1>
                <p>Portal de mesa de ayuda.</p>
                <div class="d-flex">
                    @guest
                        <!-- Mostrar el botón para iniciar sesión cuando el usuario no esté autenticado -->
                        <button class="login" data-bs-toggle="modal" data-bs-target="#auth">
                            <span>
                                <span aria-hidden="true">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <span>Iniciar Sesión</span>
                                <span aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                </span>
                            </span>
                        </button>
                    @endguest
                
                    @auth
                        <!-- Mostrar el botón que redirige al dashboard cuando el usuario está autenticado -->
                        <a style="color:#fff" class="login" href="{{ route('dashboard') }}" >
                            <span>
                                <span aria-hidden="true">
                                    <i class="fa-solid fa-swatchbook"></i>
                                </span>
                                <span>Ir al Dashboard</span>
                                <span aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                </span>
                            </span>
                        </a>
                    @endauth
                </div>
            </div>
        </section><!-- /Hero Section -->
    </main>

    {{-- Modal Section Authorice --}}
    <div class="modal fade" id="auth" tabindex="-1" role="dialog" aria-labelledby="authModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="authModalLabel"><i class="fa-solid fa-right-to-bracket"></i>
                        Authenticación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="authFormModal" action="{{ route('login.oauth') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plataform" value="WEB">
                    <div class="modal-body softlogy-modal">
                        <div class="form-group">
                            <label for="authInputUser">Usuario</label>
                            <div class="input-group mb-3">
                                <span style="color: #fff" class="input-group-text bg-softlogy"
                                    id="authInputUser-addon"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" id="authInputUser"
                                    placeholder="Usuario" aria-label="Usuario"
                                    aria-describedby="authInputUser-addon">
                            </div>
                            <small id="userHelp" class="form-text text-muted">¡No compartas tu usuario con
                                nadie!.</small>
                        </div>
                        <div class="form-group">
                            <label for="authInputPassword">Contraseña</label>
                            <div class="input-group mb-3">
                                <span style="color: #fff" class="input-group-text  bg-softlogy"
                                    id="authInput-addon"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" class="form-control" id="authInputPassword"
                                    placeholder="Contraseña" aria-label="Contraseña"
                                    aria-describedby="authInput-addon">
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="session" class="form-check-input" id="authCheck">
                            <label class="form-check-label"  for="authCheck">Mantener Sesión
                                Iniciada</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-softlogy">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>


    <!-- Vendor JS Files -->
    <!-- Jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    <!-- Vendor Scripts -->
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.min.js') }}"></script>
    <script>
        $(".button-errors").click(function() {
            $(".modal-errors").addClass("fade-out");
            setTimeout(() => {
                $(".modal-errors").remove(); // Elimina el modal del DOM
            }, 1000);

        });
    </script>
</body>

</html>
