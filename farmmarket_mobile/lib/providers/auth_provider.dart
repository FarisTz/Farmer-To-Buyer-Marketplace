import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider extends ChangeNotifier {
  User? _user;
  bool _isLoading = false;
  String? _errorMessage;

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isLoggedIn => _user != null;
  bool get isFarmer => _user?.isFarmer ?? false;
  bool get isBuyer => _user?.isBuyer ?? false;
  bool get isVerified => _user?.isVerified ?? false;

  Future<void> loadUser() async {
    _setLoading(true);
    try {
      final userData = await ApiService.getUserData();
      if (userData != null) {
        _user = User.fromJson(userData);
        notifyListeners();
      }
    } catch (e) {
      _setError('Failed to load user data');
    } finally {
      _setLoading(false);
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String phone,
    required String role,
    required String password,
    required String passwordConfirmation,
  }) async {
    _setLoading(true);
    _clearError();

    try {
      final result = await ApiService.register(
        name: name,
        email: email,
        phone: phone,
        role: role,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );

      if (result['success']) {
        _user = User.fromJson(result['data']['user']);
        notifyListeners();
        return true;
      } else {
        _setError(result['message'] ?? 'Registration failed');
        return false;
      }
    } catch (e) {
      _setError('Registration failed: $e');
      return false;
    } finally {
      _setLoading(false);
    }
  }

  Future<bool> login({
    required String email,
    required String password,
  }) async {
    _setLoading(true);
    _clearError();

    try {
      final result = await ApiService.login(email: email, password: password);

      if (result['success']) {
        _user = User.fromJson(result['data']['user']);
        notifyListeners();
        return true;
      } else {
        _setError(result['message'] ?? 'Login failed');
        return false;
      }
    } catch (e) {
      _setError('Login failed: $e');
      return false;
    } finally {
      _setLoading(false);
    }
  }

  Future<bool> updateProfile({
    String? name,
    String? phone,
    String? address,
  }) async {
    _setLoading(true);
    _clearError();

    try {
      final result = await ApiService.updateProfile(
        name: name,
        phone: phone,
        address: address,
      );

      if (result['success']) {
        _user = User.fromJson(result['data']['user']);
        notifyListeners();
        return true;
      } else {
        _setError(result['message'] ?? 'Profile update failed');
        return false;
      }
    } catch (e) {
      _setError('Profile update failed: $e');
      return false;
    } finally {
      _setLoading(false);
    }
  }

  Future<void> logout() async {
    _setLoading(true);
    try {
      await ApiService.logout();
      _user = null;
      notifyListeners();
    } catch (e) {
      _user = null;
      notifyListeners();
    } finally {
      _setLoading(false);
    }
  }

  Future<void> checkAuthStatus() async {
    final isLoggedIn = await ApiService.isLoggedIn();
    if (isLoggedIn && _user == null) {
      await loadUser();
    } else if (!isLoggedIn) {
      _user = null;
      notifyListeners();
    }
  }

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void _setError(String error) {
    _errorMessage = error;
    notifyListeners();
  }

  void _clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
