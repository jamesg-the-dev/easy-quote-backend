<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Invoice - {{ $quote->quoteNumber ?? 'Quote' }}</title>

        @vite('resources/js/invoice-app.tsx')
    </head>
    <body>
        <div id="invoice-app" data-quote="{{ json_encode($quoteData) }}"></div>
    </body>
</html>
