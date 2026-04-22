class Crop {
  final int id;
  final String name;
  final String description;
  final double pricePerKg;
  final double availableQuantity;
  final String unit;
  final String category;
  final String region;
  final String? image;
  final bool isAvailable;
  final String createdAt;
  final String updatedAt;
  final Farmer farmer;

  Crop({
    required this.id,
    required this.name,
    required this.description,
    required this.pricePerKg,
    required this.availableQuantity,
    required this.unit,
    required this.category,
    required this.region,
    this.image,
    required this.isAvailable,
    required this.createdAt,
    required this.updatedAt,
    required this.farmer,
  });

  factory Crop.fromJson(Map<String, dynamic> json) {
    return Crop(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      pricePerKg: double.parse(json['price_per_kg'].toString()),
      availableQuantity: double.parse(json['available_quantity'].toString()),
      unit: json['unit'] ?? '',
      category: json['category'] ?? '',
      region: json['region'] ?? '',
      image: json['image'],
      isAvailable: json['is_available'] ?? false,
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
      farmer: Farmer.fromJson(json['farmer'] ?? {}),
    );
  }
}

class Category {
  final int id;
  final String name;
  final String description;
  final int cropsCount;

  Category({
    required this.id,
    required this.name,
    required this.description,
    required this.cropsCount,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'],
      name: json['name'],
      description: json['description'] ?? '',
      cropsCount: json['crops_count'] ?? 0,
    );
  }
}

class Farmer {
  final int id;
  final String name;
  final String email;
  final String phone;
  final bool isVerified;

  Farmer({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.isVerified,
  });

  factory Farmer.fromJson(Map<String, dynamic> json) {
    return Farmer(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      isVerified: json['is_verified'] ?? false,
    );
  }
}

class CropImage {
  final int id;
  final String imagePath;

  CropImage({
    required this.id,
    required this.imagePath,
  });

  factory CropImage.fromJson(Map<String, dynamic> json) {
    return CropImage(
      id: json['id'],
      imagePath: json['image_path'],
    );
  }

  String get fullImageUrl => 'http://127.0.0.1:8000/storage/$imagePath';
}
