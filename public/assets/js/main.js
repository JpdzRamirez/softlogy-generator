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

    // const openTicketList = $("#openTicketList");
    // const listTickets = $("#listTickets");

    // openTicketList.on("click", function () {
    //     if (listTickets.css("display") === "flex") {
    //         listTickets.css(
    //             "animation",
    //             "slide-out-top 0.5s cubic-bezier(0.550, 0.085, 0.680, 0.530) both"
    //         );
    //         setTimeout(() => {
    //             listTickets.css("display", "none");
    //         }, 600);
    //     } else {
    //         listTickets.css(
    //             "animation",
    //             "slide-in-top 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both"
    //         );
    //         listTickets.css("display", "flex");
    //     }
    // });

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

    $("#incident").on("click", function () {
        // Mostrar el modal con el id 'fastTicketModal'
        let adminModal = new bootstrap.Modal(
            document.getElementById("fastTicketModal")
        );
        adminModal.show();
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
    // Initializar tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
})();
