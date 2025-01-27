@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.min.css') }}">
@endpush
@section('title', 'Tools')
<x-app-layout>
  <main class="main">
        @livewire('pages.softlogy-tools', ['paises' => $paises])
  </main>
</x-app-layout>
