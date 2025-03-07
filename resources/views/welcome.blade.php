@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-black dark:bg-black dark:text-white">
<div id="app" class="w-full h-screen">

</div>
@php
dd(auth()->check());
@endphp
<!-- Vue app will be mounted here -->
<script type="text/javascript">
    console.log({!! auth()->check() !!});
    window.Laravel = {
        csrfToken: "{{ csrf_token() }}",
        jsPermissions: {!! auth()->user()?auth()->user()->jsPermissions():0 !!}
    }
</script>
</body>
</html>
