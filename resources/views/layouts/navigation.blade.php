<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nge Eat</title>

    <!-- Link CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Vite JS -->
    @vite('resources/js/app.js')

    <!-- Tambahan CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body style="margin: 0; font-family: Arial, sans-serif;" x-data="{ sidebarOpen: true }">

    <div style="display: flex; height: 100vh; overflow: hidden;">

        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main Content --}}
        <div x-bind:style="{ marginLeft: sidebarOpen ? '250px' : '0px' }"
             style="flex-grow: 1; display: flex; flex-direction: column; transition: margin-left 0.3s ease;">

            {{-- Topbar --}}
            <x-topbar />

            {{-- Konten Halaman --}}
            <div style="flex-grow: 1; padding: 24px; background-color: #FAFAFA; overflow-y: auto;">
                @yield('content')
            </div>
        </div>

    </div>

    @stack('scripts')

</body>
</html>
