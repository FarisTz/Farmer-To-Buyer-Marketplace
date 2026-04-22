@extends('layouts.marketplace')

@section('title', 'Register')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">
                            <i class="fas fa-user-plus me-2 text-success"></i>Create Account
                        </h3>
                        <p class="text-muted">Join our farming community today</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Role Selection -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">I am a:</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="role_farmer" value="farmer" required>
                                        <label class="form-check-label" for="role_farmer">
                                            <i class="fas fa-seedling me-1"></i>Farmer
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="role_buyer" value="buyer" checked required>
                                        <label class="form-check-label" for="role_buyer">
                                            <i class="fas fa-shopping-cart me-1"></i>Buyer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Region -->
                        <div class="mb-3">
                            <label for="region" class="form-label">Region/State</label>
                            <select class="form-select @error('region') is-invalid @enderror" id="region" name="region">
                                
                                <option value="">Select Region</option>
                                        <optgroup label="Mainland Tanzania">
                                            <option value="Arusha" {{ old('region') == 'Arusha' ? 'selected' : '' }}>Arusha</option>
                                            <option value="Dar es Salaam" {{ old('region') == 'Dar es Salaam' ? 'selected' : '' }}>Dar es Salaam</option>
                                            <option value="Dodoma" {{ old('region') == 'Dodoma' ? 'selected' : '' }}>Dodoma</option>
                                            <option value="Geita" {{ old('region') == 'Geita' ? 'selected' : '' }}>Geita</option>
                                            <option value="Iringa" {{ old('region') == 'Iringa' ? 'selected' : '' }}>Iringa</option>
                                            <option value="Kagera" {{ old('region') == 'Kagera' ? 'selected' : '' }}>Kagera</option>
                                            <option value="Katavi" {{ old('region') == 'Katavi' ? 'selected' : '' }}>Katavi</option>
                                            <option value="Kigoma" {{ old('region') == 'Kigoma' ? 'selected' : '' }}>Kigoma</option>
                                            <option value="Kilimanjaro" {{ old('region') == 'Kilimanjaro' ? 'selected' : '' }}>Kilimanjaro</option>
                                            <option value="Lindi" {{ old('region') == 'Lindi' ? 'selected' : '' }}>Lindi</option>
                                            <option value="Manyara" {{ old('region') == 'Manyara' ? 'selected' : '' }}>Manyara</option>
                                            <option value="Mara" {{ old('region') == 'Mara' ? 'selected' : '' }}>Mara</option>
                                            <option value="Mbeya" {{ old('region') == 'Mbeya' ? 'selected' : '' }}>Mbeya</option>
                                            <option value="Morogoro" {{ old('region') == 'Morogoro' ? 'selected' : '' }}>Morogoro</option>
                                            <option value="Mtwara" {{ old('region') == 'Mtwara' ? 'selected' : '' }}>Mtwara</option>
                                            <option value="Mwanza" {{ old('region') == 'Mwanza' ? 'selected' : '' }}>Mwanza</option>
                                            <option value="Njombe" {{ old('region') == 'Njombe' ? 'selected' : '' }}>Njombe</option>
                                            <option value="Pwani" {{ old('region') == 'Pwani' ? 'selected' : '' }}>Pwani (Coast Region)</option>
                                            <option value="Rukwa" {{ old('region') == 'Rukwa' ? 'selected' : '' }}>Rukwa</option>
                                            <option value="Ruvuma" {{ old('region') == 'Ruvuma' ? 'selected' : '' }}>Ruvuma</option>
                                            <option value="Shinyanga" {{ old('region') == 'Shinyanga' ? 'selected' : '' }}>Shinyanga</option>
                                            <option value="Simiyu" {{ old('region') == 'Simiyu' ? 'selected' : '' }}>Simiyu</option>
                                            <option value="Singida" {{ old('region') == 'Singida' ? 'selected' : '' }}>Singida</option>
                                            <option value="Songwe" {{ old('region') == 'Songwe' ? 'selected' : '' }}>Songwe</option>
                                            <option value="Tabora" {{ old('region') == 'Tabora' ? 'selected' : '' }}>Tabora</option>
                                            <option value="Tanga" {{ old('region') == 'Tanga' ? 'selected' : '' }}>Tanga</option>
                                        </optgroup>
                                        <optgroup label="Zanzibar ">
                                            <option value="Zanzibar" {{ old('region') == 'Zanzibar' ? 'selected' : '' }}>Zanzibar</option>
                                            <option value="Kaskazini Unguja" {{ old('region') == 'Kaskazini Unguja' ? 'selected' : '' }}>Kaskazini Unguja (North Unguja)</option>
                                            <option value="Kusini Unguja" {{ old('region') == 'Kusini Unguja' ? 'selected' : '' }}>Kusini Unguja (South Unguja)</option>
                                            <option value="Mjini Magharibi" {{ old('region') == 'Mjini Magharibi' ? 'selected' : '' }}>Mjini Magharibi (Urban West)</option>
                                            <option value="Kaskazini Pemba" {{ old('region') == 'Kaskazini Pemba' ? 'selected' : '' }}>Kaskazini Pemba (North Pemba)</option>
                                            <option value="Kusini Pemba" {{ old('region') == 'Kusini Pemba' ? 'selected' : '' }}>Kusini Pemba (South Pemba)</option>
                                        </optgroup>

                            </select>
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                Already have an account? Login
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
