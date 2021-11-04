<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shujog ! @yield('title')</title>

    <link rel="stylesheet" href="{{asset('Modules/ManualOrder/Public/vendor/bootstrap/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.css" />
    <link type="text/css" rel="stylesheet" href="{{asset('Modules/ManualOrder/Public/vendor/design/css/front-end.css')}}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400&display=swap" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
    <link type="text/css" rel="stylesheet" href="{{asset('Modules/Core/Public/assets/css/order-cart.css')}}">
    <script src="{{asset('Modules/Core/Public/assets/js/order-cart.js')}}"></script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{asset('public/javascripts/jquery.bangla.js')}}"></script>

    <link type="text/css" rel="stylesheet" href="{{asset('Modules/Core/Public/assets/css/custom-design.css')}}">

    @yield('top_script')
    @livewireStyles
</head>
<body>
@include('core::inc.toaster-message')
@include('core::inc.header')
@yield('content')


@include('core::inc.delete-modal')
@yield('bottom_script')

<script src="{{asset('public/vendor/livewire/livewire.js')}}"></script>
<script data-turbolinks-eval="false">
    if (window.livewire) {
        console.warn('Livewire: It looks like Livewire\'s JavaScript assets have already been loaded. Make sure you aren\'t loading them twice.')
    }

    window.livewire = new Livewire();
    window.livewire.devTools(true);
    window.Livewire = window.livewire;
    //window.livewire_app_url = '{{env("APP_URL")}}';
   // window.livewire_app_url = 'http://localhost/shujog.xyz';
    window.livewire_app_url = 'https://d.shujog.xyz';
    window.livewire_token = 'ErIYDkLr56fNrQ9afNbFPCX7UIzq5t2h9xJoT6lB';

    /* Make sure Livewire loads first. */
    if (window.Alpine) {
        /* Defer showing the warning so it doesn't get buried under downstream errors. */
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                console.warn("Livewire: It looks like AlpineJS has already been loaded. Make sure Livewire\'s scripts are loaded before Alpine.\n\n Reference docs for more info: http://laravel-livewire.com/docs/alpine-js")
            })
        });
    }

    /* Make Alpine wait until Livewire is finished rendering to do its thing. */
    window.deferLoadingAlpine = function (callback) {
        window.addEventListener('livewire:load', function () {
            callback();
        });
    };

    document.addEventListener("DOMContentLoaded", function () {
        window.livewire.start();
    });


</script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.js"></script>
<script src="{{asset('Modules/ManualOrder/Public/vendor/bootstrap/js/bootstrap.js')}}"></script>

</body>
</html>
