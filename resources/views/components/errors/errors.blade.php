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
                <button class="icon-button closeMessage">
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
                <button class="button-errors is-primary closeMessage">Accept</button>
            </footer>
        </article>
    </div>
@endif
@if (session('mensage_session') || session('validation_errors') || session('error'))
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
                    Reporte de Datos
                </h1>
                <button class="icon-button closeMessage">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill="none" d="M0 0h24v24H0z" />
                    <path fill="currentColor"
                        d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z" />
                </svg>
                </button>
            </header>
            <section class="modal-errors-container-body rtf">
                <h2 style="text-align:center;">¡Se registraron los siguientes resultados!</h2>
                <hr>

                {{-- Mensajes de éxito --}}
                @if (session('mensage_session'))
                    <div class="alert alert-success d-flex align-items-center" style="word-wrap: break-word;" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Success:">
                            <use xlink:href="#check-circle-fill" />
                        </svg>
                        <div>
                            <p>{{ session('mensage_session') }}</p>
                        </div>
                    </div>
                    <hr>
                @endif

                {{-- Errores de validación --}}
                @if (session('validation_errors'))
                    <div class="alert alert-danger" style="word-wrap: break-word;">
                        <h4>Errores de validación:</h4>
                        <ul>
                            @foreach (session('validation_errors') as $campo => $errores)
                                @foreach ($errores as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                    <hr>
                @endif

                {{-- Errores generales --}}
                @if (session('error'))
                    <div class="alert alert-danger" style="word-wrap: break-word;">
                        <h4>Error:</h4>
                        <p>{{ session('error') }}</p>
                    </div>
                    <hr>
                @endif

                {{-- Procesos adicionales --}}
                @if (!empty(session('response_process')))
                    <div class="alert alert-success" style="word-wrap: break-word;">
                        <h4>Procesos adicionales:</h4>
                        <ul>
                            @foreach (session('response_process') as $fila)
                                <li>Fila {{ $fila }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <hr>
                @endif

                {{-- Archivo procesado --}}
                @if (session('response_file'))
                    <a href="{{ route('descargar.pisados', session('response_file')) }}" class="btn btn-info">
                        Descargar archivo procesado
                    </a>
                @endif
            </section>
            <footer class="modal-errors-container-footer">
                <button class="button-errors is-primary closeMessage">Aceptar</button>
            </footer>
        </article>
    </div>
@endif
