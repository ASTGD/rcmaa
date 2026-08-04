<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Home' }} &mdash; {{ config('rcmaa.short_name') }}</title>
    <meta name="description" content="{{ $description ?? config('rcmaa.tagline') }}">

    <meta property="og:site_name" content="{{ config('rcmaa.name') }}">
    <meta property="og:title" content="{{ $title ?? config('rcmaa.short_name') }}">
    <meta property="og:description" content="{{ $description ?? config('rcmaa.tagline') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon-32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#070e1b">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-parchment">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:rounded-full focus:bg-ink-900 focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-parchment">
        Skip to content
    </a>

    @include('partials.header')

    <main id="main">
        {{ $slot }}
    </main>

    @include('partials.footer')

    {{-- Persistent registration nudge; hidden on the registration flow itself. --}}
    @unless (request()->routeIs('register.*'))
        @include('partials.floating-cta')
    @endunless
</body>
</html>
