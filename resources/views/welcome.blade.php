@extends('layouts.marketplace')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">
            <i class="fas fa-seedling me-3"></i>FarmMarket
        </h1>
        <p class="lead mb-4">Connect directly with local farmers for fresh, affordable produce</p>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('crops.public') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-carrot me-2"></i>Browse Crops
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Join Now
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold">Why Choose FarmMarket?</h2>
                <p class="lead text-muted">Fresh produce, fair prices, direct from farmers</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                    <h4>Direct Connection</h4>
                    <p class="text-muted">Connect directly with local farmers, eliminating middlemen and ensuring fair prices.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-leaf fa-2x text-success"></i>
                    </div>
                    <h4>Fresh Produce</h4>
                    <p class="text-muted">Get fresh, high-quality crops directly from the source, harvested at peak freshness.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-handshake fa-2x text-success"></i>
                    </div>
                    <h4>Fair Trade</h4>
                    <p class="text-muted">Support local farmers while getting competitive prices for quality agricultural products.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold">How It Works</h2>
                <p class="lead text-muted">Simple steps to buy and sell agricultural products</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <span class="fw-bold text-success fs-4">1</span>
                    </div>
                    <h5>Register</h5>
                    <p class="text-muted small">Create an account as a farmer or buyer</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <span class="fw-bold text-success fs-4">2</span>
                    </div>
                    <h5>Browse/List</h5>
                    <p class="text-muted small">Farmers list crops, buyers browse available produce</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <span class="fw-bold text-success fs-4">3</span>
                    </div>
                    <h5>Order</h5>
                    <p class="text-muted small">Buyers place orders for desired crops and quantities</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <span class="fw-bold text-success fs-4">4</span>
                    </div>
                    <h5>Deliver</h5>
                    <p class="text-muted small">Farmers deliver fresh produce to buyers</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-success text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Join our community of farmers and buyers today!</p>
        @guest
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Register Now
                </a>
                <a href="{{ route('crops.public') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-eye me-2"></i>Browse Crops
                </a>
            </div>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
            </a>
        @endguest
    </div>
</section>
@endsection
