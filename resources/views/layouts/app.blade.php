<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMPIT NURUL AMANAH</title>

  <link rel="shortcut icon" href="{{ asset("images/pp/logo-smp.png") }}" type="image/png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="preload" as="image" href="{{ asset('./assets/images/hero-banner.png') }}">
  <link rel="preload" as="image" href="{{ asset('./assets/images/hero-abs-1.png') }}" media="min-width(768px)">
  <link rel="preload" as="image" href="{{ asset('./assets/images/hero-abs-2.png') }}" media="min-width(768px)">
  @yield('styles')

</head>

</html>
<body id="top">

  @include('components.navbar')
  @yield('content')
  @include('components.footer')
  <!-- 
    - #BACK TO TOP
  -->

  <a href="#top" class="back-top-btn" aria-label="Back to top" data-back-top-btn>
    <ion-icon name="arrow-up"></ion-icon>
  </a>

  <!-- 
    - custom js link
  -->
  <script src="{{ asset('assets/js/script.js') }}" defer></script>

  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  @yield('scripts')
</body>

</html>