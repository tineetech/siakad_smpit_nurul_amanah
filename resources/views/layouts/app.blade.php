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
  
<style>
    /* Styles for the announcement detail page (existing) */
    .announcement-detail-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .announcement-detail-container h2 {
        margin-top: 0;
        color: #333;
    }
    .announcement-detail-container .meta {
        font-size: 0.9em;
        color: #777;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .announcement-detail-container .content {
        line-height: 1.8;
        color: #444;
    }
    .announcement-detail-container .back-link {
        display: inline-block;
        margin-top: 30px;
        color: var(--primary-color, #3366cc);
        text-decoration: none;
        font-weight: 500;
    }
    .announcement-detail-container .back-link:hover {
        text-decoration: underline;
    }

    /* NEW STYLES FOR NOTIFICATION POPUP AS MODAL */
    .notification-popup {
        /* Default state: hidden */
        display: none; 

        /* Centering the popup */
        position: fixed; /* Fixed position relative to viewport */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); /* Center horizontally and vertically */
        z-index: 1001; /* Above the overlay */

        /* Basic styling */
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        width: 90%; /* Responsive width */
        max-width: 450px; /* Maximum width */
        max-height: 80vh; /* Maximum height, allow scrolling if content is long */
        overflow-y: auto; /* Enable vertical scrolling */
        padding: 20px;
        box-sizing: border-box; /* Include padding in width/height calculation */

        /* Optional: Add smooth transition for appearance */
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        opacity: 0; /* Start hidden for transition */
        transform: translate(-50%, -50%) scale(0.9); /* Start slightly smaller for pop effect */
    }

    .notification-popup.active {
        display: block; /* Show the popup */
        opacity: 1; /* Fully visible */
        transform: translate(-50%, -50%) scale(1); /* Return to normal size */
    }

    .notification-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .notification-popup-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.2em;
    }

    .notification-popup .close-popup-btn {
        background: none;
        border: none;
        font-size: 1.5em;
        cursor: pointer;
        color: #777;
        padding: 5px; /* Add padding to make click target larger */
        transition: color 0.2s;
    }
    .notification-popup .close-popup-btn:hover {
        color: #333;
    }

    .notification-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .notification-list .announcement-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .notification-list .announcement-item:last-child {
        border-bottom: none;
    }

    .notification-list .announcement-item:hover {
        background-color: #f9f9f9;
    }

    .notification-list .announcement-item strong {
        display: block;
        color: #333;
        font-size: 0.95em;
        margin-bottom: 3px;
    }

    .notification-list .announcement-item p {
        font-size: 0.85em;
        color: #555;
        margin: 0;
        line-height: 1.3;
    }

    .notification-list .announcement-item .time {
        display: block;
        font-size: 0.75em;
        color: #999;
        text-align: right;
        margin-top: 5px;
    }

    .no-notifications {
        text-align: center;
        color: #777;
        padding: 20px;
    }

    .notification-popup-footer {
        padding-top: 15px;
        border-top: 1px solid #eee;
        text-align: center;
        margin-top: 15px;
    }

    .notification-popup-footer a {
        color: var(--primary-color, #3366cc);
        text-decoration: none;
        font-weight: 500;
    }
    .notification-popup-footer a:hover {
        text-decoration: underline;
    }

    /* NEW STYLES FOR NOTIFICATION OVERLAY */
    .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Darker semi-transparent overlay */
        z-index: 1000; /* Below the popup */
        display: none; /* Hidden by default */
        transition: opacity 0.3s ease-in-out;
        opacity: 0;
    }

    .notification-overlay.active {
        display: block;
        opacity: 1;
    }

    /* Ensure the main overlay for navigation is independent if it exists */
    .overlay[data-overlay] { /* This targets the existing overlay for navigation */
        z-index: 998; /* Adjust as needed to be below notification overlay */
    }
</style>
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