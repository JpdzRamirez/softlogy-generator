@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css"
        integrity="sha256-3sPp8BkKUE7QyPSl6VfBByBroQbKxKG7tsusY2mhbVY=" crossorigin="anonymous" />
@endpush
@push('modals')
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Vista Previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Imagen ampliada">
            </div>
        </div>
    </div>
</div>
@endpush
@section('title', 'Tickets')
<x-app-layout>
    <main class="main">
        @livewire('pages.softlogy-ticket-info', ['id' => $id])
    </main>
</x-app-layout>

