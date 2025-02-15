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
                <i class="fa fa-expand"></i>
            </div>
        </div>
        <div class="window-area">
            <div class="dropdown-ticket-info">
                <ul id="accordion" class="accordion">
                    <li>
                        <div class="link"><i class="fa fa-database"></i>Acceso Rápido<i
                                class="fa fa-chevron-down"></i></div>
                        <ul class="submenu">
                            <li class="item"><a><i class="fa fa-list-alt"></i><span>Dashboard</span></a></li>
                            <li class="item active"><a href="#"><i class="fa-solid fa-list-check"></i><span>Ver
                                        Todo</span></a></li>
                            <li><a href="#"><i class="fa-solid fa-person-chalkboard"></i><span>Actualizaciones
                                        Softlogy</span></a></li>
                            <li><a href="#"><i class="fa-solid fa-child-reaching"></i><span>Tus
                                        respuestas</span></a></li>
                            <li><a href="#"><i class="fa-solid fa-clock"></i></i></i><span>Ultima
                                        Actualización</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="conversation-list">
                <ul class="">
                    <li class="item"><a><i class="fa fa-list-alt"></i><span>Dashboard</span></a></li>
                    <li class="item active"><a href="#"><i class="fa-solid fa-list-check"></i><span>Ver
                                Todo</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-person-chalkboard"></i><span>Actualizaciones
                                Softlogy</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-child-reaching"></i><span>Tus respuestas</span></a></li>
                    <li><a href="#"><i class="fa-solid fa-clock"></i></i></i><span>Ultima Actualización</span></a>
                    </li>
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
                    <ul>
                        {{-- Ticket Aperture --}}
                        <li class="me">
                            <div class="name">
                                <span class="user-msg">
                                    <i class="fa-solid fa-user-tie offline"></i>
                                    <br>
                                    {{ $ticketInfo->user->name }}
                                </span>
                            </div>
                            <div class="message">
                                <p><span class="blue-label">Apertura de Ticket</span></p>
                                <p>{{ $ticketInfo->content }}</p>
                                @if ($ticketInfo->resources && count($ticketInfo->resources) > 0)
                                    @foreach ($ticketInfo->resources as $key => $resource)
                                        <hr>
                                        <img src="{{ $resource }}" 
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
                                <li class="me">
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
                                        <p>{{$followup->content}}</p>                                        
                                        @if ($followup->documents->resources && count($followup->documents->resources) > 0)
                                            @foreach ($followup->documents->resources as $key => $resource)
                                                <hr>
                                                <img src="{{ $resource }}" 
                                                alt="{{ $followup->documents[$key]->tag }}" 
                                                class="img-thumbnail expand-img"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal" 
                                                data-src="{{ $resource }}">
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
                            <input type="text" name="message" class="messageFollowUp" wire:model="message"
                                placeholder="Escribe un mensaje...">
                            <i class="fa-regular fa-face-smile emojiButton"
                                wire:click="$dispatch('toggleEmojiPicker')"></i>
                            <i class="fa fa-paperclip" wire:click="$dispatch('triggerFileInput')"></i>

                            <!-- Mostrar el input de archivo -->
                            <input type="file" wire:model="file" id="file-input" style="display: none;"
                                accept="image/*, .pdf, .doc, .docx">

                            @error('file')
                                <span class="error">{{ $message }}</span>
                            @enderror
                    </div>
                    <button type="submit" value="Enviar" class="btn send-btn">Enviar</button>
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
                                <span class="time">{{$followup->date_mod}}</span>
                            </li>
                        </ul>
                        <nav class="tmln tmln--box tmln--hr timeline-horinzontal">
                            <h4 class="tmln__header">Estado de Caso</h4>
                            <ul class="tmln__list">
                                <li class="tmln__item tmln__item bg-new">
                                    <span data-title>32 mins ago</span>
                                    <h6 class="tmln__item-headline">Nuevo</h6>
                                </li>
                                <li class="tmln__item tmln__item--active bg-curse">
                                    <span data-title>36 mins ago</span>
                                    <h6 class="tmln__item-headline">En Curso</h6>
                                </li>
                                <li class="tmln__item tmln__item bg-wait">
                                    <span data-title>36 mins ago</span>
                                    <h6 class="tmln__item-headline">En Espera</h6>
                                </li>
                                <li class="tmln__item tmln__item bg-planning">
                                    <span data-title>36 mins ago</span>
                                    <h6 class="tmln__item-headline">Planificado</h6>
                                </li>
                                <li class="tmln__item tmln__item bg-solved">
                                    <span data-title>58 mins ago</span>
                                    <h6 class="tmln__item-headline">Solucionado</h6>
                                </li>
                                <li class="tmln__item tmln__item bg-closed">
                                    <span data-title>1 day ago</span>
                                    <h6 class="tmln__item-headline">Cerrado</h6>
                                </li>
                            </ul>
                        </nav>
                        <nav class="tmln tmln--box timeline-vertical">
                            <h4 class="tmln__header">Estado de Caso</h4>
                                <ul class="tmln__list">
                                    <li class="tmln__item tmln__item--active bg-new">
                                        <span data-title>32 mins ago</span>
                                        <h6 class="tmln__item-headline">Nuevo</h6>
                                    </li>
                                    <li class="tmln__item tmln__item bg-curse">
                                        <span data-title>36 mins ago</span>
                                        <h6 class="tmln__item-headline">En Curso</h6>
                                    </li>
                                    <li class="tmln__item tmln__item bg-wait">
                                        <span data-title>36 mins ago</span>
                                        <h6 class="tmln__item-headline">En Espera</h6>
                                    </li>
                                    <li class="tmln__item tmln__item bg-planning">
                                        <span data-title>36 mins ago</span>
                                        <h6 class="tmln__item-headline">Planificado</h6>
                                    </li>
                                    <li class="tmln__item tmln__item bg-solved">
                                        <span data-title>58 mins ago</span>
                                        <h6 class="tmln__item-headline">Solucionado</h6>
                                    </li>
                                    <li class="tmln__item tmln__item bg-closed">
                                        <span data-title>1 day ago</span>
                                        <h6 class="tmln__item-headline">Cerrado</h6>
                                    </li>
                                </ul>
                        </nav>
                    </li>
                    <li>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor leo nec ligula
                            viverra, quis facilisis nunc vehicula. Maecenas purus orci, efficitur in dapibus vel, rutrum
                            in massa. Sed auctor urna sit amet eros mattis interdum. Integer imperdiet ante in quam
                            lacinia, a laoreet risus imperdiet. Ut a blandit elit, vitae volutpat nunc. Nam posuere urna
                            sagittis lectus eleifend viverra. Quisque mauris nunc, viverra vitae sodales non, auctor in
                            diam. Sed dignissim pulvinar sapien sed fermentum. Praesent interdum turpis ut neque
                            tristique ornare. Nam dictum est sed sem elementum rutrum. Nam a mollis turpis.</p>
                    </li>
                    <li>
                        <p>Pellentesque rutrum sit amet nunc sit amet faucibus. Ut id arcu tempus, varius erat et,
                            ornare libero. In quis felis nunc. Aliquam euismod lacus a eros ornare, ut aliquam sem
                            mattis. Cras non varius dui, quis commodo ante. Maecenas gravida est non nulla malesuada
                            egestas. Proin tincidunt eros et lacus sodales lobortis.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
