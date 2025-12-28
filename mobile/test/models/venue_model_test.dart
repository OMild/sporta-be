import 'package:flutter_test/flutter_test.dart';
import 'package:sporta_mobile_app/models/venue_model.dart';

void main() {
  group('VenueOwner', () {
    test('should create VenueOwner from JSON correctly', () {
      // Arrange
      final json = {
        'id': 1,
        'name': 'John Owner',
        'email': 'owner@example.com',
        'phone': '+1234567890',
      };

      // Act
      final owner = VenueOwner.fromJson(json);

      // Assert
      expect(owner.id, 1);
      expect(owner.name, 'John Owner');
      expect(owner.email, 'owner@example.com');
      expect(owner.phone, '+1234567890');
    });

    test('should create VenueOwner from JSON with null phone', () {
      // Arrange
      final json = {
        'id': 1,
        'name': 'John Owner',
        'email': 'owner@example.com',
        'phone': null,
      };

      // Act
      final owner = VenueOwner.fromJson(json);

      // Assert
      expect(owner.id, 1);
      expect(owner.name, 'John Owner');
      expect(owner.email, 'owner@example.com');
      expect(owner.phone, null);
    });

    test('should convert VenueOwner to JSON correctly', () {
      // Arrange
      final owner = VenueOwner(
        id: 1,
        name: 'John Owner',
        email: 'owner@example.com',
        phone: '+1234567890',
      );

      // Act
      final json = owner.toJson();

      // Assert
      expect(json['id'], 1);
      expect(json['name'], 'John Owner');
      expect(json['email'], 'owner@example.com');
      expect(json['phone'], '+1234567890');
    });

    test('should implement equality correctly', () {
      // Arrange
      final owner1 = VenueOwner(id: 1, name: 'John', email: 'john@test.com');
      final owner2 = VenueOwner(id: 1, name: 'John', email: 'john@test.com');
      final owner3 = VenueOwner(id: 2, name: 'Jane', email: 'jane@test.com');

      // Assert
      expect(owner1, equals(owner2));
      expect(owner1, isNot(equals(owner3)));
      expect(owner1.hashCode, equals(owner2.hashCode));
    });
  });

  group('VenueModel', () {
    test('should create VenueModel from JSON correctly', () {
      // Arrange
      final json = {
        'id': 1,
        'name': 'Sports Center',
        'address': '123 Main St',
        'description': 'Great sports facility',
        'facilities': 'Futsal, Badminton',
        'open_hour': '08:00',
        'close_hour': '22:00',
        'price_per_hour': 50.0,
        'status': 'active',
        'owner': {
          'id': 1,
          'name': 'John Owner',
          'email': 'owner@example.com',
          'phone': '+1234567890',
        },
        'created_at': '2023-01-01T00:00:00.000000Z',
        'updated_at': '2023-01-01T00:00:00.000000Z',
      };

      // Act
      final venue = VenueModel.fromJson(json);

      // Assert
      expect(venue.id, 1);
      expect(venue.name, 'Sports Center');
      expect(venue.address, '123 Main St');
      expect(venue.description, 'Great sports facility');
      expect(venue.facilities, 'Futsal, Badminton');
      expect(venue.openHour, '08:00');
      expect(venue.closeHour, '22:00');
      expect(venue.pricePerHour, 50.0);
      expect(venue.status, 'active');
      expect(venue.owner.id, 1);
      expect(venue.owner.name, 'John Owner');
      expect(venue.createdAt, DateTime.parse('2023-01-01T00:00:00.000000Z'));
      expect(venue.updatedAt, DateTime.parse('2023-01-01T00:00:00.000000Z'));
    });

    test('should create VenueModel from JSON with null optional fields', () {
      // Arrange
      final json = {
        'id': 1,
        'name': 'Sports Center',
        'address': '123 Main St',
        'description': null,
        'facilities': null,
        'open_hour': null,
        'close_hour': null,
        'price_per_hour': 50,
        'status': 'active',
        'owner': {
          'id': 1,
          'name': 'John Owner',
          'email': 'owner@example.com',
          'phone': null,
        },
        'created_at': null,
        'updated_at': null,
      };

      // Act
      final venue = VenueModel.fromJson(json);

      // Assert
      expect(venue.id, 1);
      expect(venue.name, 'Sports Center');
      expect(venue.address, '123 Main St');
      expect(venue.description, null);
      expect(venue.facilities, null);
      expect(venue.openHour, null);
      expect(venue.closeHour, null);
      expect(venue.pricePerHour, 50.0);
      expect(venue.status, 'active');
      expect(venue.createdAt, null);
      expect(venue.updatedAt, null);
    });

    test('should convert VenueModel to JSON correctly', () {
      // Arrange
      final owner = VenueOwner(
        id: 1,
        name: 'John Owner',
        email: 'owner@example.com',
        phone: '+1234567890',
      );
      final venue = VenueModel(
        id: 1,
        name: 'Sports Center',
        address: '123 Main St',
        description: 'Great sports facility',
        facilities: 'Futsal, Badminton',
        openHour: '08:00',
        closeHour: '22:00',
        pricePerHour: 50.0,
        status: 'active',
        owner: owner,
        createdAt: DateTime.parse('2023-01-01T00:00:00.000000Z'),
        updatedAt: DateTime.parse('2023-01-01T00:00:00.000000Z'),
      );

      // Act
      final json = venue.toJson();

      // Assert
      expect(json['id'], 1);
      expect(json['name'], 'Sports Center');
      expect(json['address'], '123 Main St');
      expect(json['description'], 'Great sports facility');
      expect(json['facilities'], 'Futsal, Badminton');
      expect(json['open_hour'], '08:00');
      expect(json['close_hour'], '22:00');
      expect(json['price_per_hour'], 50.0);
      expect(json['status'], 'active');
      expect(json['owner']['id'], 1);
      expect(json['created_at'], '2023-01-01T00:00:00.000Z');
      expect(json['updated_at'], '2023-01-01T00:00:00.000Z');
    });

    test('should handle numeric price conversion correctly', () {
      // Arrange - test with int price
      final jsonWithInt = {
        'id': 1,
        'name': 'Sports Center',
        'address': '123 Main St',
        'price_per_hour': 50,
        'status': 'active',
        'owner': {
          'id': 1,
          'name': 'John Owner',
          'email': 'owner@example.com',
        },
      };

      // Act
      final venue = VenueModel.fromJson(jsonWithInt);

      // Assert
      expect(venue.pricePerHour, 50.0);
      expect(venue.pricePerHour, isA<double>());
    });

    test('should implement equality correctly', () {
      // Arrange
      final owner = VenueOwner(id: 1, name: 'John', email: 'john@test.com');
      final venue1 = VenueModel(
        id: 1,
        name: 'Sports Center',
        address: '123 Main St',
        pricePerHour: 50.0,
        status: 'active',
        owner: owner,
      );
      final venue2 = VenueModel(
        id: 1,
        name: 'Sports Center',
        address: '123 Main St',
        pricePerHour: 50.0,
        status: 'active',
        owner: owner,
      );
      final venue3 = VenueModel(
        id: 2,
        name: 'Different Center',
        address: '456 Other St',
        pricePerHour: 75.0,
        status: 'active',
        owner: owner,
      );

      // Assert
      expect(venue1, equals(venue2));
      expect(venue1, isNot(equals(venue3)));
      expect(venue1.hashCode, equals(venue2.hashCode));
    });

    test('should handle toString correctly', () {
      // Arrange
      final owner = VenueOwner(id: 1, name: 'John', email: 'john@test.com');
      final venue = VenueModel(
        id: 1,
        name: 'Sports Center',
        address: '123 Main St',
        pricePerHour: 50.0,
        status: 'active',
        owner: owner,
      );

      // Act
      final result = venue.toString();

      // Assert
      expect(result, 'VenueModel(id: 1, name: Sports Center, address: 123 Main St, pricePerHour: 50.0, status: active)');
    });

    test('should throw TypeError for invalid JSON structure', () {
      // Arrange
      final invalidJson = {
        'invalid_field': 'value',
      };

      // Act & Assert
      expect(() => VenueModel.fromJson(invalidJson), throwsA(isA<TypeError>()));
    });

    test('should throw TypeError for missing owner in JSON', () {
      // Arrange
      final jsonWithoutOwner = {
        'id': 1,
        'name': 'Sports Center',
        'address': '123 Main St',
        'price_per_hour': 50.0,
        'status': 'active',
        // Missing owner field
      };

      // Act & Assert
      expect(() => VenueModel.fromJson(jsonWithoutOwner), throwsA(isA<TypeError>()));
    });
  });
}