@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css" integrity="sha256-3sPp8BkKUE7QyPSl6VfBByBroQbKxKG7tsusY2mhbVY=" crossorigin="anonymous" /> 
@endpush
@section('title', 'Tickets')
<x-app-layout>
    <main class="main">
        @livewire('pages.softlogy-tickets')    
    </main>
</x-app-layout>