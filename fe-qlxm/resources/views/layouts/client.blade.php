<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('description', 'Hệ thống quản lý xe máy chuyên nghiệp')">
    <meta name="author" content="QLXM">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/icons/iconqlxm.jpg') }}" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap"
        rel="stylesheet">

    <title>@yield('title', 'QLXM - Quản Lý Xe Máy')</title>

    <!-- Bootstrap core CSS -->
    <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="/css/fontawesome.css">
    <link rel="stylesheet" href="/css/templatemo-sixteen.css">
    <link rel="stylesheet" href="/css/owl.css">
    <link rel="stylesheet" href="/css/chatbot.css">

    <style>
        /* Scroll to Top Button */
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #ff4444, #ff6666);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            transform: translateY(20px);
        }
        
        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .scroll-to-top:hover {
            background: linear-gradient(45deg, #ff3333, #ff5555);
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.5);
        }
        
        .scroll-to-top:active {
            transform: translateY(-2px);
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Header -->
    <header class="">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ route('client.home') }}">
                    <h2>QLXM <em>System</em></h2>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                    aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item {{ request()->routeIs('client.home') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('client.home') }}">Trang chủ
                                @if (request()->routeIs('client.home'))
                                    <span class="sr-only">(current)</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('client.motorcycles*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('client.motorcycles') }}">Xe Máy</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('client.brands*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('client.brands') }}">Hãng Xe</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('client.about') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('client.about') }}">Về Chúng Tôi</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Đăng Nhập</a>
                            </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.cart.index') }}">
                                <i class="fa fa-shopping-cart"></i> Giỏ hàng
                                <span id="cart-count" class="badge badge-danger" style="display: none;">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    @yield('content')

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="inner-content">
                        <p>Copyright &copy; {{ date('Y') }} QLXM - Quản Lý Xe Máy
                            - Thiết kế: <a rel="nofollow noopener" href="#" target="_blank">QLXM Team</a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="scroll-to-top" title="Lên đầu trang">
        <i class="fa fa-chevron-up"></i>
    </button>

    <!-- Bootstrap core JavaScript -->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="/js/custom.js"></script>
    <script src="/js/owl.js"></script>
    <script src="/js/slick.js"></script>
    <script src="/js/isotope.js"></script>
    <script src="/js/accordions.js"></script>
    <script src="/js/chatbot.js"></script>

    <script language="text/Javascript">
        cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
        function clearField(t) { //declaring the array outside of the
            if (!cleared[t.id]) { // function makes it static and global
                cleared[t.id] = 1; // you could use true and false, but that's more typing
                t.value = ''; // with more chance of typos
                t.style.color = '#fff';
            }
        }
    </script>
    
    <script>
        // Scroll to Top Button
        document.addEventListener('DOMContentLoaded', function() {
            const scrollBtn = document.getElementById('scrollToTopBtn');
            
            if (scrollBtn) {
                // Show button when user scrolls down 300px
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300 || document.documentElement.scrollTop > 300) {
                        scrollBtn.classList.add('show');
                    } else {
                        scrollBtn.classList.remove('show');
                    }
                });
                
                // Scroll to top when button clicked
                scrollBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    // Fallback for browsers that don't support smooth scroll
                    document.body.scrollTop = 0; // For Safari
                    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
                });
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
