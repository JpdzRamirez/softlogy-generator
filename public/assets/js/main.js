/**
 * Template Name: HeroBiz
 * Template URL: https://bootstrapmade.com/herobiz-bootstrap-business-template/
 * Updated: Aug 07 2024 with Bootstrap v5.3.3
 * Author: BootstrapMade.com
 * License: https://bootstrapmade.com/license/
 */

(function () {
    "use strict";

    /**
     * Apply .scrolled class to the body as the page is scrolled down
     */
    function toggleScrolled() {
        const selectBody = document.querySelector("body");
        const selectHeader = document.querySelector("#header");
        if (
            !selectHeader.classList.contains("scroll-up-sticky") &&
            !selectHeader.classList.contains("sticky-top") &&
            !selectHeader.classList.contains("fixed-top")
        )
            return;
        window.scrollY > 100
            ? selectBody.classList.add("scrolled")
            : selectBody.classList.remove("scrolled");
    }

    document.addEventListener("scroll", toggleScrolled);
    window.addEventListener("load", toggleScrolled);

    /**
     * Mobile nav toggle
     */
    const mobileNavToggleBtn = document.querySelector(".mobile-nav-toggle");

    function mobileNavToogle() {
        document.querySelector("body").classList.toggle("mobile-nav-active");
        mobileNavToggleBtn.classList.toggle("bi-list");
        mobileNavToggleBtn.classList.toggle("bi-x");
    }
    mobileNavToggleBtn.addEventListener("click", mobileNavToogle);

    /**
     * Hide mobile nav on same-page/hash links
     */
    document.querySelectorAll("#navmenu a").forEach((navmenu) => {
        navmenu.addEventListener("click", () => {
            if (document.querySelector(".mobile-nav-active")) {
                mobileNavToogle();
            }
        });
    });

    /**
     * Toggle mobile nav dropdowns
     */
    document
        .querySelectorAll(".navmenu .toggle-dropdown")
        .forEach((navmenu) => {
            navmenu.addEventListener("click", function (e) {
                e.preventDefault();
                this.parentNode.classList.toggle("active");
                this.parentNode.nextElementSibling.classList.toggle(
                    "dropdown-active"
                );
                e.stopImmediatePropagation();
            });
        });

    /**
     * Preloader
     */
    const preloader = document.querySelector("#preloader");
    if (preloader) {
        window.addEventListener("load", () => {
            preloader.remove();
        });
    }

    /**
     * Scroll top button
     */
    let scrollTop = document.querySelector(".scroll-top");

    function toggleScrollTop() {
        if (scrollTop) {
            window.scrollY > 100
                ? scrollTop.classList.add("active")
                : scrollTop.classList.remove("active");
        }
    }
    scrollTop.addEventListener("click", (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });

    window.addEventListener("load", toggleScrollTop);
    document.addEventListener("scroll", toggleScrollTop);

    /**
     * Animation on scroll function and init
     */
    function aosInit() {
        AOS.init({
            duration: 600,
            easing: "ease-in-out",
            once: true,
            mirror: false,
        });
    }
    window.addEventListener("load", aosInit);

    /**
     * Initiate glightbox
     */
    const glightbox = GLightbox({
        selector: ".glightbox",
    });

    /**
     * Init swiper sliders
     */
    function initSwiper() {
        document
            .querySelectorAll(".init-swiper")
            .forEach(function (swiperElement) {
                let config = JSON.parse(
                    swiperElement
                        .querySelector(".swiper-config")
                        .innerHTML.trim()
                );

                if (swiperElement.classList.contains("swiper-tab")) {
                    initSwiperWithCustomPagination(swiperElement, config);
                } else {
                    new Swiper(swiperElement, config);
                }
            });
    }

    window.addEventListener("load", initSwiper);

    /**
     * Frequently Asked Questions Toggle
     */
    document
        .querySelectorAll(".faq-item h3, .faq-item .faq-toggle")
        .forEach((faqItem) => {
            faqItem.addEventListener("click", () => {
                faqItem.parentNode.classList.toggle("faq-active");
            });
        });

    /**
     * Init isotope layout and filters
     */
    document
        .querySelectorAll(".isotope-layout")
        .forEach(function (isotopeItem) {
            let layout = isotopeItem.getAttribute("data-layout") ?? "masonry";
            let filter = isotopeItem.getAttribute("data-default-filter") ?? "*";
            let sort =
                isotopeItem.getAttribute("data-sort") ?? "original-order";

            let initIsotope;
            imagesLoaded(
                isotopeItem.querySelector(".isotope-container"),
                function () {
                    initIsotope = new Isotope(
                        isotopeItem.querySelector(".isotope-container"),
                        {
                            itemSelector: ".isotope-item",
                            layoutMode: layout,
                            filter: filter,
                            sortBy: sort,
                        }
                    );
                }
            );

            isotopeItem
                .querySelectorAll(".isotope-filters li")
                .forEach(function (filters) {
                    filters.addEventListener(
                        "click",
                        function () {
                            isotopeItem
                                .querySelector(
                                    ".isotope-filters .filter-active"
                                )
                                .classList.remove("filter-active");
                            this.classList.add("filter-active");
                            initIsotope.arrange({
                                filter: this.getAttribute("data-filter"),
                            });
                            if (typeof aosInit === "function") {
                                aosInit();
                            }
                        },
                        false
                    );
                });
        });

    /**
     * Correct scrolling position upon page load for URLs containing hash links.
     */
    window.addEventListener("load", function (e) {
        if (window.location.hash) {
            if (document.querySelector(window.location.hash)) {
                setTimeout(() => {
                    let section = document.querySelector(window.location.hash);
                    let scrollMarginTop =
                        getComputedStyle(section).scrollMarginTop;
                    window.scrollTo({
                        top: section.offsetTop - parseInt(scrollMarginTop),
                        behavior: "smooth",
                    });
                }, 100);
            }
        }
    });

    /**
     * Navmenu Scrollspy
     */
    let navmenulinks = document.querySelectorAll(".navmenu a");

    function navmenuScrollspy() {
        navmenulinks.forEach((navmenulink) => {
            if (!navmenulink.hash) return;
            let section = document.querySelector(navmenulink.hash);
            if (!section) return;
            let position = window.scrollY + 200;
            if (
                position >= section.offsetTop &&
                position <= section.offsetTop + section.offsetHeight
            ) {
                document
                    .querySelectorAll(".navmenu a.active")
                    .forEach((link) => link.classList.remove("active"));
                navmenulink.classList.add("active");
            } else {
                navmenulink.classList.remove("active");
            }
        });
    }
    window.addEventListener("load", navmenuScrollspy);
    document.addEventListener("scroll", navmenuScrollspy);
    const spinner = $(".spinner");

    // Exponer la función al ámbito global
    window.showSpinner = function (show) {
        spinner.css("display", show ? "block" : "none");
    };

    document.addEventListener("hideSpinnerTicketSubmmited", function (event) {
        showSpinner(false);
        console.log(event);
        let responseData = event.detail[0]; // Recibe el array
        let ticketId = responseData.ticket; // Extrae el ID del ticket
        Swal.fire({
            title: "¡Envío exitoso!",
            imageUrl: "/assets/img/support/softlogyLogo-White.png",
            imageWidth: 150,
            imageHeight: 50,
            imageAlt: "SoftlogyTickets",
            html: `Se ha creado correctamente el ticket: <br>N°: <strong style="background-color: yellow;font-size: 2em;"><code>${ticketId}</code></strong>`,
            icon: "success",
            confirmButtonColor: "#3085d6",
        }).then((result) => {
            // Verifica si el usuario hizo clic en "OK"
            if (result.isConfirmed) {
                $(".modal-backdrop.fade.show").remove();
                location.reload();
            }
        });
    });

    /*
        TOOLS PAGE
    */
    $("#usersFileButton").on("click", function () {
        // Mostrar el modal con el id 'adminUserModal'
        let adminModal = new bootstrap.Modal(
            document.getElementById("adminUserModal")
        );
        adminModal.show();
    });
    // Evento de cierre del modal
    $("#adminUserModal").on("hidden.bs.modal", function () {
        // Limpiar los campos del formulario
        $("#adminFormModal")[0].reset();
    });
    $(".closeMessage").on("click", function () {
        $(".modal-errors").fadeOut(); // Oculta el modal con efecto de desvanecimiento
    });
    // Evento de cierre del modal
    $("#auth").on("hidden.bs.modal", function () {
        // Limpiar los campos del formulario
        $("#authFormModal")[0].reset();
    });
    $(".counter").each(function () {
        $(this)
            .prop("Counter", 0)
            .animate(
                {
                    Counter: $(this).text(),
                },
                {
                    duration: 4000,
                    easing: "swing",
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    },
                }
            );
    });

    /*
        TICKET PAGE
    */
    const openTutorialButton = $("#openTutorial");
    const closeTutorialButton = $("#closeTutorial");
    const headerTicket = $(".header-ticket-title");

    const listTickets = $("#listTickets");

    document.addEventListener("toggleListTickets", function (event) {                                
            if (event.detail[0].showList) {                
                listTickets.addClass("listTicketToggled").removeClass("listTicketNotToggled");
            } else {                
                listTickets.addClass("listTicketNotToggled").removeClass("listTicketToggled");
            }
    });

    // Función para aplicar estilos según media query
    function applyResponsiveStyles() {
        if (window.matchMedia("(max-width: 991px)").matches) {
            // Estilos para pantallas pequeñas
            if (headerTicket.css("display") !== "none") {
                headerTicket.css({
                    display: "flex",
                    "flex-direction": "column",
                    "align-items": "center",
                });
            }
        } else {
            // Estilos para pantallas grandes
            if (headerTicket.css("display") !== "none") {
                headerTicket.css({
                    display: "grid",
                });
            }
        }
    }
    // Cargar foto de ticket
    $('#photoTicketData,#photoRequestData').on('change', function () {
        showSpinner(true); 
        $(this).prop('disabled', true);
    });
    // Mostrar el tutorial y ocultar el botón "Tutorial"
    openTutorialButton.click(function () {
        if (window.matchMedia("(max-width: 991px)").matches) {
            headerTicket.css({
                display: "flex",
                "flex-direction": "column",
                "align-items": "center",
            }); // Mostrar el tutorial en pantallas pequeñas
        } else {
            headerTicket.css("display", "grid"); // Mostrar el tutorial en pantallas grandes
        }
        openTutorialButton.css("display", "none"); // Ocultar el botón "Tutorial"
    });

    // Ocultar el tutorial y mostrar el botón "Tutorial"
    closeTutorialButton.click(function () {
        headerTicket.css("display", "none"); // Ocultar el tutorial
        openTutorialButton.css("display", "block"); // Mostrar el botón "Tutorial"
    });

    // Escuchar cambios en el tamaño de la ventana
    $(window).resize(function () {
        applyResponsiveStyles();
    });

    // Aplicar estilos inicialmente al cargar la página
    applyResponsiveStyles();

    /*
        MODALS EVENTS
    */
    $("#incident").on("click", function () {
        // Mostrar el modal con el id 'fastTicketModal'
        let adminModal = new bootstrap.Modal(
            document.getElementById("fastTicketModal")
        );
        adminModal.show();
    });
    $("#requeriment").on("click", function () {
        // Mostrar el modal con el id 'fastTicketModal'
        let adminModal = new bootstrap.Modal(
            document.getElementById("requestModal")
        );
        adminModal.show();
    });

    // Detectamos el cambio en el select
    $("#requestSelectGrid").change(function () {
        // Obtenemos el valor seleccionado
        let selectedValue = $(this).val();

        // Obtenemos el elemento del alert
        let alertElement = $("#alert-request-info");

        // Verificamos el valor seleccionado y actualizamos el contenido del alert
        if (selectedValue === "401") {
            alertElement
                .html(
                    '<i class="fa-solid fa-circle-info"></i> Los desarrollos son nuevas funcionalidades que se necesiten adicionar.'
                )
                .removeAttr("hidden");
        } else if (selectedValue === "428") {
            alertElement
                .html(
                    '<i class="fa-solid fa-headset"></i> Si necesitas instalar un nuevo punto de facturación.'
                )
                .removeAttr("hidden");
        } else if (selectedValue === "429") {
            alertElement
                .html(
                    '<i class="fa-solid fa-headset"></i> Si necesitas realizar la reinstalación de un punto de facturación.<br> <code>¡Se debe realizar un BackupPrevio!</code>'
                )
                .removeAttr("hidden");
        } else if (selectedValue === "286") {
            alertElement
                .html(
                    '<i class="fa-regular fa-folder-closed"></i> Si se han agotado los folios o necesitas nueva resolución.'
                )
                .removeAttr("hidden");
        } else {
            // Si se selecciona la opción por defecto, ocultamos el alert
            alertElement.attr("hidden", true);
        }
    });

    document.addEventListener("reloadsupportModal", function (event) {
        $(".modal-backdrop.fade.show").remove();
        // Abrir el modal
        let supportModal = new bootstrap.Modal(
            document.getElementById("sopportDataModal")
        );
        supportModal.show();

        let backdrops = $(".modal-backdrop.fade.show");

        let totalBackdrops = backdrops.length;

        if (totalBackdrops > 1) {
            backdrops.slice(1).remove();
        }
        showSpinner(false);
        $('#photoTicketData').prop('disabled', false); // Reactiva el input
    });
    document.addEventListener("reloadrequestModal", function (event) {
        $(".modal-backdrop.fade.show").remove();
        // Abrir el modal
        let requestModal = new bootstrap.Modal(
            document.getElementById("requestModal")
        );
        requestModal.show();

        let backdrops = $(".modal-backdrop.fade.show");

        let totalBackdrops = backdrops.length;

        if (totalBackdrops > 1) {
            backdrops.slice(1).remove();
        }
        showSpinner(false);
        $('#photoRequestData').prop('disabled', false); // Reactiva el input
    });

    // Seleccionar todos los botones de cerrar modales
    $('button[data-bs-dismiss="modal"]').on("click", function () {
        // Obtener el modal asociado a este botón
        let modal = $(this).closest(".modal");

        // Reiniciar el formulario dentro de ese modal específico
        modal.find("form")[0].reset();

        // Resetear variables

        Livewire.dispatch("resetAll");
    });
    /*

        LIST TICKET EVENTS
    
    */
    $(".has-tooltip").on("click", function (e) {
        // Verifica si el mismo elemento fue clickeado
        if ($(this).hasClass("active")) {
            $(this).removeClass("active"); // Remueve la clase si ya está activa
        } else {
            $(".has-tooltip").removeClass("active"); // Remueve "active" de otros elementos
            $(this).addClass("active"); // Agrega "active" al elemento clicado
        }
        e.stopPropagation(); // Evita que el clic se propague al documento
    });

    // Eliminar la clase "active" si se hace clic fuera de un .has-tooltip
    $(document).on("click", function () {
        $(".has-tooltip").removeClass("active");
    });
    // Inicializar tooltips
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    const tooltipTitleTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltipTitle"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );
    const tooltipTitleList = [...tooltipTitleTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );

    document.addEventListener("restartToolTip", function (event) {
        setTimeout(() => {
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            );
            const tooltipList = [...tooltipTriggerList].map(
                (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
            );
        }, 1000);
    });

    /*
        CHAT INPUT MESSAGES
    */
    /*
        Emoji Picker
    */
    Livewire.on("toggleEmojiPicker", () => {
        console.log("emoji testing");
        new EmojiPicker({
            trigger: [
                {
                    selector: ".emojiButton",
                    insertInto: [".messageFollowUp"],
                },
            ],
            closeButton: true,
            //specialButtons: green
        });
    });

    // File Input
    // Activar el input de archivo al hacer clic en el icono de paperclip
    Livewire.on("triggerFileInput", () => {
        document.getElementById("file-input").click();
    });
    // Event acordion effect
    function Accordion(el, multiple) {
        this.el = el;
        this.multiple = multiple;

        // Evento
        this.el.find(".link").on("click", function () {
            let $this = $(this);
            let $next = $this.next();

            $next.slideToggle();
            $this.parent().toggleClass("open");

            if (!multiple) {
                $this
                    .closest("#accordion")
                    .find(".submenu")
                    .not($next)
                    .slideUp()
                    .parent()
                    .removeClass("open");
            }
        });
    }

    // Inicializar el acordeón
    new Accordion($("#accordion"), false);

    // Tabs Chat Followup
    $(".tabs li").on("click", function (e) {
        e.preventDefault();

        let $this = $(this);
        let index = $this.index();

        // Remover la clase 'active' de todas las pestañas y agregarla solo a la seleccionada
        $(".tabs li").removeClass("active");
        $this.addClass("active");

        // Ocultar todos los contenidos y mostrar solo el correspondiente
        $(".tabs-container > li").hide();
        $(".tabs-container > li").eq(index).show();
    });

    // Asegurar que la primera pestaña y su contenido estén visibles al cargar la página
    $(".tabs li:first-child").addClass("active");
    $(".tabs-container > li").hide().first().show();
})();
