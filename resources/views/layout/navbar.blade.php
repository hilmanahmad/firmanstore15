<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="iq-menu-bt d-flex align-items-center">
                <div class="wrapper-menu">
                    <div class="main-circle"><i class="ri-menu-line"></i></div>
                    <div class="hover-circle"><i class="ri-close-fill"></i></div>
                </div>
                <div class="iq-navbar-logo d-flex justify-content-between ml-3">
                    <a href="index.html" class="header-logo">
                        <img src="../assets/images/logo.png" class="img-fluid rounded" alt="">
                    </a>
                </div>
            </div>
            <div class="iq-search-bar">
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-list">

                    <li class="nav-item nav-icon">
                        <a href="#" class="iq-waves-effect bg-danger rounded"><span
                                class="ripple rippleeffect"></span><i class="ri-heart-line"></i></a>
                    </li>
                </ul>
            </div>
            <ul class="navbar-list">
                <li class="line-height">
                    <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                        <img src="../assets/images/user/1.jpg" class="img-fluid rounded mr-3" alt="user">
                        <div class="caption">
                            <h6 class="mb-0 line-height">
                                {{ auth()->user()->name }}
                            </h6>
                        </div>
                    </a>
                    <div class="iq-sub-dropdown iq-user-dropdown">
                        <div class="iq-card shadow-none m-0">
                            <div class="iq-card-body p-0 ">
                                <div class="bg-primary p-3">
                                    <h5 class="mb-0 text-white line-height">Hello {{ auth()->user()->name }}</h5>

                                </div>
                                <div class="d-inline-block w-100 text-center p-3">
                                    <a class="bg-primary iq-sign-btn" href="{{ route('logout') }}" role="button"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Sign
                                        out<i class="ri-login-box-line ml-2"></i></a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="GET" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>
