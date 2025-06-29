@section('styles')
@endsection

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
                    <a href="{{ route('spmb.index') }}" class="navbar-link" data-nav-toggler>SPMB</a>
                </li>

            </ul>

        </nav>

        <div class="header-actions">

            {{-- Toggle Notifikasi --}}
            <button class="header-action-btn notification-toggle-btn" style="display: flex; gap: 5px; align-items: center" aria-label="Toggle notifications" data-notification-toggler>
                <ion-icon name="notifications-outline"></ion-icon>
                <span class="notification-count" id="notification-count">0</span>
            </button>

            {{-- Popup Notifikasi --}}
            <div class="notification-popup" id="notification-popup">
                <div class="notification-popup-header">
                    <h3>Pengumuman Terbaru</h3>
                    <button class="close-popup-btn" aria-label="Close notification popup" data-notification-toggler>
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>
                <ul class="notification-list" id="notification-list">
                    <li class="no-notifications">Memuat pengumuman...</li>
                </ul>
            </div>
            
            {{-- NEW: Overlay for the notification popup --}}
            <div class="notification-overlay" id="notification-overlay"></div>

            @if (Auth::check())
                <a href="{{ '/siakad' }}" class="header-action-btn login-btn">
                    <span class="span">SIAKAD</span>
                </a>
                <a href="{{ '/logout' }}" class="header-action-btn login-btn">
                    <span class="span">LOGOUT</span>
                </a>
            @else
                <a href="{{ '/siakad' }}" class="header-action-btn login-btn">
                    <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
                    <span class="span">Login / Register</span>
                </a>
            @endif

            <button class="header-action-btn nav-open-btn" aria-label="Open menu" data-nav-toggler>
                <ion-icon name="menu-outline"></ion-icon>
            </button>

        </div>

        {{-- Existing overlay for main navigation (keep separate) --}}
        <div class="overlay" data-nav-toggler data-overlay></div>

    </div>
</header>
 
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationToggler = document.querySelector('[data-notification-toggler]');
        const notificationPopup = document.getElementById('notification-popup');
        const notificationCountSpan = document.getElementById('notification-count');
        const notificationList = document.getElementById('notification-list');
        const notificationOverlay = document.getElementById('notification-overlay'); // Get the new overlay element

        // Function to fetch and display announcements
        async function fetchAnnouncements() {
            try {
                const response = await fetch('/api/announcements');
                const data = await response.json(); // Expected: { announcements: [], announcementCount: number }

                const announcementCount = data.announcementCount;
                notificationCountSpan.textContent = announcementCount > 99 ? '99+' : announcementCount;
                notificationCountSpan.style.display = announcementCount > 0 ? 'block' : 'none';

                notificationList.innerHTML = ''; 

                if (data.announcements.length === 0) {
                    notificationList.innerHTML = '<li class="no-notifications">Tidak ada pengumuman terbaru.</li>';
                } else {
                    data.announcements.forEach(announcement => {
                        const li = document.createElement('li');
                        li.className = 'announcement-item'; 
                        li.dataset.id = announcement.id;

                        const title = announcement.judul || 'Pengumuman';
                        const content = announcement.konten || 'Detail tidak tersedia.'; 
                        const date = announcement.tanggal_publikasi ? new Date(announcement.tanggal_publikasi).toLocaleString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) : '';

                        li.innerHTML = `
                            <strong>${title}</strong>
                            <p>${content.substring(0, 100)}${content.length > 100 ? '...' : ''}</p>
                            <span class="time">${date}</span>
                        `;
                        notificationList.appendChild(li);

                        li.addEventListener('click', () => {
                            // Redirect to the HTML detail page of the announcement
                            // window.location.href = `/pengumuman/${announcement.id}`; 
                            // Close popup after redirect
                            // notificationPopup.classList.remove('active');
                            // notificationOverlay.classList.remove('active');
                        });
                    });
                }

            } catch (error) {
                console.error('Error fetching announcements:', error);
                notificationCountSpan.style.display = 'none';
                notificationList.innerHTML = '<li class="no-notifications">Gagal memuat pengumuman.</li>';
            }
        }

        // --- Main Logic for Toggle and Close ---

        // Toggle notification popup and overlay
        notificationToggler.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent this click from bubbling up to document and closing immediately
            notificationPopup.classList.toggle('active');
            notificationOverlay.classList.toggle('active');

            if (notificationPopup.classList.contains('active')) {
                fetchAnnouncements(); 
            }
        });

        // Close popup when clicking the "X" button inside the popup
        const closePopupBtn = notificationPopup.querySelector('.close-popup-btn');
        if (closePopupBtn) {
            closePopupBtn.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent bubbling
                notificationPopup.classList.remove('active');
                notificationOverlay.classList.remove('active');
            });
        }


        // Close popup when clicking outside (on document) or on the overlay
        document.addEventListener('click', function(event) {
            // Check if click target is outside the popup AND outside the toggler button
            // AND the popup is currently active
            if (!notificationPopup.contains(event.target) && 
                !notificationToggler.contains(event.target) && 
                notificationPopup.classList.contains('active')) 
            {
                notificationPopup.classList.remove('active');
                notificationOverlay.classList.remove('active');
            }
        });

        // Close popup when clicking the overlay itself
        notificationOverlay.addEventListener('click', function(event) {
            // This is a direct click on the overlay, so close it.
            notificationPopup.classList.remove('active');
            notificationOverlay.classList.remove('active');
        });


        // Initial fetch when the page loads
        fetchAnnouncements();
        // Set an interval to refresh announcements (e.g., every 5 minutes)
        setInterval(fetchAnnouncements, 300000); // 300000 ms = 5 minutes
    });
</script>
@endsection