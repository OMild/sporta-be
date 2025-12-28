import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/models.dart';
import '../utils/constants.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  Dio? _dio;
  static const FlutterSecureStorage _storage = FlutterSecureStorage();

  void initialize() {
    if (_dio != null) return; // Prevent re-initialization
    
    _dio = Dio(BaseOptions(
      baseUrl: Constants.apiBaseUrl,
      connectTimeout: Duration(milliseconds: Constants.connectTimeout),
      receiveTimeout: Duration(milliseconds: Constants.receiveTimeout),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Add interceptor for automatic Bearer token injection
    _dio!.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await getToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        // Handle 401 unauthorized errors by clearing token
        if (error.response?.statusCode == 401) {
          await clearToken();
        }
        handler.next(error);
      },
    ));
  }

  Dio get dio {
    if (_dio == null) {
      initialize();
    }
    return _dio!;
  }

  // Token management methods
  Future<String?> getToken() async {
    return await _storage.read(key: Constants.tokenKey);
  }

  Future<void> saveToken(String token) async {
    await _storage.write(key: Constants.tokenKey, value: token);
  }

  Future<void> clearToken() async {
    await _storage.delete(key: Constants.tokenKey);
    await _storage.delete(key: Constants.userKey);
  }

  // User data management
  Future<void> saveUser(UserModel user) async {
    await _storage.write(key: Constants.userKey, value: user.toJson().toString());
  }
  // Authentication methods
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200) {
        final data = response.data;
        final token = data['token'] as String;
        final userData = data['user'] as Map<String, dynamic>;
        
        // Save token and user data
        await saveToken(token);
        final user = UserModel.fromJson(userData);
        await saveUser(user);
        
        return {
          'success': true,
          'token': token,
          'user': user,
        };
      } else {
        return {
          'success': false,
          'message': 'Login failed',
        };
      }
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error occurred',
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'An unexpected error occurred',
      };
    }
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    try {
      final response = await dio.post('/register', data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
        if (phone != null) 'phone': phone,
      });

      if (response.statusCode == 201) {
        final data = response.data;
        final token = data['token'] as String;
        final userData = data['user'] as Map<String, dynamic>;
        
        // Save token and user data
        await saveToken(token);
        final user = UserModel.fromJson(userData);
        await saveUser(user);
        
        return {
          'success': true,
          'token': token,
          'user': user,
        };
      } else {
        return {
          'success': false,
          'message': 'Registration failed',
        };
      }
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error occurred',
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'An unexpected error occurred',
      };
    }
  }
  Future<Map<String, dynamic>> logout() async {
    try {
      final response = await dio.post('/logout');
      
      // Clear local storage regardless of response
      await clearToken();
      
      return {
        'success': response.statusCode == 200,
        'message': response.statusCode == 200 ? 'Logged out successfully' : 'Logout failed',
      };
    } on DioException catch (e) {
      // Clear local storage even if request fails
      await clearToken();
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error occurred',
      };
    } catch (e) {
      // Clear local storage even if request fails
      await clearToken();
      return {
        'success': false,
        'message': 'An unexpected error occurred',
      };
    }
  }

  // Venue methods
  Future<List<VenueModel>> getVenues({String? search}) async {
    try {
      print('DEBUG: Making API call to ${Constants.apiBaseUrl}/venues');
      final queryParams = <String, dynamic>{};
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      final response = await dio.get('/venues', queryParameters: queryParams);
      print('DEBUG: API response status: ${response.statusCode}');
      print('DEBUG: API response data: ${response.data}');

      if (response.statusCode == 200) {
        final data = response.data;
        final venuesData = data['data'] as List<dynamic>;
        print('DEBUG: Found ${venuesData.length} venues');
        
        final venues = venuesData
            .map((venueJson) => VenueModel.fromJson(venueJson as Map<String, dynamic>))
            .toList();
        
        print('DEBUG: Successfully parsed ${venues.length} venues');
        return venues;
      } else {
        throw Exception('Failed to load venues');
      }
    } on DioException catch (e) {
      print('DEBUG: DioException: ${e.message}');
      print('DEBUG: Response: ${e.response?.data}');
      throw Exception(e.response?.data['message'] ?? 'Network error occurred');
    } catch (e) {
      print('DEBUG: General Exception: $e');
      throw Exception('An unexpected error occurred: $e');
    }
  }
  // Booking methods
  Future<Map<String, dynamic>> createBooking(BookingModel booking) async {
    try {
      final response = await dio.post('/bookings', data: booking.toCreateRequest());

      if (response.statusCode == 201) {
        final data = response.data;
        final bookingData = data['data'] as Map<String, dynamic>;
        final createdBooking = BookingModel.fromJson(bookingData);
        
        return {
          'success': true,
          'booking': createdBooking,
          'message': 'Booking created successfully',
        };
      } else {
        return {
          'success': false,
          'message': 'Booking creation failed',
        };
      }
    } on DioException catch (e) {
      String errorMessage = 'Network error occurred';
      
      if (e.response?.statusCode == 409) {
        errorMessage = 'Time slot is already booked';
      } else if (e.response?.statusCode == 422) {
        errorMessage = e.response?.data['message'] ?? 'Invalid booking data';
      } else if (e.response?.data != null && e.response!.data['message'] != null) {
        errorMessage = e.response!.data['message'];
      }
      
      return {
        'success': false,
        'message': errorMessage,
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'An unexpected error occurred',
      };
    }
  }

  // Helper method to check if user is authenticated
  Future<bool> isAuthenticated() async {
    final token = await getToken();
    return token != null;
  }

  // Helper method to get current user data
  Future<UserModel?> getCurrentUser() async {
    try {
      final userDataString = await _storage.read(key: Constants.userKey);
      if (userDataString != null) {
        // Note: In a real app, you might want to use proper JSON parsing
        // For now, this is a simplified approach
        return null; // Would need proper JSON parsing implementation
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}