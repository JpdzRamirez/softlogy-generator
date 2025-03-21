<div>
    <div class="window-wrapper-chat">
        <div class="window-title">
            <div class="dots">
                <i class="fa fa-circle"></i>
                <i class="fa fa-circle"></i>
                <i class="fa fa-circle"></i>
            </div>
            <div class="title">
                <span><i class="fa-solid fa-file-waveform"></i>Historial de Ticket</span>
            </div>
            <div class="expand">
                <a href="{{route('softlogy.tickets')}}"><button type="button" class="btn btn-info send-btn" style="color:white;"><i class="fa-solid fa-left-long"></i> Regresar</button></a>
            </div>
        </div>
        <div class="window-area">
            <div class="dropdown-ticket-info">
                <ul id="accordion" class="accordion">
                    <li class="filter-list">
                        <div class="link"><i class="fa fa-database"></i>Acceso Rápido<i
                                class="fa fa-chevron-down"></i></div>
                                <ul class="submenu">
                                    <li class="item" data-filter="dashboard"><a><i class="fa fa-list-alt"></i>
                                        <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="item active" data-filter="ver-todo"><a><i class="fa-solid fa-list-check"></i>
                                        <span>Ver Todo</span>
                                        </a>
                                    </li>
                                    <li class="item" data-filter="actualizaciones"><a><i class="fa-solid fa-person-chalkboard"></i>
                                        <span>Actualizaciones Softlogy</span>
                                        </a>
                                    </li>
                                    <li class="item" data-filter="tus-respuestas"><a><i class="fa-solid fa-child-reaching"></i>
                                        <span>Tus respuestas</span>
                                        </a>
                                    </li>
                                    <li class="item" data-filter="ultima-actualizacion"><a><i class="fa-solid fa-clock"></i>
                                        <span>Ultima Actualización</span>
                                        </a>
                                    </li>
                                </ul>
                    </li>
                </ul>
            </div>

            <div class="conversation-list">
                <ul class="filter-list">
                    <li class="item" data-filter="dashboard"><a><i class="fa fa-list-alt"></i>
                        <span>Dashboard</span>
                    </a></li>
                    <li class="item active" data-filter="ver-todo"><a><i class="fa-solid fa-list-check"></i>
                        <span>Ver Todo</span>
                    </a></li>
                    <li class="item" data-filter="actualizaciones"><a><i class="fa-solid fa-person-chalkboard"></i>
                        <span>Actualizaciones Softlogy</span>
                    </a></li>
                    <li class="item" data-filter="tus-respuestas"><a><i class="fa-solid fa-child-reaching"></i>
                        <span>Tus respuestas</span>
                    </a></li>
                    <li class="item" data-filter="ultima-actualizacion"><a><i class="fa-solid fa-clock"></i>
                        <span>Ultima Actualización</span>
                    </a></li>
                </ul>
                <div class="my-account">
                    <div class="image">
                        <img src="https://secure.gravatar.com/avatar/de76e03aa6b5b0bf675c1e8a990da52f?s=64">
                        <div class="status">
                            <i class="fa fa-circle online"></i>
                            <span class="blink online"></span>
                        </div>
                    </div>
                    <div class="name">
                        <span>{{ $ticketInfo->user->name }}</span>
                        <i class="fa fa-angle-down"></i>
                        <span class="availability">Conectado</span>
                    </div>
                </div>
            </div>

            <div class="chat-area">
                <div class="title"><b>Ticket #{{ $ticketInfo->id }}-{{ $ticketInfo->name }} </b><i
                        class="fa fa-search"></i></div>
                <div class="chat-list">
                    <ul id="messages-list">
                        {{-- Ticket Aperture --}}
                        <li class="principal">
                            <div class="name">
                                <span class="user-msg">
                                    <i class="fa-solid fa-user-tie offline"></i>
                                    <br>
                                    {{ $ticketInfo->user->name }}
                                </span>
                            </div>
                            <div class="message">
                                <p><span class="blue-label">Apertura de Ticket</span></p>
                                {!! $ticketInfo->content !!}
                                @if ($ticketInfo->resources && count($ticketInfo->resources) > 0)
                                    @foreach ($ticketInfo->resources as $key => $resource)
                                        <hr>
                                        <img 
                                        src="{{ $resource }}" 
                                        alt="{{ $ticketInfo->documents[$key]->tag }}" 
                                        class="img-thumbnail expand-img"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#imageModal" 
                                        data-src="{{ $resource }}"
                                        />
                                    @endforeach
                                @endif
                            </div>
                            <span class="msg-time">{{ $ticketInfo->date }}</span>
                        </li>
                        {{-- Iterator folloups --}}
                        @if ($ticketInfo->followups && count($ticketInfo->followups) > 0)
                            @foreach ($ticketInfo->followups as $followup)
                                <li class="message-item" data-profile-id="{{optional($followup->user->profile)->id}}" class="{{ optional($followup->user->profile)->id === 1 ? 'me' : '' }}">
                                    <div class="name">
                                        @if ($followup->users_id!=$ticketInfo->users_id_recipient)
                                                <i class="fa-solid fa-user-astronaut online"></i>
                                            @else
                                                <i class="fa-solid fa-user-tie offline"></i>
                                        @endif
                                        
                                        <br>
                                        <span class="user-msg">{{$followup->user->name}}</span>
                                    </div>
                                    <div class="message">
                                        @if (optional($followup->user->profile)->id === 4)
                                            <p><span class="violet-label">Administrador Softlogy</span></p>    
                                        @endif                                             
                                        
                                        @if (in_array(optional($followup->user->profile)->id, [5, 7], true))
                                            <p><span class="gold-label">Lider de Soporte Softlogy</span></p>    
                                        @endif                                         
                                        
                                        @if (optional($followup->user->profile)->id === 9)
                                            <p><span class="green-label">Tecnico N1 Softlogy</span></p>    
                                        @endif                                          
                                        
                                        @if (optional($followup->user->profile)->id === 10)
                                            <p><span class="black-label">Tecnico N2 Softlogy</span></p>    
                                        @endif                                       
                                        <p>{!!$followup->content!!}</p>                                        
                                        @if ($followup->documents->resources && count($followup->documents->resources) > 0)
                                            @foreach ($followup->documents->resources as $key => $resource)
                                                <hr>
                                                <img src="{{ $resource }}" 
                                                alt="{{ $followup->documents[$key]->tag }}" 
                                                class="img-thumbnail expand-img"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal" 
                                                data-src="{{ $resource }}"/>
                                            @endforeach
                                        @endif
                                    </div>
                                    <span class="msg-time">{{$followup->date}}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <div class="input-area">
                    <div class="input-wrapper">
                        <form wire:submit.prevent="uploadFollowUp">
                            <div class="input-group" style="flex-wrap: nowrap; width: inherit;align-items: center;">                                                                   
                                <button class="btn btn-outline-secondary emojiButton" type="button"><i class="fa-regular fa-face-smile"></i></button>
                                <button class="btn btn-outline-secondary triggerFileInput" type="button"><i class="fa fa-paperclip"></i>  </button>
                                <input type="text" class="form-control" name="message" id="messageFollowUp" class="@error('message') is-invalid @enderror" wire:model="message"
                                placeholder="Escribe un mensaje..." style="margin: 6px;">
                                @error('message') <span class="error">{{ $message }}</span> @enderror
                                @if ($tempFilePath && !empty($tempFilePath))
                                    <img  src="{{asset('assets/img/img-loaded.png')}}"
                                    id="preview-img"
                                    class="img-thumbnail expand-img img-loaded"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#imageModal" 
                                    data-src="{{ $tempFilePath }}"/>                            
                                @endif                       
                                <!-- Mostrar el input de archivo -->
                                <input type="file" wire:model.live.debounce.500ms="attach" id="attach-message" style="display: none;"
                                capture="camera"">
                            </div>                            
                    </div>
                    <button type="submit" value="Enviar" class="btn send-btn" onclick="showSpinner(true)">Enviar</button>
                    </form>
                </div>

            </div>
            <div class="right-tabs">
                <ul class="tabs">
                    <li class="active">
                        <a href="#"><i class="fa fa-users"></i></a>
                    </li>
                    <li><a href="#"><i class="fa fa-paperclip"></i></a></li>
                    <li><a href="#"><i class="fa fa-link"></i></a></li>
                </ul>
                <ul class="tabs-container">
                    <li class="active">
                        <ul class="member-list">
                            <li>
                                <span class="status offline">
                                    <i class="fa-solid fa-user-tie"></i>
                                </span>
                                <span>
                                    <strong>Solicitante:</strong>
                                    <br>
                                    <ul style="list-style:disc;">
                                        <li style="display: list-item;">{{ $ticketInfo->user->name }}</li>
                                    </ul>
                                </span>
                                @if ($ticketInfo->user->usercategory)
                                    <span>
                                        <strong>Tipo de Cliente:</strong>
                                        <br>
                                        <ul style="list-style:disc;">
                                            <li style="display: list-item; word-wrap: break-word;">
                                                {{ $ticketInfo->user->usercategory->name }}</li>
                                        </ul>
                                    </span>
                                @endif
                                @if ($ticketInfo->user->email)
                                    <span>
                                        Email:
                                        <br>
                                        <ul style="list-style:disc;">
                                            <li style="display: list-item; word-wrap: break-word;">
                                                {{ $ticketInfo->user->email->email }}</li>
                                        </ul>
                                    </span>
                                @endif
                                @if ($ticketInfo->user->location)
                                    <span>
                                        <strong>Ubicación:</strong> 
                                        <br>
                                        <ul style="list-style:disc;">
                                            <li style="display: list-item; word-wrap: break-word;">
                                                {{ $ticketInfo->user->location->name }}</li>
                                        </ul>
                                    </span>
                                @endif
                                @if ($ticketInfo->user->entiti)
                                    <span>
                                        <strong>Razón Social:</strong>
                                        <br>
                                        <ul style="list-style:disc;">
                                            @if ($ticketInfo->user->entiti->id===0)
                                                <li style="display: list-item; word-wrap: break-word;">
                                                   Softlogy S.A.S
                                                </li>
                                            @else
                                                <li style="display: list-item; word-wrap: break-word;">
                                                    {{ $ticketInfo->user->entiti->completename }}
                                                </li>
                                            @endif
                                        </ul>
                                    </span>
                                @endif
                            </li>
                            <li>
                                <span class="status online"><i
                                        class="fa-solid fa-user-astronaut"></i></span><span><strong>Tecnicos:</strong><br>
                                    @if (!empty($ticketInfo->tecnicos) && count($ticketInfo->tecnicos) > 0)
                                        <ul style="list-style: disc;">
                                            @foreach ($ticketInfo->tecnicos as $index => $tecnico)
                                                @if ($index === 0)
                                                    <li style="display: list-item;">{{ $tecnico->nombrecompleto }}
                                                    </li>
                                                @else
                                                    <li>{{ $tecnico->nombrecompleto }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </span>
                            </li>
                            <li>
                                <span class="status idle"><i class="fa-solid fa-users"></i></span>
                                <span><strong>Observadores:</strong><br>
                                    @if (!empty(trim($ticketInfo->observadores)))
                                        @php
                                            $observadores = array_map('trim', explode(',', $ticketInfo->observadores)); // Convertir en array y limpiar espacios
                                        @endphp
                                        <ul style="list-style: disc;">
                                            @foreach ($observadores as $index => $observador)
                                                @if ($index === 0)
                                                    <li style="display: list-item;">{{ $observador }}</li>
                                                @else
                                                    <li>{{ $observador }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <ul style="list-style: disc;">
                                            <li style="display: list-item;">Sin Observadores</li>
                                        </ul>
                                    @endif
                                </span>
                                <span class="time">{{$ticketInfo->date_mod}}</span>
                            </li>
                        </ul>
                        <nav class="tmln tmln--box tmln--hr timeline-horinzontal">
                            <h5 class="tmln__header">Estado de Caso</h5>
                            <hr>
                            <ul class="tmln__list">
                                <li class="tmln__item {{ $ticketInfo->status === 1 ? 'tmln__item--active' : '' }} bg-new">
                                    @if ($ticketInfo->status === 1)
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                    @endif
                                    <h6 class="tmln__item-headline">Nuevo</h6>
                                </li>
                                
                                @if ($ticketInfo->status === 2)
                                    <li class="tmln__item tmln__item--active bg-curse">
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        <h6 class="tmln__item-headline">En Curso</h6>
                                    </li>
                                @endif
                                
                                @if ($ticketInfo->status === 3)
                                    <li class="tmln__item tmln__item--active bg-wait">
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        <h6 class="tmln__item-headline">En Espera</h6>
                                    </li>
                                @endif
                                
                                @if ($ticketInfo->status === 4)
                                    <li class="tmln__item tmln__item--active bg-planning">
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        <h6 class="tmln__item-headline">Planificado</h6>
                                    </li>
                                @endif                            
                                
                                <li class="tmln__item {{ $ticketInfo->status === 5 ? 'tmln__item--active' : '' }} bg-solved">
                                    @if ($ticketInfo->status === 5)
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                    @endif
                                    <h6 class="tmln__item-headline">Solucionado</h6>
                                </li>
                                
                                <li class="tmln__item {{ $ticketInfo->status === 6 ? 'tmln__item--active' : '' }} bg-closed">
                                    @if ($ticketInfo->status === 6)
                                        <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                    @endif
                                    <h6 class="tmln__item-headline">Cerrado</h6>
                                </li>
                            </ul>
                        </nav>
                        <nav class="tmln tmln--box timeline-vertical">
                            <h5 class="tmln__header">Estado de Caso</h5>
                            <hr>
                                <ul class="tmln__list">
                                    <li class="tmln__item {{ $ticketInfo->status === 1 ? 'tmln__item--active' : '' }} bg-new">
                                        @if ($ticketInfo->status === 1)
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        @endif
                                        <h6 class="tmln__item-headline">Nuevo</h6>
                                    </li>
                                    
                                    @if ($ticketInfo->status === 2)
                                        <li class="tmln__item tmln__item--active bg-curse">
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                            <h6 class="tmln__item-headline">En Curso</h6>
                                        </li>
                                    @endif
                                    
                                    @if ($ticketInfo->status === 3)
                                        <li class="tmln__item tmln__item--active bg-wait">
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                            <h6 class="tmln__item-headline">En Espera</h6>
                                        </li>
                                    @endif
                                    
                                    @if ($ticketInfo->status === 4)
                                        <li class="tmln__item tmln__item--active bg-planning">
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                            <h6 class="tmln__item-headline">Planificado</h6>
                                        </li>
                                    @endif
                                
                                    
                                    <li class="tmln__item {{ $ticketInfo->status === 5 ? 'tmln__item--active' : '' }} bg-solved">
                                        @if ($ticketInfo->status === 5)
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        @endif
                                        <h6 class="tmln__item-headline">Solucionado</h6>
                                    </li>
                                    
                                    <li class="tmln__item {{ $ticketInfo->status === 6 ? 'tmln__item--active' : '' }} bg-closed">
                                        @if ($ticketInfo->status === 6)
                                            <span>{{$ticketInfo->tiempoTranscurrido }}</span>
                                        @endif
                                        <h6 class="tmln__item-headline">Cerrado</h6>
                                    </li>
                                </ul>
                        </nav>
                        <div class="d-flex flex-column sla-indicator mt-2">
                            <h5 class="tmln__header">Categorización de Caso</h5>
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                @if ($ticketInfo->itilcategory && $ticketInfo->itilcategory->completename)
                                    @php
                                        // Convertimos la cadena en un array separando por " > "
                                        $breadcrumbItems = explode(' > ', $ticketInfo->itilcategory->completename);
                                    @endphp
                                
                                    <ol class="breadcrumb itilbreadcrumb justify-content-center">
                                        <li class="breadcrumb-item"><a>ITIL</a></li>
                                    
                                        @foreach ($breadcrumbItems as $index => $itilcategory)
                                            @if ($index === count($breadcrumbItems) - 1)
                                                <li class="breadcrumb-item active" aria-current="page">{{ $itilcategory }}</li>
                                            @else
                                                <li class="breadcrumb-item"><a>{{ $itilcategory }}</a></li>
                                            @endif
                                        @endforeach
                                    </ol>
                                @else
                                    <ol class="breadcrumb itilbreadcrumb justify-content-center">
                                        <li class="breadcrumb-item"><a>Sin Categorización</a></li>
                                    </ol>
                                @endif
                              </nav>
                            <hr>
                            <div class="usage-item d-flex align-items-center justify-content-between mt-3 border-bottom border-secondary pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="usage-icon 
                                        {{ $ticketInfo->urgency == 2 ? 'bg-low' : ($ticketInfo->urgency == 3 ? 'bg-medium' : 'bg-hight') }} 
                                        rounded" 
                                        style="width: 20px; height: 20px;">
                                    </div>
                                    <span class="usage-title fs-6 ms-2">Urgencia</span>
                                </div>
                                <span class="text-muted">
                                    <span id="urgency-value">
                                        {{ $ticketInfo->urgency == 2 ? 'Baja' : ($ticketInfo->urgency == 3 ? 'Media' : 'Alta') }}
                                    </span>
                                </span>
                            </div>
                            
                            <div class="usage-item d-flex align-items-center justify-content-between mt-3 border-bottom border-secondary pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="usage-icon 
                                        {{ $ticketInfo->impact == 2 ? 'bg-low' : ($ticketInfo->impact == 3 ? 'bg-medium' : 'bg-hight') }} 
                                        rounded" 
                                        style="width: 20px; height: 20px;">
                                    </div>
                                    <span class="usage-title fs-6 ms-2">Impacto</span>
                                </div>
                                <span class="text-muted">
                                    <span id="impact-value">
                                        {{ $ticketInfo->impact == 2 ? 'Bajo' : ($ticketInfo->impact == 3 ? 'Medio' : 'Alto') }}
                                    </span>
                                </span>
                            </div>
                            
                            <div class="usage-item d-flex align-items-center justify-content-between mt-3 border-bottom border-secondary pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="usage-icon 
                                        {{ $ticketInfo->priority == 2 ? 'bg-low' : 
                                           ($ticketInfo->priority == 3 ? 'bg-medium' : 
                                           ($ticketInfo->priority == 4 ? 'bg-hight' : 'bg-max')) }} 
                                        rounded" 
                                        style="width: 20px; height: 20px;">
                                    </div>
                                    <span class="usage-title fs-6 ms-2">Prioridad</span>
                                </div>
                                <span class="text-muted">
                                    <span id="priority-value">
                                        {{ $ticketInfo->priority == 2 ? 'Baja' : 
                                           ($ticketInfo->priority == 3 ? 'Media' : 
                                           ($ticketInfo->priority == 4 ? 'Alta' : 'Máxima')) }}
                                    </span>
                                </span>
                            </div>      
                        </div>
                    </li>
                    <li>
                        <p><strong>Adjuntos:</strong></p>
                    </li>
                    <li>
                        <p><strong>Enlaces Externos:</strong></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
