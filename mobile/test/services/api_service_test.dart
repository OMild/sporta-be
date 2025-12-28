import 'package:flutter_test/flutter_test.dart';
import 'package:sporta_mobile_app/models/models.dart';

void main() {
  group('ApiService Tests', () {
    group('Booking Model', () {
      test('should create booking request data correctly', () {
        final booking = BookingModel(
          userId: 1,
          venueId: 1,
          bookingDate: '2024-01-15',
          startTime: '10:00',
          endTime: '12:00',
          totalPrice: 100.0,
          status: 'pending',
        );

        final requestData = booking.toCreateRequest();

        expect(requestData['venue_id'], equals(1));
        expect(requestData['booking_date'], equals('2024-01-15'));
        expect(requestData['start_time'], equals('10:00'));
        expect(requestData['end_time'], equals('12:00'));
        expect(requestData.containsKey('user_id'), isFalse);
        expect(requestData.containsKey('total_price'), isFalse);
      });
    });

    group('Model Serialization', () {
      test('should serialize and deserialize VenueModel correctly', () {
        final owner = VenueOwner(
          id: 1,
          name: 'Owner Name',
          email: 'owner@example.com',
          phone: '1234567890',
        );

        final venue = VenueModel(
          id: 1,
          name: 'Test Venue',
          address: '123 Test St',
          description: 'A test venue',
          facilities: 'Football, Basketball',
          openHour: '08:00',
          closeHour: '22:00',
          pricePerHour: 50.0,
          status: 'active',
          owner: owner,
        );

        final json = venue.toJson();
        final deserializedVenue = VenueModel.fromJson(json);

        expect(deserializedVenue, equals(venue));
      });

      test('should serialize and deserialize UserModel correctly', () {
        final user = UserModel(
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
          phone: '1234567890',
          avatar: 'avatar.jpg',
          role: 'player',
        );

        final json = user.toJson();
        final deserializedUser = UserModel.fromJson(json);

        expect(deserializedUser, equals(user));
      });

      test('should serialize and deserialize BookingModel correctly', () {
        final booking = BookingModel(
          id: 1,
          userId: 1,
          venueId: 1,
          bookingDate: '2024-01-15',
          startTime: '10:00',
          endTime: '12:00',
          totalPrice: 100.0,
          status: 'pending',
          paymentProof: 'proof.jpg',
        );

        final json = booking.toJson();
        final deserializedBooking = BookingModel.fromJson(json);

        expect(deserializedBooking, equals(booking));
      });
    });
    group('User Model Role Helpers', () {
      test('should correctly identify player role', () {
        final user = UserModel(
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
          role: 'player',
        );

        expect(user.isPlayer, isTrue);
        expect(user.isVenueOwner, isFalse);
        expect(user.isSuperAdmin, isFalse);
      });

      test('should correctly identify venue owner role', () {
        final user = UserModel(
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
          role: 'venue_owner',
        );

        expect(user.isPlayer, isFalse);
        expect(user.isVenueOwner, isTrue);
        expect(user.isSuperAdmin, isFalse);
      });

      test('should correctly identify super admin role', () {
        final user = UserModel(
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
          role: 'super_admin',
        );

        expect(user.isPlayer, isFalse);
        expect(user.isVenueOwner, isFalse);
        expect(user.isSuperAdmin, isTrue);
      });
    });

    group('VenueOwner Model', () {
      test('should serialize and deserialize correctly', () {
        final owner = VenueOwner(
          id: 1,
          name: 'Owner Name',
          email: 'owner@example.com',
          phone: '1234567890',
        );

        final json = owner.toJson();
        final deserializedOwner = VenueOwner.fromJson(json);

        expect(deserializedOwner, equals(owner));
      });

      test('should handle null phone correctly', () {
        final owner = VenueOwner(
          id: 1,
          name: 'Owner Name',
          email: 'owner@example.com',
        );

        final json = owner.toJson();
        final deserializedOwner = VenueOwner.fromJson(json);

        expect(deserializedOwner, equals(owner));
        expect(deserializedOwner.phone, isNull);
      });
    });
  });
}