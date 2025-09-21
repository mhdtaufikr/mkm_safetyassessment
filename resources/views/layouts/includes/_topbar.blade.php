<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
    <!-- Sidenav Toggle Button-->
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle">
        <i data-feather="menu"></i>
    </button>

    <!-- Logo Kiri -->
    <div class="ms-2 preloader flex-column justify-content-center align-items-center">
        <a href="#"><img class="animation__shake" src="{{ asset('assets/img/topbar.png') }}" alt="MKM Logo" height="40%" width="40%"></a>
    </div>

    <!-- Judul -->
    <h1 style="margin-left: -160px">Safety Assessment & 5S</h1>

    <!-- Navbar Items -->
    <ul class="navbar-nav align-items-center ms-auto">
        <!-- User Dropdown -->
        <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle"
               id="userDropdown"
               href="javascript:void(0);"
               role="button"
               data-bs-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">
                <img class="img-fluid" src="{{ asset('assets/img/illustrations/profiles/profile-1.png') }}" />
            </a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                 aria-labelledby="userDropdown">
                <h6 class="dropdown-header d-flex align-items-center">
                    <img class="dropdown-user-img" src="{{ asset('assets/img/illustrations/profiles/profile-1.png') }}" />
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">{{ auth()->user()->name }}</div>
                        <div class="dropdown-user-details-email">{{ auth()->user()->email }}</div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <div class="dropdown-item-icon"><i data-feather="key"></i></div>
                    Change Password
                </a>
                <a class="dropdown-item" href="{{ url('/logout') }}">
                    <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
