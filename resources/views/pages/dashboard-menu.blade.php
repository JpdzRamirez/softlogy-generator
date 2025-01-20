@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
@endpush
@section('title', 'Dashboard')
<x-app-layout>
    <main class="main">
        @livewire('pages.dashboard-menu')  
    </main>
</x-app-layout>
