@extends('layouts.marketplace')

@section('title', 'Edit Crop - ' . $crop->name)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2 text-success"></i>Edit Crop
            </h1>
            <p class="text-muted mb-0">Update crop information and availability</p>
        </div>
        <div>
            <a href="{{ route('farmer.crops.show', $crop) }}" class="btn btn-outline-info me-2">
                <i class="fas fa-eye me-2"></i>View Crop
            </a>
            <a href="{{ route('farmer.crops.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Crops
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Validation Error Summary -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Please fix the following errors:
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('farmer.crops.update', $crop) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Crop Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $crop->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Vegetables" {{ old('category', $crop->category) == 'Vegetables' ? 'selected' : '' }}>Vegetables</option>
                                        <option value="Fruits" {{ old('category', $crop->category) == 'Fruits' ? 'selected' : '' }}>Fruits</option>
                                        <option value="Grains" {{ old('category', $crop->category) == 'Grains' ? 'selected' : '' }}>Grains</option>
                                        <option value="Legumes" {{ old('category', $crop->category) == 'Legumes' ? 'selected' : '' }}>Legumes</option>
                                        <option value="Tubers" {{ old('category', $crop->category) == 'Tubers' ? 'selected' : '' }}>Tubers</option>
                                        <option value="Spices" {{ old('category', $crop->category) == 'Spices' ? 'selected' : '' }}>Spices</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" required>{{ old('description', $crop->description) }}</textarea>
                                    <small class="text-muted">Describe your crop quality, variety, growing conditions, etc.</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pricing and Quantity -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-tag me-2"></i>Pricing & Quantity
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="price_per_kg" class="form-label">Price per KG *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">TZS</span>
                                        <input type="number" class="form-control @error('price_per_kg') is-invalid @enderror" 
                                               id="price_per_kg" name="price_per_kg" value="{{ old('price_per_kg', $crop->price_per_kg) }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="available_quantity" class="form-label">Available Quantity (KG) *</label>
                                    <input type="number" class="form-control @error('available_quantity') is-invalid @enderror" 
                                           id="available_quantity" name="available_quantity" value="{{ old('available_quantity', $crop->available_quantity) }}" 
                                           step="0.1" min="0" required>
                                    @error('available_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="unit" class="form-label">Unit</label>
                                    <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                        <option value="kg" {{ old('unit', $crop->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                        <option value="ton" {{ old('unit', $crop->unit) == 'ton' ? 'selected' : '' }}>Ton</option>
                                        <option value="basket" {{ old('unit', $crop->unit) == 'basket' ? 'selected' : '' }}>Basket</option>
                                        <option value="bag" {{ old('unit', $crop->unit) == 'bag' ? 'selected' : '' }}>Bag</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Location Information
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="region" class="form-label">Region *</label>
                                    <select class="form-select @error('region') is-invalid @enderror" id="region" name="region" required>
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
                                
                                <div class="col-md-6">
                                    <label for="farm_location" class="form-label">Farm Location</label>
                                    <input type="text" class="form-control @error('farm_location') is-invalid @enderror" 
                                           id="farm_location" name="farm_location" value="{{ old('farm_location', $crop->farm_location) }}" 
                                           placeholder="Specific farm address or landmark">
                                    @error('farm_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-image me-2"></i>Crop Image
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="image" class="form-label">Current Image</label>
                                    <div class="mb-2">
                                        <img src="{{ $crop->image_url }}" alt="{{ $crop->name }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <label for="image" class="form-label">Upload New Image (Optional)</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <small class="text-muted">Leave empty to keep current image</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Image Tips:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Use clear, recent photos</li>
                                            <li>Show actual product quality</li>
                                            <li>Good lighting is essential</li>
                                            <li>Include scale reference</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Availability Settings -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="fas fa-toggle-on me-2"></i>Availability Settings
                            </h5>
                            
                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="is_available" value="0">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1"
                                       {{ old('is_available', $crop->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    <strong>Available for Sale</strong>
                                    <br>
                                    <small class="text-muted">Uncheck if crop is temporarily out of stock</small>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('farmer.crops.show', $crop) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update Crop
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Crop Performance
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Orders:</span>
                            <strong>{{ $crop->orderItems->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Quantity Sold:</span>
                            <strong>{{ $crop->orderItems->sum('quantity') }} kg</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Revenue:</span>
                            <strong class="text-success">₦{{ number_format($crop->orderItems->sum('total_price'), 2) }}</strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Quick Tips
                        </h6>
                        <ul class="mb-0 mt-2">
                            <li>Update quantity regularly</li>
                            <li>Adjust prices based on demand</li>
                            <li>Keep photos current</li>
                            <li>Respond to orders quickly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
