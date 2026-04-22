import 'crop.dart';

class Order {
  final int id;
  final String orderNumber;
  final int buyerId;
  final int farmerId;
  final double totalAmount;
  final String status;
  final String deliveryAddress;
  final String paymentMethod;
  final String? paymentReceipt;
  final String? trackingNumber;
  final String? notes;
  final String createdAt;
  final String updatedAt;
  final Buyer buyer;
  final Farmer farmer;
  final List<OrderItem> items;

  Order({
    required this.id,
    required this.orderNumber,
    required this.buyerId,
    required this.farmerId,
    required this.totalAmount,
    required this.status,
    required this.deliveryAddress,
    required this.paymentMethod,
    this.paymentReceipt,
    this.trackingNumber,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
    required this.buyer,
    required this.farmer,
    required this.items,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      orderNumber: json['order_number'],
      buyerId: json['buyer_id'],
      farmerId: json['farmer_id'],
      totalAmount: double.parse(json['total_amount'].toString()),
      status: json['status'],
      deliveryAddress: json['delivery_address'],
      paymentMethod: json['payment_method'],
      paymentReceipt: json['payment_receipt'],
      trackingNumber: json['tracking_number'],
      notes: json['notes'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
      buyer: Buyer.fromJson(json['buyer']),
      farmer: Farmer.fromJson(json['farmer']),
      items: (json['items'] as List<dynamic>?)
          ?.map((item) => OrderItem.fromJson(item))
          .toList() ?? [],
    );
  }

  String get statusDisplay {
    switch (status) {
      case 'pending':
        return 'Pending';
      case 'confirmed':
        return 'Confirmed';
      case 'shipped':
        return 'Shipped';
      case 'delivered':
        return 'Delivered';
      case 'cancelled':
        return 'Cancelled';
      default:
        return status;
    }
  }
}

class OrderItem {
  final int id;
  final int orderId;
  final int cropId;
  final int farmerId;
  final int quantity;
  final double pricePerUnit;
  final double totalPrice;
  final String createdAt;
  final String updatedAt;
  final Crop? crop;

  OrderItem({
    required this.id,
    required this.orderId,
    required this.cropId,
    required this.farmerId,
    required this.quantity,
    required this.pricePerUnit,
    required this.totalPrice,
    required this.createdAt,
    required this.updatedAt,
    this.crop,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'],
      orderId: json['order_id'],
      cropId: json['crop_id'],
      farmerId: json['farmer_id'],
      quantity: json['quantity'],
      pricePerUnit: double.parse(json['price_per_unit'].toString()),
      totalPrice: double.parse(json['total_price'].toString()),
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
      crop: json['crop'] != null ? Crop.fromJson(json['crop']) : null,
    );
  }
}

class Buyer {
  final int id;
  final String name;
  final String email;
  final String phone;

  Buyer({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
  });

  factory Buyer.fromJson(Map<String, dynamic> json) {
    return Buyer(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
    );
  }
}

class Farmer {
  final int id;
  final String name;
  final String email;
  final String phone;

  Farmer({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
  });

  factory Farmer.fromJson(Map<String, dynamic> json) {
    return Farmer(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
    );
  }
}

