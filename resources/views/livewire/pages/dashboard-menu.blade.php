<div>
    <section id="menu-cards" class="hero section">
        @if ( in_array(Auth::user()->profile_id,[9,7,5,4]))
            <a href="{{route('softlogy.tools')}}" class="card">
                <img class="card__background" src="https://thumbs.dreamstime.com/b/global-technical-support-vector-concept-design-man-support-operator-outline-flat-illustration-86072184.jpg"
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
            </a>
        @endif
        <a href="{{route('softlogy.tickets')}}" class="card">
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
        </a>
    </section>
</div>
