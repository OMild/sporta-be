import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mockito/mockito.dart';
import 'package:mockito/annotations.dart';

import 'package:sporta_mobile_app/screens/home_screen.dart';
import 'package:sporta_mobile_app/services/api_service.dart';
import 'package:sporta_mobile_app/models/venue_model.dart';

import 'home_screen_test.mocks.dart';

@GenerateMocks([ApiService])
void main() {
  group('HomeScreen Widget Tests', () {
    late MockApiService mockApiService;

    setUp(() {
      mockApiService = MockApiService();
      // Setup default behavior for initialize method
      when(mockApiService.initialize()).thenReturn(null);
    });

    // Helper method to create test venues
    List<VenueModel> createTestVenues() {
      return [
        VenueModel(
          id: 1,
          name: 'Test Venue 1',
          address: '123 Test Street',
          description: 'A great venue for sports',
          facilities: 'Parking, Changing rooms',
          openHour: '08:00',
          closeHour: '22:00',
          pricePerHour: 50.0,
          status: 'active',
          owner: VenueOwner(
            id: 1,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '123-456-7890',
          ),
        ),
        VenueModel(
          id: 2,
          name: 'Test Venue 2',
          address: '456 Another Street',
          description: 'Another excellent venue',
          facilities: 'WiFi, Snack bar',
          openHour: '09:00',
          closeHour: '21:00',
          pricePerHour: 75.0,
          status: 'active',
          owner: VenueOwner(
            id: 2,
            name: 'Jane Smith',
            email: 'jane@example.com',
            phone: '987-654-3210',
          ),
        ),
      ];
    }

    Widget createTestWidget() {
      return MaterialApp(
        home: HomeScreen(apiService: mockApiService),
      );
    }

    testWidgets('displays app title correctly', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => createTestVenues());

      // Act
      await tester.pumpWidget(createTestWidget());

      // Assert
      expect(find.text('SPORTA'), findsOneWidget);
    });

    testWidgets('displays search field', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => createTestVenues());

      // Act
      await tester.pumpWidget(createTestWidget());

      // Assert
      expect(find.byType(TextField), findsOneWidget);
      expect(find.text('Search venues...'), findsOneWidget);
    });

    testWidgets('displays loading indicator initially', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => createTestVenues());

      // Act
      await tester.pumpWidget(createTestWidget());

      // Assert - should show loading indicator initially
      expect(find.byType(CircularProgressIndicator), findsOneWidget);
    });

    testWidgets('displays venue list when data loads successfully', (WidgetTester tester) async {
      // Arrange
      final testVenues = createTestVenues();
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => testVenues);

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle(); // Wait for async operations to complete

      // Assert
      expect(find.byType(ListView), findsOneWidget);
      expect(find.byType(VenueCard), findsNWidgets(testVenues.length));
      
      // Check if venue names are displayed
      for (final venue in testVenues) {
        expect(find.text(venue.name), findsOneWidget);
      }
    });

    testWidgets('displays venue card information correctly', (WidgetTester tester) async {
      // Arrange
      final testVenues = createTestVenues();
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => testVenues);

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle();

      // Assert - Check first venue card content
      final firstVenue = testVenues.first;
      expect(find.text(firstVenue.name), findsOneWidget);
      expect(find.text(firstVenue.address), findsOneWidget);
      expect(find.text('\$${firstVenue.pricePerHour.toStringAsFixed(0)}/hour'), findsOneWidget);
      expect(find.text('${firstVenue.openHour} - ${firstVenue.closeHour}'), findsOneWidget);
      
      // Check for icons
      expect(find.byIcon(Icons.location_on), findsAtLeastNWidgets(1));
      expect(find.byIcon(Icons.access_time), findsAtLeastNWidgets(1));
    });

    testWidgets('displays error message when venue loading fails', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenThrow(Exception('Network error'));

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle();

      // Assert
      expect(find.text('Error loading venues'), findsOneWidget);
      expect(find.byIcon(Icons.error_outline), findsOneWidget);
      expect(find.text('Retry'), findsOneWidget);
    });

    testWidgets('displays empty state when no venues found', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => []);

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle();

      // Assert
      expect(find.text('No venues found'), findsOneWidget);
      expect(find.text('No venues are currently available'), findsOneWidget);
      expect(find.byIcon(Icons.sports_tennis), findsOneWidget);
    });

    testWidgets('venue card tap navigates to booking screen', (WidgetTester tester) async {
      // Arrange
      final testVenues = createTestVenues();
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => testVenues);

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle();

      // Tap on first venue card
      await tester.tap(find.byType(VenueCard).first);
      await tester.pumpAndSettle();

      // Assert - Should navigate to BookingScreen
      expect(find.text('Book Venue'), findsOneWidget);
      expect(find.text(testVenues.first.name), findsOneWidget);
    });

    testWidgets('displays appropriate empty state message for search', (WidgetTester tester) async {
      // Arrange
      when(mockApiService.getVenues(search: null))
          .thenAnswer((_) async => createTestVenues());
      when(mockApiService.getVenues(search: 'nonexistent'))
          .thenAnswer((_) async => []);

      // Act
      await tester.pumpWidget(createTestWidget());
      await tester.pumpAndSettle();

      // Enter search text that returns no results
      await tester.enterText(find.byType(TextField), 'nonexistent');
      await tester.pumpAndSettle();

      // Assert
      expect(find.text('No venues found'), findsOneWidget);
      expect(find.text('Try adjusting your search terms'), findsOneWidget);
    });
  });
}