<div>
    <div class="window-wrapper-chat">
		<div class="window-title">
			<div class="dots">
				<i class="fa fa-circle"></i>
				<i class="fa fa-circle"></i>
				<i class="fa fa-circle"></i>
			</div>
			<div class="title">
				<span><i class="fa-solid fa-file-waveform"></i> SoftlogyDesk Historial de Caso</span>
			</div>
			<div class="expand">
				<i class="fa fa-expand"></i>
			</div>
		</div>
		<div class="window-area">
			<div class="dropdown-ticket-info">
				<ul id="accordion" class="accordion">
					<li>
					  <div class="link"><i class="fa fa-database"></i>Web Design<i class="fa fa-chevron-down"></i></div>
					  <ul class="submenu">
						<li class="item"><a><i class="fa fa-list-alt"></i><span>Dashboard</span></a></li>
						<li class="item active"><a href="#"><i class="fa-solid fa-list-check"></i><span>Ver Todo</span></a></li>
						<li><a href="#"><i class="fa-solid fa-person-chalkboard"></i><span>Actualizaciones Softlogy</span></a></li>
						<li><a href="#"><i class="fa-solid fa-child-reaching"></i><span>Tus respuestas</span></a></li>
						<li><a href="#"><i class="fa-solid fa-clock"></i></i></i><span>Ultima Actualización</span></a></li>		
					  </ul>
					</li>
				  </ul>	
			</div>
	  
			<div class="conversation-list">
						<ul class="">
							<li class="item"><a><i class="fa fa-list-alt"></i><span>Dashboard</span></a></li>
							<li class="item active"><a href="#"><i class="fa-solid fa-list-check"></i><span>Ver Todo</span></a></li>
							<li><a href="#"><i class="fa-solid fa-person-chalkboard"></i><span>Actualizaciones Softlogy</span></a></li>
							<li><a href="#"><i class="fa-solid fa-child-reaching"></i><span>Tus respuestas</span></a></li>
							<li><a href="#"><i class="fa-solid fa-clock"></i></i></i><span>Ultima Actualización</span></a></li>				
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
								<span>Cucu Ionel</span>
								<i class="fa fa-angle-down"></i>
								<span class="availability">Available</span>
							</div>		
						</div>
				</div>

			<div class="chat-area">
				<div class="title"><b>Caso #{{$ticketInfo->id}}-{{$ticketInfo->name}} </b><i class="fa fa-search"></i></div>
				<div class="chat-list">
					<ul>
						{{--Ticket Aperture --}}
						<li class="me">
							<div class="name">
								<span class="user-msg">								
								<i class="fa-solid fa-user-tie idle"></i>
								<br>
								{{$ticketInfo->user->name}}							
								</span>
							</div>
							<div class="message">
								<p><span class="blue-label">Apertura de Caso</span></p>								
								<p>{{$ticketInfo->content}}</p>
								@if($ticketInfo->resources && count($ticketInfo->resources) > 0)
									@foreach ($ticketInfo->resources as $key => $resource)
										<hr>
										<img src="{{ $resource }}" alt="{{$ticketInfo->documents[$key]->tag}}" />                                                        
									@endforeach
								@endif    								
							</div>
							<span class="msg-time">{{$ticketInfo->date}}</span>
						</li>
						{{--Iterator folloups--}}
						@if($ticketInfo->followups && count($ticketInfo->followups) > 0)
							@foreach ($ticketInfo->followups as $followup )
								
							@endforeach
						@endif
						<li class="me">
							<div class="name">
								<i class="fa-solid fa-user-astronaut online"></i>
								<br>
								<span class="user-msg">Christian Smith</span>
							</div>
							<div class="message">
								<p><span class="green-label">Tecnico Softlogy</span></p>
								<p>La situacion es esta y aquella</p>								
							</div>
							<span class="msg-time">2025-02-01 : 5:01 PM</span>
						</li>
					</ul>
				</div>
				
				<div class="input-area">
					
					<div class="input-wrapper">		
						<form wire:submit.prevent="uploadFollowUp">			
						<input type="text" class="messageFollowUp" wire:model="message" placeholder="Escribe un mensaje...">
						<i class="fa-regular fa-face-smile emojiButton" wire:click="$dispatch('toggleEmojiPicker')"></i>
						<i class="fa fa-paperclip" wire:click="$dispatch('triggerFileInput')"></i>
				
						<!-- Mostrar el input de archivo -->
						<input type="file" wire:model="file" id="file-input" style="display: none;" accept="image/*, .pdf, .doc, .docx">
				
						@error('file') <span class="error">{{ $message }}</span> @enderror
					</div>					
					<button type="submit" value="Enviar" class="btn send-btn" >Enviar</button>
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
							<li><span class="status offline"><i class="fa-solid fa-user-tie"></i></span><span>Solicitante:<br>
								<ul style="list-style:disc;">
									<li style="display: list-item;">Papa Johns</li>
								</ul>	
							</span></li>
							<li><span class="status online"><i class="fa-solid fa-user-astronaut"></i></span><span>Tecnicos:<br>
								<ul style="list-style:disc;">
									<li style="display: list-item;">Jeremy Pedraza</li>
								</ul>
							</span></li>
							<li><span class="status idle"><i class="fa-solid fa-users"></i></span>
							<span>Observadores:<br>
								<ul style="list-style:disc;">
									<li style="display: list-item;">Rodrigo Fernandez</li>
									<li>Andres Chinchilla</li>
								</ul>
							</span><span class="time">10:45 pm</span></li>							
						</ul>
						<nav class="tmln tmln--box tmln--hr">
							<h2 class="tmln__header">Estado de Caso</h2>
								<ul class="tmln__list">
									<li class="tmln__item tmln__item--active">
										<span data-title>32 mins ago</span>
										<h3 class="tmln__item-headline">Nuevo</h3>										
									</li>
									<li class="tmln__item tmln__item--bg-lght">
										<span data-title>36 mins ago</span>
										<h3 class="tmln__item-headline">En Curso</h3>											
									</li>
									<li class="tmln__item tmln__item--bg-dark">
										<span data-title>58 mins ago</span>
										<h3 class="tmln__item-headline">Solucionado</h3>
									</li>
									<li class="tmln__item tmln__item--bg-lght">
										<span data-title>1 day ago</span>
										<h3 class="tmln__item-headline">Cerrado</h3>																				
									</li>
								</ul>
						</nav>
					</li>
					<li>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor leo nec ligula viverra, quis facilisis nunc vehicula. Maecenas purus orci, efficitur in dapibus vel, rutrum in massa. Sed auctor urna sit amet eros mattis interdum. Integer imperdiet ante in quam lacinia, a laoreet risus imperdiet. Ut a blandit elit, vitae volutpat nunc. Nam posuere urna sagittis lectus eleifend viverra. Quisque mauris nunc, viverra vitae sodales non, auctor in diam. Sed dignissim pulvinar sapien sed fermentum. Praesent interdum turpis ut neque tristique ornare. Nam dictum est sed sem elementum rutrum. Nam a mollis turpis.</p>
					</li>
					<li>
						<p>Pellentesque rutrum sit amet nunc sit amet faucibus. Ut id arcu tempus, varius erat et, ornare libero. In quis felis nunc. Aliquam euismod lacus a eros ornare, ut aliquam sem mattis. Cras non varius dui, quis commodo ante. Maecenas gravida est non nulla malesuada egestas. Proin tincidunt eros et lacus sodales lobortis.</p>
					</li>
				</ul>				
			</div>
		</div>
	</div>
</div>
