class User {
  final int id;
  final String name;
  final String email;
  final String phone;
  final String? address;
  final String role;
  final bool isVerified;
  final String? verificationStatus;
  final String createdAt;
  final String? updatedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    this.address,
    required this.role,
    required this.isVerified,
    this.verificationStatus,
    required this.createdAt,
    this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      address: json['address'],
      role: json['role'] ?? '',
      isVerified: json['is_verified'] ?? false,
      verificationStatus: json['verification_status'],
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'],
    );
  }

  bool get isFarmer => role == 'farmer';
  bool get isBuyer => role == 'buyer';
  bool get isAdmin => role == 'admin';
}

class UserVerification {
  final int? id;
  final int userId;
  final String? idType;
  final String? idNumber;
  final String? idFrontImage;
  final String? idBackImage;
  final String? selfieImage;
  final String? phoneNumber;
  final String? phoneVerificationCode;
  final String? phoneCodeExpiresAt;
  final String? verificationDocument;
  final String? addressProofImage;
  final String? idStatus;
  final String? phoneStatus;
  final String? addressStatus;
  final String createdAt;
  final String updatedAt;

  UserVerification({
    this.id,
    required this.userId,
    this.idType,
    this.idNumber,
    this.idFrontImage,
    this.idBackImage,
    this.selfieImage,
    this.phoneNumber,
    this.phoneVerificationCode,
    this.phoneCodeExpiresAt,
    this.verificationDocument,
    this.addressProofImage,
    this.idStatus,
    this.phoneStatus,
    this.addressStatus,
    required this.createdAt,
    required this.updatedAt,
  });

  factory UserVerification.fromJson(Map<String, dynamic> json) {
    return UserVerification(
      id: json['id'],
      userId: json['user_id'],
      idType: json['id_type'],
      idNumber: json['id_number'],
      idFrontImage: json['id_front_image'],
      idBackImage: json['id_back_image'],
      selfieImage: json['selfie_image'],
      phoneNumber: json['phone_number'],
      phoneVerificationCode: json['phone_verification_code'],
      phoneCodeExpiresAt: json['phone_code_expires_at'],
      verificationDocument: json['verification_document'],
      addressProofImage: json['address_proof_image'],
      idStatus: json['id_status'],
      phoneStatus: json['phone_status'],
      addressStatus: json['address_status'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
    );
  }

  bool get isIdVerified => idStatus == 'verified';
  bool get isPhoneVerified => phoneStatus == 'verified';
  bool get isAddressVerified => addressStatus == 'verified';
  bool get isFullyVerified => isIdVerified && isPhoneVerified && isAddressVerified;

  String? get idFrontImageUrl => idFrontImage != null ? 'http://127.0.0.1:8000/storage/$idFrontImage' : null;
  String? get idBackImageUrl => idBackImage != null ? 'http://127.0.0.1:8000/storage/$idBackImage' : null;
  String? get selfieImageUrl => selfieImage != null ? 'http://127.0.0.1:8000/storage/$selfieImage' : null;
  String? get addressProofImageUrl => addressProofImage != null ? 'http://127.0.0.1:8000/storage/$addressProofImage' : null;
}
