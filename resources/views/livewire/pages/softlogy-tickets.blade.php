<div>
    <div class="container">
        {{-- HEADER TICKET LIST SECTION --}}

        <div class="row" style="justify-content: center;">
            <button class="tutorialButton" id="openTutorial">
                <span>
                    <span aria-hidden="true">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </span>
                    <span>Tutorial</span>
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </span>
                </span>
            </button>
            <div class="card-tickets-title">
                <div class="header-ticket-title clearfix" style="display: none;">
                    <div class="thumbnail"><img class="left"
                            src="{{asset('assets/img/support/Tutorial.gif')}}" />
                    </div>
                    <div class="right">
                        <h1>¿Cómo crear tus tickets de soporte?</h1>
                        <div class="author"><img src="https://randomuser.me/api/portraits/men/95.jpg" />
                            <h2>Softlogy</h2>
                        </div>
                        <div class="separator"></div>
                        <p>
                            Crear una solicitud de soporte no puede ser más fácil. Solo necesitas seguir unos pocos
                            pasos para encontrar ayuda inmediata y especializada. Nuestro equipo está siempre disponible
                            para resolver tus consultas y garantizar una solución eficiente a tus problemas.
                            <a href="https://softlogy.sharepoint.com/:b:/s/CALIDAD/ESQ6I_bcVYxKgoaIt2IrhQ4BCKxAiq3OzFZPILbbrDM_pA?e=xkOizj" target="_blank" rel="noopener noreferrer"><code>Mas información🔑👈</code></a>
                        </p>
                    </div>
                    <button class="icon-button" id="closeTutorial">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="none" d="M0 0h24v24H0z" />
                            <path fill="currentColor"
                                d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z" />
                        </svg>
                    </button>
                </div>
                <div class="group-ticket-info">
                    <div class="date-ticket">
                        <div class="date">
                            <h5>{{ $day }}</h5>
                            <span>{{ strtoupper($month) }}</span>
                        </div>
                    </div>
                    <div class="counter-tickets">
                        <div class="row justify-content-center" style="gap:1em;max-width: 36em;">
                            <div class="info-card col-md-3">
                                <div class="counter-box new">
                                    <i class="fa-solid fa-plus"></i>
                                    <span class="counter">{{ $ticketsCounter['nuevos'] }}</span>
                                    <p>Nuevos</p>
                                </div>
                            </div>
                            <div class="info-card col-md-3">
                                <div class="counter-box oncurse">
                                    <i class="fa-solid fa-person-circle-check"></i>
                                    <span class="counter">{{ $ticketsCounter['encurso'] }}</span>
                                    <p>En Curso</p>
                                </div>
                            </div>
                            <div class="info-card col-md-3">
                                <div class="counter-box planning">
                                    <i class="fa-solid fa-calendar-check"></i>
                                    <span class="counter">{{ $ticketsCounter['planificados'] }}</span>
                                    <p>Planificados</p>
                                </div>
                            </div>
                            <div class="info-card col-md-3">
                                <div class="counter-box wait">
                                    <i class="fa-solid fa-hand"></i>
                                    <span class="counter">{{ $ticketsCounter['enespera'] }}</span>
                                    <p>En Espera</p>
                                </div>
                            </div>
                            <div class="info-card col-md-3">
                                <div class="counter-box solved">
                                    <i class="fa-solid fa-check-double"></i>
                                    <span class="counter">{{ $ticketsCounter['solucionados'] }}</span>
                                    <p>Solucionados</p>
                                </div>
                            </div>
                            <div class="info-card col-md-3">
                                <div class="counter-box closed">
                                    <i class="fa-solid fa-box-archive"></i>
                                    <span class="counter">{{ $ticketsCounter['cerrados'] }}</span>
                                    <p>Cerrados</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fab" wire:click.prevent="toggleList">
                    <i data-bs-toggle="tooltipTitle" data-bs-placement="top"
                    data-bs-custom-class="custom-tooltip"
                    data-bs-title="Desplegar lista de tickets" class="fa fa-arrow-down fa-3x"></i></div>
            </div>
        </div>
        {{-- END HEADER TICKET LIST SECTION --}}
        <div class="row">
            <div class="buttons-actions">
                <div class="multi-button">
                    <button class="button" id="incident"><span> Soporte Rápido</span></button>
                    <button class="button" id="requeriment"></><span> Solicitud</span></button>
                </div>
            </div>
        </div>

        {{-- TICKET LIST SECTION --}}
        <div class="row {{ $listToggleClass }}" id="listTickets" wire:ignore.self>
            <div class="col-lg-10 mx-auto">
                <div class="career-search mb-60">

                    <form wire:submit.prevent="searchTickets" style="gap:1em;" class="career-form mb-60 d-flex align-items-center">
                        <div class="row">
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="input-group position-relative">
                                    <input type="text" class="form-control" placeholder="N°Ticket"
                                        id="ticketID" wire:model="ticketID">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="input-group position-relative">
                                    <input type="text" class="form-control" placeholder="Nombre"
                                        id="ticketName" wire:model="ticketName">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="select-container" wire:model="ticketStatus">
                                    <select name="ticketStatus" class="custom-select">
                                        <option selected value="">Estado</option>
                                        <option value="1">Nuevos</option>
                                        <option value="2">En curso</option>
                                        <option value="3">Planificados</option>
                                        <option value="4">En Espera</option>
                                        <option value="5">Solucionados</option>
                                        <option value="6">Cerrados</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 my-3">
                                <div class="select-container">
                                    <select name="ticketType" class="custom-select" wire:model="ticketType">
                                        <option selected value="">Tipo</option>
                                        <option value="1">Soporte Rápido</option>
                                        <option value="2">Solicitud</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 my-3 d-inline-flex justify-content-center">
                            <button type="submit" id="search"
                                class="button btn btn-lg btn-block btn-light btn-custom">
                                <span> Buscar</span>
                            </button>
                        </div>
                    </form>

                    <div class="filter-result">
                        <p class="mb-30 ff-montserrat">Total casos :  {{ array_sum($ticketsCounter)}}</p>
                        @foreach ($listTickets as $ticket)
                        <div class="job-box d-md-flex align-items-center justify-content-between mb-30">
                            <div class="job-left my-4 d-md-flex align-items-center flex-wrap ticket-info">
                                <div class="img-holder mr-md-4 mb-md-0 mb-4 mx-auto mx-md-0 d-md-none d-lg-flex">
                                   <span class="has-tooltip">{{$ticket->id}}</span>
                                   <div class='field'>                                        
                                            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="toast-header">
                                                <img src="{{asset('assets/img/clients/softlogy-logo.png')}}" style="max-width: 1.2em;" class="rounded me-2" alt="Ticket Preview">
                                                <strong class="me-auto">Ticket: {{$ticket->id}}</strong>
                                                <small>{{$ticket->date}}</small>
                                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                              </div>
                                              <div class="toast-body">
                                                {!!$ticket->content!!}                                                
                                                @if($ticket->resources && count($ticket->resources) > 0)
                                                    @foreach ($ticket->resources as $resource)
                                                        <hr>
                                                        <img src="{{ $resource }}" />                                                        
                                                    @endforeach
                                                @endif                                          
                                              </div>
                                            </div>                                            
                                    </div>
                                </div>
                                <div class="job-content">
                                    <h5 style="margin-left: 1.5em;">{{$ticket->name}}</h5>
                                    <ul class="d-md-flex flex-wrap text-capitalize ff-open-sans" style="gap:1em;">
                                        <li class="mr-md-4">
                                            @switch($ticket->status)
                                                @case(1)
                                                <div class="online-indicator new">
                                                    <span class="blink"></span>
                                                </div>
                                                <span>Estado: Nuevo</span> 
                                                    @break
                                                @case(2)
                                                    <div class="online-indicator oncurse">
                                                        <span class="blink"></span>
                                                    </div>
                                                    <span>Estado: En Curso</span> 
                                                    @break
                                                @case(3)
                                                <div class="online-indicator planning">
                                                    <span class="blink"></span>
                                                </div>
                                                <span>Estado: Planificado</span> 
                                                    @break
                                                @case(4)
                                                <div class="online-indicator wait">
                                                    <span class="blink"></span>
                                                </div>
                                                <span>Estado: En Espera</span> 
                                                    @break
                                                @case(5)
                                                <div class="online-indicator solved" >
                                                    <span class="blink"></span>
                                                </div>
                                                <span>Estado: Solucionado</span> 
                                                    @break
                                                @case(6)
                                                <div class="online-indicator closed">
                                                    <span class="blink"></span>
                                                </div>
                                                <span>Estado: Cerrado</span> 
                                                    @break
                                                @default                                                    
                                            @endswitch                                            
                                        </li>
                                        <li class="mr-md-4">
                                            @if (!$ticket->solvedate)
                                                <i class="zmdi zmdi-time mr-2"></i> Fecha de apertura: {{$ticket->date}} - {{$ticket->tiempoTranscurrido}}
                                             @else
                                                <i class="zmdi zmdi-time mr-2"></i> Fecha de solución: {{$ticket->solvedate}}
                                            @endif                                            
                                        </li>
                                    </ul>
                                    <p style="margin-left: 2em;"><i class="fa-solid fa-user-gear"></i> Tecnicos asignados: 
                                        @if($ticket->tecnicos && count($ticket->tecnicos) > 0)
                                                @foreach ($ticket->tecnicos as $tecnico)
                                                    <span>{{$tecnico->nombrecompleto}}|</span>
                                                @endforeach
                                            @else
                                            <span>Sin Tecnicos Asignados</span>
                                        @endif
                                        @if ($ticket->observadores)
                                            <i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-custom-class="custom-tooltip"
                                            data-bs-title="{{$ticket->observadores}}"></i>                                            
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="job-right my-4 flex-shrink-0 ticket-buttom">
                                <div class="col">
                                    <a class="btn d-block w-100 d-sm-inline-block" href="{{route('ticket',['id'=>$ticket->id])}}">
                                      <span class="text">Ver Caso</span>
                                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.66669 11.3334L11.3334 4.66669" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.66669 4.66669H11.3334V11.3334" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                  </div>                                
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>

                <!-- START Pagination -->
                <div class="pagination justify-content-center">
                    {{ $listTickets->links(data: ['scrollTo' => false]) }}
                </div>
                <!-- END Pagination -->
            </div>
        </div>
        {{-- END TICKET LIST SECTION --}}
    </div>
    <!-- Modal Form-Fast Ticket -->  
    <div class="modal modal-xl modal-dialog-scrollable fade" id="fastTicketModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="fastTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="fastTicketModalLabel">
                        <i class="fa-solid fa-rectangle-list"></i> Solicitar asistencia rápida</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <form id="fastTicketFormModal">  
                        <div class="container-incident ">
                            <div class="top-text-wrapper">
                              <h4>Opciones rápidas: Unica elección</h4>
                              <p>
                                Seleccione <code>UN ELEMENTO</code> de la lista de opciones y da click en  <code>Continuar</code>.
                              </p>
                              <hr />
                            </div>
                            <div class="grid-wrapper grid-col-auto">

                              <label for="radio-card-1" class="radio-card">
                                <input type="radio" wire:model="ticketCheck" name="radio-card" value="406" id="radio-card-1" />
                                <div class="card-content-wrapper">
                                  <span class="check-icon"></span>
                                  <div class="card-content">
                                    <img
                                      src="{{asset('assets/img/support/printing-fail.jpg')}}"
                                      alt=""
                                    />
                                    <h4>Falla de Impresión</h4>
                                    <h5>Sí presentas fallas a la hora de imprimir tus facturas.</h5>
                                  </div>
                                </div>
                              </label>
                              <!-- /.radio-card -->
                    
                              <label for="radio-card-2" class="radio-card">
                                <input type="radio" wire:model="ticketCheck" name="radio-card" value="207" id="radio-card-2" />
                                <div class="card-content-wrapper">
                                  <span class="check-icon"></span>
                                  <div class="card-content">
                                    <img
                                      src="{{asset('assets/img/support/edit-facture.jpg')}}"
                                      alt=""
                                    />
                                    <h4>Personalizar Factura</h4>
                                    <h5>Sí necesitas personalizar los datos de tu fatura.</h5>
                                  </div>
                                </div>
                              </label>

                              <label for="radio-card-3" class="radio-card">
                                <input type="radio" wire:model="ticketCheck" name="radio-card" value="427" id="radio-card-3" />
                                <div class="card-content-wrapper">
                                  <span class="check-icon"></span>
                                  <div class="card-content">
                                    <img
                                      src="{{asset('assets/img/support/id-web.jpg')}}"
                                      alt=""
                                    />
                                    <h4>Portal Web</h4>
                                    <h5>Sí necesitas identificar una factura o se presentan fallasn en el portal.</h5>
                                  </div>
                                </div>
                              </label>

                              <label for="radio-card-4" class="radio-card">
                                <input type="radio" wire:model="ticketCheck" name="radio-card" value="389" id="radio-card-4" />
                                <div class="card-content-wrapper">
                                  <span class="check-icon"></span>
                                  <div class="card-content">
                                    <img
                                      src="{{asset('assets/img/support/document-errors.jpg')}}"
                                      alt=""
                                    />
                                    <h4>Errores en Factura</h4>
                                    <h5>Sí tus facturas presentan anomalías en sus datos.</h5>
                                  </div>
                                </div>
                              </label>
                              <!-- /.radio-card -->
                            </div>
                            <!-- /.grid-wrapper -->
                          </div>
                          <!-- /.container -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-bs-target="#sopportDataModal" data-bs-toggle="modal" style="color:#fff" class="btn btn-info">Continuar</button>
                    </div>                
            </div>
        </div>
    </div>
    <div class="modal fade" id="sopportDataModal" aria-hidden="true" aria-labelledby="sopportDataModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="sopportDataModalLabel2"><i class="fa-solid fa-photo-film"></i> Adjuntos</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="saveTicket(1)" id="supportDataForm">
                    <p><i class="fa-solid fa-camera"></i> Adjunte foto del incidente</p>                    
                    <div class="card-img card-pic">
                        @if ($photoTicketData instanceof \Livewire\TemporaryUploadedFile)
                            <img class="ticket-photo"
                                src="{{ $photoTicketData->temporaryUrl() }}" />
                        @elseif ($photoTicketData)
                            <img class="ticket-photo"
                                src="{{ $photoTicketData }}" />
                        @else
                            <img class="ticket-photo"
                                src="{{asset('assets/img/support/imageTicket.png')}}" />
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="photoTicketData" class="form-label">Fotos</label>
                        <input id="photoTicketData" required
                        class="form-control"
                        wire:model.live.debounce.500ms="photoTicketData" name="photoTicketData" type="file" capture="camera"/>                 
                        <small>Opcional</small>
                    </div>
                    <div class="mb-3">
                        <label for="aditionalDescription" class="form-label">Datos Adicionales</label>
                        <textarea required class="form-control" wire:model="descriptionTicketData" id="aditionalDescription" name="aditionalDescription" placeholder="Añada aquí una descripción breve" aria-label="With textarea"></textarea>
                     </div>                
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info" data-bs-target="#fastTicketModal" style="color:#fff" data-bs-toggle="modal">Regresar</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" style="color:#fff" onclick="showSpinner(true)"  class="btn btn-info">Enviar</button>
            </div>
            </form>
          </div>
        </div>
      </div>   

      {{--Solicitude Modal Form --}}

      <div class="modal fade" id="requestModal" aria-hidden="true" aria-labelledby="requestModalLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="requestModalLabel"><i class="fa-solid fa-photo-film"></i> Adjuntos</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="saveTicket(2)" id="requestModalForm">  
                    <div class="mb-3">
                        <label for="requestTitle" class="form-label">Nombre de solicitud</label>
                        <input type="text" class="form-control" required wire:model="requestTitle" id="requestTitle" placeholder="Titulo">
                    </div>
                    <div class="col-md">
                        <div class="form-floating">
                          <select class="form-select" required wire:model="requestType" id="requestSelectGrid">
                            <option selected>Seleccione una categoría</option>
                            <option value="401">Desarrollo-Nueva Implementación</option>
                            <option value="428">Instalación</option>
                            <option value="429">Reinstalación</option>
                            <option value="286">Nueva Resolución</option>
                          </select>
                          <label for="requestSelectGrid">Tipo de solicitud</label>
                        </div>
                        <div class="alert alert-info mt-3 text-center" hidden id="alert-request-info" role="alert"></div>
                      </div>
                    <hr>
                    <p><i class="fa-solid fa-camera"></i> Adjunte una foto de referencia</p>                    
                    <div class="card-img card-pic">
                        @if ($photoRequestData instanceof \Livewire\TemporaryUploadedFile)
                            <img class="ticket-photo"
                                src="{{ $photoRequestData->temporaryUrl() }}" />
                        @elseif ($photoRequestData)
                            <img class="ticket-photo"
                                src="{{ $photoRequestData }}" />
                        @else
                            <img class="ticket-photo"
                                src="{{asset('assets/img/support/imageTicket.png')}}" />
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="photoRequestData" class="form-label">Fotos</label>
                        <input id="photoRequestData"
                        class="form-control" 
                        wire:model.live.debounce.500ms="photoRequestData" name="photoRequestData" type="file" capture="camera"/>                                
                    </div>
                    <div class="mb-3">
                        <label for="aditionalDescription" class="form-label">Datos Adicionales</label>
                        <textarea required class="form-control" wire:model="descriptionTicketData" id="aditionalDescription" name="aditionalDescription" placeholder="Añada aquí una descripción breve" aria-label="With textarea"></textarea>
                    </div>                
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" style="color:#fff" onclick="showSpinner(true)"  class="btn btn-info">Enviar</button>
            </div>
            </form>
          </div>
        </div>
      </div> 
</div>
