<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-head>
        </x-head>
        <!-- Scripts -->
        @routes
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    {{-- <body class="font-sans antialiased">

    </body> --}}
    <body class="
        @if(userPreferencesSidebar() == 'closed')
            sidebar-mini
        @endif
        m-0 font-sans antialiased font-normal dark:bg-slate-900 text-sm 2xl:text-base leading-default bg-gray-100 text-slate-600">
        <x-scripts>
        </x-scripts>
        <div class="min-h-screen">
            @inertia
        </div>
    </body>
</html>
