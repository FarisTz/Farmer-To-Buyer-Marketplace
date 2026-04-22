<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Farmer Marketplace') }} - @yield('title', 'Home')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.19/sweetalert2.min.css">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 4rem 0;
        }
        .crop-card {
            transition: transform 0.2s;
        }
        .crop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-seedling me-2"></i>FarmMarket
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('crops.public') }}">
                            <i class="fas fa-carrot me-1"></i>Browse Crops
                        </a>
                    </li>
                    @auth
                        @if(Auth::user()->isFarmer())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('farmer.crops.index') }}">
                                    <i class="fas fa-seedling me-1"></i>My Crops
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('farmer.orders') }}">
                                    <i class="fas fa-shopping-bag me-1"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('farmer.payment-receipts') }}">
                                    <i class="fas fa-money-check-alt me-1"></i>Payments
                                </a>
                            </li>
                        @elseif(Auth::user()->isBuyer())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('buyer.cart') }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Cart
                                    @if(session('cart'))
                                        <span class="badge bg-danger">{{ count(session('cart')) }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('buyer.orders') }}">
                                    <i class="fas fa-receipt me-1"></i>My Orders
                                </a>
                            </li>
                        @elseif(Auth::user()->isAdmin())
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.statistics') }}">
                                    <i class="fas fa-chart-line me-1"></i>Statistics
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.activities') }}">
                                    <i class="fas fa-history me-1"></i>System Activity
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm ms-2" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </ul>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(Auth::user()->isAdmin())
                                    
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">
                                        <i class="fas fa-users me-1"></i>Manage Users
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.verifications.index') }}">
                                        <i class="fas fa-shield-alt me-1"></i>Verifications
                                    </a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('verification.index') }}">
                                        <i class="fas fa-shield-alt me-1"></i>Verify Account
                                    </a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user me-1"></i>My Profile
                                </a></li>
                                
                                @if(Auth::user()->isBuyer())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('buyer.cart') }}">
                                        <i class="fas fa-shopping-cart me-1"></i>Cart
                                        @if(session('cart'))
                                            <span class="badge bg-danger">{{ count(session('cart')) }}</span>
                                        @endif
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('buyer.orders') }}">
                                        <i class="fas fa-receipt me-1"></i>My Orders
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('buyer.chats.index') }}">
                                        <i class="fas fa-comments me-1"></i>Messages
                                        <span class="badge bg-primary" id="unread-badge" style="display: none;">0</span>
                                    </a></li>
                                @endif
                                
                                @if(Auth::user()->isFarmer())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer.crops.index') }}">
                                        <i class="fas fa-seedling me-1"></i>My Crops
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer.orders') }}">
                                        <i class="fas fa-shopping-bag me-1"></i>Orders
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer.payment-receipts') }}">
                                        <i class="fas fa-money-check-alt me-1"></i>Payment Receipts
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer.chats.index') }}">
                                        <i class="fas fa-comments me-1"></i>Messages
                                        <span class="badge bg-primary" id="unread-badge" style="display: none;">0</span>
                                    </a></li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-seedling me-2"></i>FarmMarket</h5>
                    <p>Connecting farmers directly with buyers for fresh, affordable produce.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light">Home</a></li>
                        <li><a href="{{ route('crops.public') }}" class="text-light">Browse Crops</a></li>
                        <li><a href="{{ route('register') }}" class="text-light">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p><i class="fas fa-envelope me-2"></i>info@farmmarket.com</p>
                    <p><i class="fas fa-phone me-2"></i>+255 772 470 544</p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} FarmMarket. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.19/sweetalert2.min.js"></script>
    
    <!-- Notification System -->
    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // SweetAlert2 Configuration
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Global notification functions
        window.showSuccess = function(message) {
            toastr.success(message);
        };

        window.showError = function(message) {
            toastr.error(message);
        };

        window.showWarning = function(message) {
            toastr.warning(message);
        };

        window.showInfo = function(message) {
            toastr.info(message);
        };

        window.showConfirm = function(message, callback) {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Flash message handling
        @if(session()->has('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if(session()->has('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if(session()->has('warning'))
            toastr.warning('{{ session('warning') }}');
        @endif

        @if(session()->has('info'))
            toastr.info('{{ session('info') }}');
        @endif
    </script>
    
    <!-- Chatbot Widget -->
    @include('chatbot.widget')
</body>
</html>
