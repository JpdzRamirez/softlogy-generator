<div>
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
    
            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">Softlogy-MICRO</h1>
                <span>.</span>
            </a>
    
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fa-solid fa-house"></i> Home<br>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-swatchbook"></i> Dashboard<br>
                        </a>
                    </li>
                    @if (in_array(Auth::user()->profile_id, [4, 5, 7, 9]))
                    <li>
                        <a href="{{ route('softlogy.tools') }}" class="{{ request()->routeIs('softlogy.tools') ? 'active' : '' }}">
                            <i class="fa-solid fa-screwdriver-wrench"></i> Tools<br>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('softlogy.tickets') }}" class="{{ request()->routeIs('softlogy.tickets') ? 'active' : '' }}">
                            <i class="fa-solid fa-ticket"></i> Tickets<br>
                        </a>
                    </li>
                    <li class="logout-list">
                        <a class="confirmLogout" id="confirmLogout2">
                            <i class="fa-solid fa-right-from-bracket"></i> Log Out<br>
                        </a>
                    </li>
                </ul>
                
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            <div class="logout-container">
                <a class="button" id="confirmLogout">
                    <img src="{{ Auth::user()->picture ? Auth::user()->picture : asset('assets/img/USER.png') }}" alt="User Picture">
                    <div class="logout">Log Out</div>
                </a>
            </div>
        </div>
    </header>
</div>
@push('scripts')
    <script>
        $("#confirmLogout, #confirmLogout2").click(function (e) { 
            e.preventDefault();
            Swal.fire({
            title: "¿Estás seguro de cerrar sesión?",
            text: "Se cerrará tu sesión actual",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText:"Cancelar",
            confirmButtonText: "Sí, confirmar"
            }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmLogout');
            }
            });
        });
    </script>
@endpush