@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
@endpush
<x-app-layout>
    <main class="main">
        <section id="menu-cards" class="hero section">
            <article class="card">
                <img class="card__background" src="https://colombia.travel/sites/default/files/styles/imagen_650x450_escala_y_recorte/public/destino/shutterstock_2200949319%20%282%29%20%281%29.jpg?itok=SxWtDXg_"
                    alt="Photo of Cartagena's cathedral at the background and some colonial style houses" width="1920"
                    height="2193" />
                <div class="card__content | flow">
                    <div class="card__content--container | flow">
                        <h2 class="card__title">Colombia</h2>
                        <p class="card__description">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rerum in
                            labore laudantium deserunt fugiat numquam.
                        </p>
                    </div>
                    <button class="card__button">Read more</button>
                </div>
            </article>
            <article class="card">
                <img class="card__background" src="https://colombia.travel/sites/default/files/styles/imagen_650x450_escala_y_recorte/public/destino/shutterstock_2200949319%20%282%29%20%281%29.jpg?itok=SxWtDXg_"
                    alt="Photo of Cartagena's cathedral at the background and some colonial style houses" width="1920"
                    height="2193" />
                <div class="card__content | flow">
                    <div class="card__content--container | flow">
                        <h2 class="card__title">Colombia</h2>
                        <p class="card__description">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rerum in
                            labore laudantium deserunt fugiat numquam.
                        </p>
                    </div>
                    <button class="card__button">Read more</button>
                </div>
            </article>
        </section>
    </main>
</x-app-layout>
