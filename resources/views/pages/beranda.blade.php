@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="{{ asset('./assets/css/style.css') }}">
@endsection

@section('content')
    
  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="hero" id="home" aria-label="hero" style="background: url('./assets/images/hero-bg.jpg')">
        <div class="container">

          <div class="hero-content">

            <p class="section-subtitle">Bersama Kami Pasti Terpadu</p>

            <h2 class="h1 hero-title">Membentuk Generasi Islami Yang Cerdas</h2>

            <p class="hero-text">
              Bersama SMPIT Nurul Amanah, kami tidak hanya mendidik, tapi juga menanamkan iman, akhlak, dan budi pekerti siswa.
            </p>

            <a href="{{ route('ppdb.index') }}" class="btn btn-primary">
              <span class="span">Daftar Sekarang</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

          <figure class="">
            <img src="{{ asset('images/banner1.png') }}" width="500" height="500" loading="lazy" alt="hero image"
              class="w-100">

            {{-- <img src="./assets/images/hero-banner.png" width="500" height="500" loading="lazy" alt="hero image"
              class="w-100">

            <img src="./assets/images/hero-abs-1.png" width="318" height="352" loading="lazy" aria-hidden="true"
              class="abs-img abs-img-1">

            <img src="./assets/images/hero-abs-2.png" width="160" height="160" loading="lazy" aria-hidden="true"
              class="abs-img abs-img-2"> --}}

          </figure>

        </div>
      </section>





      <!-- 
        - #CATEGORY
      -->

      <section class="section category" aria-label="category">
        <div class="container">

          <p class="section-subtitle">SMPIT Nurul Amanah</p>

          <h2 class="h2 section-title">Fasilitas Unggulan Kami</h2>

          <ul class="grid-list">

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="briefcase-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Smart Classroom</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="file-tray-full-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Perpustakaan Interaktif</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="color-palette-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Laboratorium Sains & Komputer</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="layers-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Asrama Islami</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="laptop-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Area Olahraga & Ekstrakurikuler</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div class="card-icon">
                  <ion-icon name="thumbs-up-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Ruang Kreativitas & Komunitas</a>
                  </h3>

                </div>

              </div>
            </li>

          </ul>

        </div>
      </section>





      <!-- 
        - #ABOUT
      -->

      <section class="section about" id="about" aria-label="about">
        <div class="container">

          <figure class="about-banner">

            <img src="./assets/images/about-banner.jpg" width="450" height="590" loading="lazy" alt="about banner"
              class="w-100 about-img">

            <img src="./assets/images/about-abs-1.jpg" width="188" height="242" loading="lazy" aria-hidden="true"
              class="abs-img abs-img-1">

            <img src="./assets/images/about-abs-2.jpg" width="150" height="200" loading="lazy" aria-hidden="true"
              class="abs-img abs-img-2">

          </figure>

          <div class="about-content">

            <p class="section-subtitle">Tentang Kami</p>

            <h2 class="h2 section-title">Pendidikan Islami Berkualitas</h2>
            
            <p class="item-text" style="padding-bottom: 20px; padding-top: 10px">
              SMPIT Nurul Amanah adalah Sekolah Menengah Pertama Islam Terpadu yang berlokasi di Bogor dan berkomitmen membentuk generasi Qur'ani, cerdas, berkarakter, dan siap menghadapi tantangan global. Dengan mengusung prinsip pendidikan terpadu, kami menggabungkan kurikulum nasional dengan nilai-nilai Islam dan keterampilan abad 21 dalam satu sistem yang harmonis dan berkelanjutan.
            </p>

            <p class="item-text" style="padding-bottom: 20px; padding-top: 10px">
              Visi kami pun menjunjung tinggi persatuan pendidikan islam di indonesia yaitu "Menjadi sekolah Islam terpadu unggulan yang mencetak generasi berilmu, berakhlak mulia, dan berjiwa pemimpin."
            </p>

            <a href="https://wa.me/6281324198827" class="btn btn-primary">
              <span class="span">Kenali Lebih Lanjut</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

        </div>
      </section>

      <!-- 
        - #contact
      -->

      <section class="section cta" id="contact" aria-label="workshop" style="background-image: url('./assets/images/cta-bg.png')">
        <div class="container">

          <figure class="cta-banner">
            <img src="./assets/images/cta-banner.jpg" width="580" height="380" loading="lazy" alt="cta banner"
              class="img-cover">
          </figure>

          <div class="cta-content">

            <p class="section-subtitle">Hubungi Kami</p>

            <h2 class="h2 section-title">Ayo Bergabung dengan SMPIT Nurul Amanah!</h2>

            <p class="section-text">
              Daftarkan putra-putri Anda sekarang dan jadikan mereka bagian dari generasi unggul yang siap menghadapi tantangan global.
            </p>

            <a href="{{ route('ppdb.index') }}" class="btn btn-secondary">
              <span class="span">Daftar Sekarang</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

        </div>
      </section>

    </article>
  </main>
@endsection
