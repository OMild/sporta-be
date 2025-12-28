class Constants {
  // API Configuration
  // Use localhost for web development
  static const String baseUrl = 'http://127.0.0.1:8000';
  static const String apiBaseUrl = '$baseUrl/api';
  
  // API Endpoints
  static const String loginEndpoint = '$apiBaseUrl/login';
  static const String registerEndpoint = '$apiBaseUrl/register';
  static const String logoutEndpoint = '$apiBaseUrl/logout';
  static const String venuesEndpoint = '$apiBaseUrl/venues';
  static const String bookingsEndpoint = '$apiBaseUrl/bookings';
  
  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  
  // App Configuration
  static const String appName = 'SPORTA';
  static const String appVersion = '1.0.0';
  
  // Request Timeouts
  static const int connectTimeout = 30000; // 30 seconds
  static const int receiveTimeout = 30000; // 30 seconds
}