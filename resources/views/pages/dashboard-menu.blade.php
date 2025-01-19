@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
@endpush
@section('title', 'Dashboard')
<x-app-layout>
    <main class="main">
        <section style="background-image: url('{{asset('assets/img/hero-bg.png')}}')" id="menu-cards" class="hero section">
            <article class="card">
                <img class="card__background" src="https://infowan.net/Content/images/support/supportticket.png"
                    alt="Tools Photo" width="1920"
                    height="2193" />
                <div class="card__content | flow">
                    <div class="card__content--container | flow">
                        <h2 class="card__title"><i class="fa-solid fa-screwdriver-wrench"></i> Tools</h2>
                        <p class="card__description">
                            Herramientas para automatizar procesos
                        </p>
                    </div>
                    <button class="card__button">Ingresar</button>
                </div>
            </article>
            <article class="card">
                <img class="card__background" src="https://support.cc/images/blog/it-ticketing-tools.png?v=1682512742702523116"
                    alt="Tickets SoftlogyDesk" width="1920"
                    height="2193" />
                <div class="card__content | flow">
                    <div class="card__content--container | flow">
                        <h2 class="card__title"><i class="fa-solid fa-ticket"></i> Tickets</h2>
                        <p class="card__description">
                            Solicite Soporte Técnico en pocos pasos
                        </p>
                    </div>
                    <button class="card__button">Ingresar</button>
                </div>
            </article>
        </section>
    </main>
</x-app-layout>
