
<header class="main-header bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light container">
        <!-- Brand / Logo -->
        <a class="navbar-brand fw-bold text-dark" href="{{ url('/') }}">House Rental</a>

        <!-- Navbar Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center ms-auto">

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ url('/') }}">Home</a>
                </li>

                <!-- View Houses -->
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('see_house') }}">View Houses</a>
                </li>

                <!-- Authentication -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('login') }}">
                            <i class="fas fa-user"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('register') }}">
                            <i class="fas fa-address-card"></i> Register
                        </a>
                    </li>
                @else
                    <!-- Dashboard Links -->
                    @if(Auth::check() && Auth::user()->usertype === 'tenant')
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('tenant.dashboard') }}">
                                <i class="fas fa-user-circle"></i> My Dashboard
                            </a>
                        </li>
                    @elseif(Auth::check() && Auth::user()->usertype === 'landlord')
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('landlord.dashboard') }}">
                                <i class="fas fa-user-tie"></i> Landlord Dashboard
                            </a>
                        </li>
                    @endif

                    <!-- Search Toggle -->
                    <li class="nav-item">
                        <button id="searchToggle" class="btn nav_search-btn" type="button">
                            <i class="fas fa-search text-dark"></i>
                        </button>
                    </li>

                    <!-- Search Form -->
                    <li class="nav-item" id="searchForm" style="display: none;">
                        <form class="d-flex ms-2" action="{{ url('search') }}" method="GET">
                            <input class="form-control form-control-sm me-2" type="search" name="query"
                                   placeholder="Search houses..." aria-label="Search" style="width: 200px;">
                            <button class="btn btn-outline-dark btn-sm" type="submit">Search</button>
                            <button id="closeSearch" class="btn btn-link text-dark btn-sm ms-1" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </li>

                    <!-- Optional shopping bag -->
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link text-dark">
                            <i class="fas fa-shopping-bag"></i>
                        </a>
                    </li> --}}
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/chat') }}">
                            <i class="fa fa-comments"></i> My Chats
                            @php
                                $unreadCount = Auth::user()->unreadMessagesCount();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endauth

                    <!-- Logout -->
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="nav-link btn text-dark border-0 bg-transparent" style="display:inline;">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>

        </div>
    </nav>
</header>

<br>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.getElementById('searchToggle');
    const searchForm = document.getElementById('searchForm');
    const closeSearch = document.getElementById('closeSearch');

    if (searchToggle && searchForm && closeSearch) {
        // Show search form and hide icon
        searchToggle.addEventListener('click', function() {
            searchToggle.parentElement.style.display = 'none';
            searchForm.style.display = 'block';
            searchForm.querySelector('input').focus();
        });

        // Hide search form and show icon
        closeSearch.addEventListener('click', function() {
            searchForm.style.display = 'none';
            searchToggle.parentElement.style.display = 'block';
        });

        // ESC key closes search
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchForm.style.display === 'block') {
                searchForm.style.display = 'none';
                searchToggle.parentElement.style.display = 'block';
            }
        });
    }
});
</script>

<style>
.nav_search-btn {
    background: transparent;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
}
.nav_search-btn:hover {
    color: #0056b3;
}
#searchForm input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
