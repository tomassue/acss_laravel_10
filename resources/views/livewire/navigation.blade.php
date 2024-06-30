<div>
    <!-- Custom CSS -->
    <style>
        .sidebar {
            background-color: #111827;
        }

        .sidebar-nav .nav-link:hover {
            color: #dadce0;
            background: #1a2231;
        }

        .sidebar-nav .nav-link.collapsed {
            color: #dadce0;
            background: #1a2231;
        }

        .sidebar-nav .nav-link {
            color: #f3f4f6;
            background: #111827;
        }

        .sidebar-nav .nav-link:hover i {
            color: #f3f4f6;
        }

        .sidebar-nav .nav-link i {
            color: #f3f4f6;
        }

        .card-title {
            color: #000;
        }

        .card-body {
            padding: 0 20px 0px 20px;
        }

        .list-group {
            max-height: 800px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #aab7cf transparent;
            box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1);
            -webkit-overflow-scrolling: touch;
        }

        .list-group-item.list-group-item {
            background-color: #374151;
            color: #f6f9ff;
            margin-bottom: 5px;
            padding: 20px 16px 20px 16px;
        }

        .list-group-item.list-group-item:hover {
            background-color: rgb(31 41 55);
            padding: 20px 16px 20px 16px;
        }

        .list-group-item.active {
            background-color: rgb(31 41 55);
            color: #f6f9ff;
            margin-bottom: 5px;
            border-color: rgb(31 41 55);
            padding: 20px 16px 20px 16px;
        }

        /* Users Page */
        .item-list-scroll {
            height: 600px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #aab7cf transparent;
            box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1);
            -webkit-overflow-scrolling: touch;
        }

        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* End Users Page */

        .custom-invalid-feedback {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: var(--bs-form-invalid-color);
        }
    </style>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center border-bottom border-black border-4">

        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('img/logotrans.png') }}" alt="" style="max-height: 50px;">
                <span class="d-none d-lg-block fs-6">Automated Class Scheduling System | XU Computer Studies</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Search Bar -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="{{ asset('img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2 text-black">{{ Auth::user()->name }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ Auth::user()->name }}</h6>
                            <span>{{ Auth::user()->role }}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'collapsed' : '' }}" href="{{ route('dashboard') }}">
                    <i class="ri-pie-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rooms') ? 'collapsed' : '' }}" href="{{ route('rooms') }}">
                    <i class="ri-home-8-line"></i>
                    <span>Rooms</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('courses') ? 'collapsed' : '' }}" href="{{ route('courses') }}">
                    <i class="ri-file-copy-2-line"></i>
                    <span>Courses</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('faculty-schedules') ? 'collapsed' : '' }}" href="{{ route('faculty-schedules') }}">
                    <i class="ri-user-2-line"></i>
                    <span>Faculty Schedules</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="ri-user-line"></i>
                    <span>Student Schedules</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users') ? 'collapsed' : '' }}" href="{{ route('users') }}">
                    <i class="ri-user-star-line"></i>
                    <span>Users</span>
                </a>
            </li><!-- End Profile Page Nav -->

        </ul>

    </aside><!-- End Sidebar-->
</div>