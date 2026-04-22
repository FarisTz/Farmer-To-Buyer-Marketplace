import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api/v1';
  static const storage = FlutterSecureStorage();

  // Headers for authenticated requests
  static Future<Map<String, String>> _getHeaders() async {
    final token = await storage.read(key: 'auth_token');
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  // Headers for public requests
  static Map<String, String> _getPublicHeaders() {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
  }

  // Authentication endpoints
  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String phone,
    required String role,
    required String password,
    required String passwordConfirmation,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/register'),
        headers: _getPublicHeaders(),
        body: jsonEncode({
          'name': name,
          'email': email,
          'phone': phone,
          'role': role,
          'password': password,
          'password_confirmation': passwordConfirmation,
        }),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 201 && data['success']) {
        await storage.write(key: 'auth_token', value: data['data']['token']);
        await _saveUserData(data['data']['user']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'], 'errors': data['errors']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/login'),
        headers: _getPublicHeaders(),
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        await storage.write(key: 'auth_token', value: data['data']['token']);
        await _saveUserData(data['data']['user']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'], 'errors': data['errors']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/auth/profile'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        await _saveUserData(data['data']['user']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> updateProfile({
    String? name,
    String? phone,
    String? address,
  }) async {
    try {
      final Map<String, dynamic> body = {};
      if (name != null) body['name'] = name;
      if (phone != null) body['phone'] = phone;
      if (address != null) body['address'] = address;

      final response = await http.put(
        Uri.parse('$baseUrl/auth/profile'),
        headers: await _getHeaders(),
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        await _saveUserData(data['data']['user']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'], 'errors': data['errors']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> logout() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/logout'),
        headers: await _getHeaders(),
      );

      await storage.delete(key: 'auth_token');
      await _clearUserData();
      
      return {'success': true, 'message': 'Logged out successfully'};
    } catch (e) {
      await storage.delete(key: 'auth_token');
      await _clearUserData();
      return {'success': true, 'message': 'Logged out successfully'};
    }
  }

  // Crops endpoints
  static Future<Map<String, dynamic>> getCrops({
    String? category,
    double? minPrice,
    double? maxPrice,
    String? search,
    String sortBy = 'created_at',
    String sortOrder = 'desc',
    int page = 1,
  }) async {
    try {
      final Map<String, String> queryParams = {
        'page': page.toString(),
        'sort_by': sortBy,
        'sort_order': sortOrder,
      };
      
      if (category != null) queryParams['category'] = category;
      if (minPrice != null) queryParams['min_price'] = minPrice.toString();
      if (maxPrice != null) queryParams['max_price'] = maxPrice.toString();
      if (search != null) queryParams['search'] = search;

      final uri = Uri.parse('$baseUrl/crops').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: _getPublicHeaders());

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getCropDetails(int cropId) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/crops/$cropId'),
        headers: _getPublicHeaders(),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getCategories() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/categories'),
        headers: _getPublicHeaders(),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> getMyCrops({
    String? available,
    String? category,
    int page = 1,
  }) async {
    try {
      final Map<String, String> queryParams = {'page': page.toString()};
      
      if (available != null) queryParams['available'] = available;
      if (category != null) queryParams['category'] = category;

      final uri = Uri.parse('$baseUrl/my-crops').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // Orders endpoints
  static Future<Map<String, dynamic>> getOrders({
    String? status,
    int page = 1,
  }) async {
    try {
      final Map<String, String> queryParams = {'page': page.toString()};
      
      if (status != null) queryParams['status'] = status;

      final uri = Uri.parse('$baseUrl/orders').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String, dynamic>> createOrder({
    required List<Map<String, dynamic>> items,
    required String deliveryAddress,
    required String paymentMethod,
    String? notes,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'items': items,
          'delivery_address': deliveryAddress,
          'payment_method': paymentMethod,
          'notes': notes,
        }),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 201 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'], 'errors': data['errors']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // Verification endpoints
  static Future<Map<String, dynamic>> getVerificationStatus() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/verification'),
        headers: await _getHeaders(),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // Chat endpoints
  static Future<Map<String, dynamic>> getChats({int page = 1}) async {
    try {
      final uri = Uri.parse('$baseUrl/chats').replace(queryParameters: {'page': page.toString()});
      final response = await http.get(uri, headers: await _getHeaders());

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success']) {
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // Helper methods
  static Future<void> _saveUserData(Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user_data', jsonEncode(user));
  }

  static Future<void> _clearUserData() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_data');
  }

  static Future<Map<String, dynamic>?> getUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final userData = prefs.getString('user_data');
    if (userData != null) {
      return jsonDecode(userData);
    }
    return null;
  }

  static Future<bool> isLoggedIn() async {
    final token = await storage.read(key: 'auth_token');
    return token != null;
  }
}
