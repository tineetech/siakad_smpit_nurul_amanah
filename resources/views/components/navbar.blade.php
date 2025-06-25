
  <!-- 
    - #HEADER
  -->

  <header class="header" data-header>
    <div class="container">

      <h1>
        <a href="#" class="logo">SMPIT NURUL AMANAH</a>
      </h1>

      <nav class="navbar" data-navbar>

        <div class="navbar-top">
          <a href="#" class="logo">SMPIT NURUL AMANAH</a>

          <button class="nav-close-btn" aria-label="Close menu" data-nav-toggler>
            <ion-icon name="close-outline"></ion-icon>
          </button>
        </div>

        <ul class="navbar-list">

          <li class="navbar-item">
            <a href="#home" class="navbar-link" data-nav-toggler>Beranda</a>
          </li>

          <li class="navbar-item">
            <a href="#about" class="navbar-link" data-nav-toggler>Tentang</a>
          </li>

          <li class="navbar-item">
            <a href="#contact" class="navbar-link" data-nav-toggler>Hubungi</a>
          </li>

          <li class="navbar-item">
            <a href="{{ route('ppdb.index') }}" class="navbar-link" data-nav-toggler>PPDB</a>
          </li>


        </ul>

      </nav>

      <div class="header-actions">

        {{-- <button class="header-action-btn" aria-label="Open search" data-search-toggler>
          <ion-icon name="search-outline"></ion-icon>
        </button> --}}
        
        @if (Auth::check())
          <a href="{{ '/admin' }}" class="header-action-btn login-btn">
            <span class="span">SIAKAD</span>
          </a>
          <a href="{{ '/logout' }}" class="header-action-btn login-btn">
            <span class="span">LOGOUT</span>
          </a>
        @else
          <a href="{{ '/admin' }}" class="header-action-btn login-btn">
            <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

            <span class="span">Login / Register</span>
          </a>
        @endif

        <button class="header-action-btn nav-open-btn" aria-label="Open menu" data-nav-toggler>
          <ion-icon name="menu-outline"></ion-icon>
        </button>

      </div>

      <div class="overlay" data-nav-toggler data-overlay></div>

    </div>
  </header>
  