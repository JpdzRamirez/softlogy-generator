<div>
    <!-- Hero Section -->
    <section id="hero" class="hero section">
        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out">
            <img src="{{ asset('assets/img/hero-img.svg') }}" class="img-fluid animated" alt="">
            <h1>Bienvenido a <span>Softlogy MICRO</span></h1>
            <p>Aquí podrás agilizar gestiones de soporte en facturación electrónica.</p>
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
                                <input class="form-control" required name="archivo" type="file" id="formFile">
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Prefijo</label>
                                <div class="col-sm-10">
                                    <input type="text" name="prefijo" required class="form-control" id="prefijo"
                                        placeholder="Ejemplo: FV03">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Nit Emisor</label>
                                <div class="col-sm-10">
                                    <input type="text" name="nitEmisor" required class="form-control" id="nitEmisor"
                                        placeholder="Ejemplo: 123546879">
                                </div>
                                <div class="form-text" id="basic-addon4">Sin número de verificación.</div>
                            </div>
                            <button type="submit" style="background: #27303F; margin:auto;"
                                class="btn btn-primary mb-3">Procesar TXT</button>
                        </form>
                    </div>
                </div><!-- End Service Item -->

            </div>
            <div class="col gy-4">

                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                    <div class="service-item position-relative">
                        <div class="icon"><i class="fa-solid fa-bomb"></i></div>
                        <h4>Generador de Errores - SOLO PARA FACTURAS ELECTRONICAS</h4>
                        <p>Herramienta para generar el conteo de errores de los JSON para cada BD de todas las
                            empresas</p>
                        <form action="{{ route('obtener.errores') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="errorFile" class="form-label">Lista de Errores JSON-TXT</label>
                                <input class="form-control" required name="errorFile" type="file" id="errorFile">
                            </div>
                            <button type="submit" style="background: #27303F; margin:auto;"
                                class="btn btn-primary mb-3">Procesar TXT</button>
                        </form>
                    </div>
                </div><!-- End Service Item -->

            </div>

            <div class="col gy-4">

                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                    <div class="service-item position-relative">
                        <div class="icon"><i class="fa-solid fa-user"></i></div>
                        <h4>Cargue de clientes HelpDesk - SOLO PARA ADMINISTRADORES</h4>
                        <p>Su uso está pensado para la creación de usuarios para puntos de ventas de forma masiva
                        </p>
                        <div class="d-flex flex-column align-items-center g-1" style="margin-bottom: 4em">
                            <a href="{{ route('descargar.formatos') }}" style="position: relative"
                                class="d-flex flex-column align-items-center">Descargar formato Excel
                                <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExeDlhaWNjaWEwdmFla2tpdnYwdXE1amF4b2RzNmV2ZDZ5NTRsZ3F4dSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/24G0F8lQWYMb6jD47X/giphy.webp"
                                    alt="Click-Here" style="width: 5em; position: absolute; top:1em;"></a>

                        </div>
                        <form id="formUsersFile">
                            <div class="mb-3">
                                <label for="usersFile" class="form-label">Archivo Excel Formato- XLSS</label>
                                <input class="form-control" required name="usersFile" type="file" id="usersFile">
                            </div>
                            <button type="button" id="usersFileButton" style="background: #27303F; margin:auto;"
                                class="btn btn-primary mb-3">Procesar TXT</button>
                        </form>
                    </div>
                </div><!-- End Service Item -->

            </div>

            {{-- Refacturador --}}

            <div class="col gy-4">

                <div class="row d-flex" data-aos="fade-up" style="min-width: 40vw;" data-aos-delay="100">
                    <div class="service-item position-relative">
                        <div class="icon"><i class="fa-solid fa-user"></i></div>
                        <h4>Refacturación de XML</h4>
                        <p>Agiliza la personalización de la factura electrónica, los unicos campos obligatorios son:
                        </p>
                        <ul>
                            <li>Archivo xml</li>
                            <li>Tipo de Documento</li>
                            <li>País</li>
                        </ul>
                        <small>Si no se ingresan los otros datos los campos no se modificarán</small>
                        <form action="{{ route('refacturar.xml') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="xmlFactura" class="form-label">Archivo .xml</label>
                                <input class="form-control" required name="xmlFactura" type="file"
                                    id="xmlFactura">
                                <details>
                                    <summary>
                                        <span class="summary-title">Agregar detalles a la factura 👈</span>
                                        <div class="summary-chevron-up">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-chevron-down">
                                                <polyline points="6 9 12 15 18 9"></polyline>
                                            </svg>
                                        </div>
                                    </summary>

                                    <div class="summary-content container d-flex flex-column" style="gap:1em;">
                                        <div class="col-auto my-1">
                                            <label class="mr-sm-2" for="inlineFormCustomSelect">Tipo de
                                                documento</label>
                                            <select required name="tipeDocument" class="custom-select mr-sm-2"
                                                id="inlineFormCustomSelect">
                                                <option value="" selected>Seleccione...</option>
                                                <option value="1">C.Extrangería</option>
                                                <option value="2">Nit Extrangería</option>
                                                <option value="3">Nit Empresa</option>
                                                <option value="4">C.Ciudadanía</option>
                                            </select>
                                        </div>
                                        <h4>Nuevos datos</h4>
                                        <div class="form-row d-flex flex-column" style="gap:1em; margin-left:8em;">
                                            <div class="form-group col-md-6">
                                                <label for="identificator">Identificación</label>
                                                <input required type="text" class="form-control"
                                                    name="identificator" id="identificator"
                                                    placeholder="Documento / Pasaporte / Rut">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="digit">Digito de Verificación</label>
                                                <input type="text" class="form-control" id="digit"
                                                    name="digit" placeholder="Ditito de verificación de nit"
                                                    aria-describedby="digitVerification">
                                                <small id="digitVerification"
                                                    class="form-text text-muted text-warning">
                                                    Si cuenta con digito de verificación.
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="firstName">Primer Nombre</label>
                                                <input required type="text" name="firstName" class="form-control"
                                                    id="firstName" placeholder="Nombre">
                                                <small id="digitVerification"
                                                    class="form-text text-muted text-warning">
                                                    Si es nit poner la razón social completa aquí.
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="secondName">Segundo Nombre</label>
                                                <input type="text" name="secondName" class="form-control"
                                                    id="secondName" placeholder="Nombre">
                                                <small id="digitVerification"
                                                    class="form-text text-muted text-warning">
                                                    Si es nit no llenar.
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="lastName">Apellidos</label>
                                                <input type="text" name="lastName" class="form-control"
                                                    id="lastName" placeholder="Apellidos">
                                                <small id="digitVerification"
                                                    class="form-text text-muted text-warning">
                                                    Si es nit no llenar.
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="emailReceptor">Correo Electrónico</label>
                                                <input required type="email" name="emailReceptor"
                                                    class="form-control" id="emailReceptor" placeholder="Email">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="phone">Teléfono</label>
                                                <input type="number" name="phone" class="form-control"
                                                    id="phone" placeholder="Teléfono">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="country">País</label>
                                                <select required id="paises" name="country" class="form-control">
                                                    <option value="">Selecciona un país</option>
                                                    @foreach ($paises as $pais)
                                                        <option value="{{ $pais->codigo }}">{{ $pais->codigo }} -
                                                            {{ $pais->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="state">Departamento</label>
                                                <input type="text" name="state" class="form-control"
                                                    id="state" placeholder="Departamento / Estado">
                                                <small id="addressInput" class="form-text text-muted text-warning">
                                                    Opcional
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="city">Ciudad</label>
                                                <input type="text" name="city" class="form-control"
                                                    id="city" placeholder="Ciudad">
                                                <small id="addressInput" class="form-text text-muted text-warning">
                                                    Opcional
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="address">Dirección del Receptor</label>
                                                <input type="text" name="address" class="form-control"
                                                    id="address" placeholder="Dirección">
                                                <small id="addressInput" class="form-text text-muted text-warning">
                                                    Opcional
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="postalCode">Código Postal</label>
                                                <input type="number" name="postalCode" class="form-control"
                                                    id="postalCode" placeholder="Ej: 680011">
                                                <small id="postalInput" class="form-text text-muted text-warning">
                                                    Opcional
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="folio">Nuevo Folio</label>
                                                <input required type="number" name="folio" class="form-control"
                                                    id="folio" placeholder="Nuevo numero de folio">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="summary-chevron-down">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-up">
                                            <polyline points="18 15 12 9 6 15"></polyline>
                                        </svg>

                                </details>
                            </div>
                            <button type="submit" style="background: #27303F; margin:auto;"
                                class="btn btn-primary mb-3">Procesar XML</button>
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
            <h2>SOFTLOGY-MICRO</h2>
            <p>Herramienta Integral para la Gestión de Reportes y Datos XML Relacionados con la DIAN
                SOFTLOGY-GENERATOR es una solución tecnológica diseñada para facilitar la gestión de reportes y la
                consulta de datos en formatos XML,
                con un enfoque especializado en los procesos regulatorios exigidos por la Dirección de Impuestos y
                Aduanas Nacionales de Colombia (DIAN).
            </p>
            <br>
            <code>Este software también Contiene una API RESTFUL que comunica la mesa de ayuda con SoftlogyMobile
                para la gestión de solicitudes, incidentes y problemas.</code>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up">

            <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">

                <div class="col-lg-5">
                    <div class="about-img">
                        <img src="https://www.obsbusiness.school/sites/obsbusiness.school/files/inline-images/importancia-trabajo-en-equipo.jpg"
                            class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-lg-7">
                    <h3 class="pt-0 pt-lg-5">Nuestro equipo de trabajo</h3>

                    <!-- Tabs -->
                    <ul class="nav nav-pills mb-3">
                        <li><a class="nav-link active" data-bs-toggle="pill" href="#about-tab1">Soporte y
                                Contabilidad</a></li>
                    </ul><!-- End Tabs -->

                    <!-- Tab Content -->
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="about-tab1">

                            <p class="fst-italic">Somos un equipo profesional.</p>

                            <div class="d-flex align-items-center mt-4">
                                <i class="bi bi-check2"></i>
                                <h4>Repudiandae rerum velit modi et officia quasi facilis</h4>
                            </div>
                            <p>Laborum omnis voluptates voluptas qui sit aliquam blanditiis. Sapiente minima commodi
                                dolorum non eveniet magni quaerat nemo et.</p>

                            <div class="d-flex align-items-center mt-4">
                                <i class="bi bi-check2"></i>
                                <h4>Incidunt non veritatis illum ea ut nisi</h4>
                            </div>
                            <p>Non quod totam minus repellendus autem sint velit. Rerum debitis facere soluta
                                tenetur. Iure molestiae assumenda sunt qui inventore eligendi voluptates nisi at.
                                Dolorem quo tempora. Quia et perferendis.</p>

                            <div class="d-flex align-items-center mt-4">
                                <i class="bi bi-check2"></i>
                                <h4>Omnis ab quia nemo dignissimos rem eum quos..</h4>
                            </div>
                            <p>Eius alias aut cupiditate. Dolor voluptates animi ut blanditiis quos nam. Magnam
                                officia aut ut alias quo explicabo ullam esse. Sunt magnam et dolorem eaque magnam
                                odit enim quaerat. Vero error error voluptatem eum.</p>
                        </div><!-- End Tab 1 Content -->
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /About Section -->
</div>
