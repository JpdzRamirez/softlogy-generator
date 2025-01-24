<div>
    <div class="container">
        {{-- HEADER TICKET LIST SECTION --}}
        <div class="row">
                <div class="card-tickets-title">
                    <div class="header-ticket-title clearfix">
                        <div class="thumbnail"><img class="left" src="https://herothemes.com/wp-content/uploads/how-to-create-a-support-ticket-form2x-1200x600.png" /></div>
                        <div class="right">
                            <h1>¿Cómo crear tus tickets de soporte?</h1>
                            <div class="author"><img src="https://randomuser.me/api/portraits/men/95.jpg" />
                                <h2>SoftlogyTech</h2>
                            </div>
                            <div class="separator"></div>
                            <p>
                                Crear una solicitud de soporte no puede ser más fácil. Solo necesitas seguir unos pocos pasos para encontrar ayuda inmediata y especializada. Nuestro equipo está siempre disponible para resolver tus consultas y garantizar una solución eficiente a tus problemas.                                
                            </p>
                        </div>
                    </div>
                    <div class="group-ticket-info">
                        <div class="date-ticket">
                            <div class="date">
                                <h5>{{ $day }}</h5>
                                <span>{{ strtoupper($month) }}</span>
                            </div>                                 
                        </div>                   
                        <div class="counter-tickets">
                            <div class="row" style="gap:1em; margin: 1em;">
                                <div class="info-card col-md-3">
                                    <div class="counter-box new">
                                        <i class="fa-solid fa-plus"></i>
                                        <span class="counter">{{$ticketsCounter['nuevos']}}</span>
                                        <p>Nuevos</p>
                                    </div>
                                </div>
                                <div class="info-card col-md-3">
                                    <div class="counter-box working">
                                        <i class="fa-solid fa-person-circle-check"></i>
                                        <span class="counter">{{$ticketsCounter['encurso']}}</span>
                                        <p>En Curso</p>
                                    </div>
                                </div>
                                <div class="info-card col-md-3">
                                    <div class="counter-box planning">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <span class="counter">{{$ticketsCounter['planificados']}}</span>
                                        <p>Planificados</p>
                                    </div>
                                </div>
                                <div class="info-card col-md-3">
                                    <div class="counter-box wait">
                                        <i class="fa-solid fa-hand"></i>
                                        <span class="counter">{{$ticketsCounter['enespera']}}</span>
                                        <p>En Espera</p>
                                    </div>
                                </div>
                                <div class="info-card col-md-3">
                                    <div class="counter-box solved">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span class="counter">{{$ticketsCounter['solucionados']}}</span>
                                        <p>Solucionados</p>
                                    </div>
                                </div>
                                <div class="info-card col-md-3">
                                    <div class="counter-box closed">
                                        <i class="fa-solid fa-box-archive"></i>
                                        <span class="counter">{{$ticketsCounter['cerrados']}}</span>
                                        <p>Cerrados</p>
                                    </div>
                                </div>
                              </div>	
                        </div>
                    </div>
                    <div class="fab"><i class="fa fa-arrow-down fa-3x"> </i></div>
                </div>
        </div>
        {{-- END HEADER TICKET LIST SECTION --}}
        <div class="row">
            <div class="buttons-actions">
                <div class="multi-button">
                    <button class="button" id="incident"><span>      Soporte Rápido</span></button>
                    <button class="button" id="requeriment"></><span>      Solicitud</span></button>                    
                  </div>
            </div>
        </div>

        {{-- TICKET LIST SECTION --}}
        <div class="row" id="listTicket" >
            <div class="col-lg-10 mx-auto">
                <div class="career-search mb-60">

                    <form action="#" class="career-form mb-60">
                        <div class="row">
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="input-group position-relative">
                                    <input type="text" class="form-control" placeholder="Enter Your Keywords"
                                        id="keywords">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="select-container">
                                    <select class="custom-select">
                                        <option selected="">Location</option>
                                        <option value="1">Jaipur</option>
                                        <option value="2">Pune</option>
                                        <option value="3">Bangalore</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="select-container">
                                    <select class="custom-select">
                                        <option selected="">Select Job Type</option>
                                        <option value="1">Ui designer</option>
                                        <option value="2">JS developer</option>
                                        <option value="3">Web developer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3 d-inline-flex justify-content-center">
                                <button type="button" id="search" class="button btn btn-lg btn-block btn-light btn-custom">
                                    <span>      Seacrh</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="filter-result">
                        <p class="mb-30 ff-montserrat">Total Job Openings : 89</p>

                        <div class="job-box d-md-flex align-items-center justify-content-between mb-30">
                            <div class="job-left my-4 d-md-flex align-items-center flex-wrap">
                                <div class="img-holder mr-md-4 mb-md-0 mb-4 mx-auto mx-md-0 d-md-none d-lg-flex">
                                    FD
                                </div>
                                <div class="job-content">
                                    <h5 class="text-center text-md-left">Front End Developer</h5>
                                    <ul class="d-md-flex flex-wrap text-capitalize ff-open-sans">
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-pin mr-2"></i> Los Angeles
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-money mr-2"></i> 2500-3500/pm
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-time mr-2"></i> Full Time
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="job-right my-4 flex-shrink-0">
                                <a href="#" class="btn d-block w-100 d-sm-inline-block btn-light">Apply now</a>
                            </div>
                        </div>

                        <div class="job-box d-md-flex align-items-center justify-content-between mb-30">
                            <div class="job-left my-4 d-md-flex align-items-center flex-wrap">
                                <div class="img-holder mr-md-4 mb-md-0 mb-4 mx-auto mx-md-0 d-md-none d-lg-flex">
                                    UX
                                </div>
                                <div class="job-content">
                                    <h5 class="text-center text-md-left">Ui/Ux Developer</h5>
                                    <ul class="d-md-flex flex-wrap text-capitalize ff-open-sans">
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-pin mr-2"></i> Los Angeles
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-money mr-2"></i> 2500-3500/pm
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-time mr-2"></i> Full Time
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="job-right my-4 flex-shrink-0">
                                <a href="#" class="btn d-block w-100 d-sm-inline-block btn-light">Apply now</a>
                            </div>
                        </div>

                        <div class="job-box d-md-flex align-items-center justify-content-between mb-30">
                            <div class="job-left my-4 d-md-flex align-items-center flex-wrap">
                                <div class="img-holder mr-md-4 mb-md-0 mb-4 mx-auto mx-md-0 d-md-none d-lg-flex">
                                    GD
                                </div>
                                <div class="job-content">
                                    <h5 class="text-center text-md-left">Graphic Designer</h5>
                                    <ul class="d-md-flex flex-wrap text-capitalize ff-open-sans">
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-pin mr-2"></i> Los Angeles
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-money mr-2"></i> 2500-3500/pm
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-time mr-2"></i> Full Time
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="job-right my-4 flex-shrink-0">
                                <a href="#" class="btn d-block w-100 d-sm-inline-block btn-light">Apply now</a>
                            </div>
                        </div>

                        <div class="job-box d-md-flex align-items-center justify-content-between mb-30">
                            <div class="job-left my-4 d-md-flex align-items-center flex-wrap">
                                <div class="img-holder mr-md-4 mb-md-0 mb-4 mx-auto mx-md-0 d-md-none d-lg-flex">
                                    JS
                                </div>
                                <div class="job-content">
                                    <h5 class="text-center text-md-left">Javascript Developer</h5>
                                    <ul class="d-md-flex flex-wrap text-capitalize ff-open-sans">
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-pin mr-2"></i> Los Angeles
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-money mr-2"></i> 2500-3500/pm
                                        </li>
                                        <li class="mr-md-4">
                                            <i class="zmdi zmdi-time mr-2"></i> Full Time
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="job-right my-4 flex-shrink-0">
                                <a href="#" class="btn d-block w-100 d-sm-inline-block btn-light">Apply now</a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- START Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-reset justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                <i class="zmdi zmdi-long-arrow-left"></i>
                            </a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item d-none d-md-inline-block"><a class="page-link" href="#">2</a></li>
                        <li class="page-item d-none d-md-inline-block"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">...</a></li>
                        <li class="page-item"><a class="page-link" href="#">8</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="zmdi zmdi-long-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- END Pagination -->
            </div>
        </div>
        {{-- END TICKET LIST SECTION --}}

    </div>
</div>
