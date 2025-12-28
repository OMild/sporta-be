import 'package:flutter_test/flutter_test.dart';
import 'package:sporta_mobile_app/models/user_model.dart';

void main() {
  group('UserModel', () {
    test('should create UserModel from JSON correctly', () {
      // Arrange
      final json = {
        'id': 1,
        'name': 'John Doe',
        'email': 'john@example.com',
        'phone': '+1234567890',
        'avatar': 'avatar.jpg',
        'role': 'player',
        'created_at': '2023-01-01T00:00:00.000000Z',
        'updated_at': '2023-01-01T00:00:00.000000Z',
      };

      // Act
      final user = UserModel.fromJson(json);

      // Assert
      expect(user.id, 1);
      expect(user.name, 'John Doe');
      expect(user.email, 'john@example.com');
      expect(user.phone, '+1234567890');
      expect(user.avatar, 'avatar.jpg');
      expect(user.role, 'player');
      expect(user.createdAt, DateTime.parse('2023-01-01T00:00:00.000000Z'));
      expect(user.updatedAt, DateTime.parse('2023-01-01T00:00:00.000000Z'));
    });

    test('should create UserModel from JSON with null optional fields', () {
      // Arrange
      final json = {
        'id': 2,
        'name': 'Jane Doe',
        'email': 'jane@example.com',
        'phone': null,
        'avatar': null,
        'role': 'venue_owner',
        'created_at': null,
        'updated_at': null,
      };

      // Act
      final user = UserModel.fromJson(json);

      // Assert
      expect(user.id, 2);
      expect(user.name, 'Jane Doe');
      expect(user.email, 'jane@example.com');
      expect(user.phone, null);
      expect(user.avatar, null);
      expect(user.role, 'venue_owner');
      expect(user.createdAt, null);
      expect(user.updatedAt, null);
    });

    test('should convert UserModel to JSON correctly', () {
      // Arrange
      final user = UserModel(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        phone: '+1234567890',
        avatar: 'avatar.jpg',
        role: 'player',
        createdAt: DateTime.parse('2023-01-01T00:00:00.000000Z'),
        updatedAt: DateTime.parse('2023-01-01T00:00:00.000000Z'),
      );

      // Act
      final json = user.toJson();

      // Assert
      expect(json['id'], 1);
      expect(json['name'], 'John Doe');
      expect(json['email'], 'john@example.com');
      expect(json['phone'], '+1234567890');
      expect(json['avatar'], 'avatar.jpg');
      expect(json['role'], 'player');
      expect(json['created_at'], '2023-01-01T00:00:00.000Z');
      expect(json['updated_at'], '2023-01-01T00:00:00.000Z');
    });

    test('should handle role checking methods correctly', () {
      // Arrange & Act
      final player = UserModel(id: 1, name: 'Player', email: 'player@test.com', role: 'player');
      final venueOwner = UserModel(id: 2, name: 'Owner', email: 'owner@test.com', role: 'venue_owner');
      final superAdmin = UserModel(id: 3, name: 'Admin', email: 'admin@test.com', role: 'super_admin');

      // Assert
      expect(player.isPlayer, true);
      expect(player.isVenueOwner, false);
      expect(player.isSuperAdmin, false);

      expect(venueOwner.isPlayer, false);
      expect(venueOwner.isVenueOwner, true);
      expect(venueOwner.isSuperAdmin, false);

      expect(superAdmin.isPlayer, false);
      expect(superAdmin.isVenueOwner, false);
      expect(superAdmin.isSuperAdmin, true);
    });

    test('should implement equality correctly', () {
      // Arrange
      final user1 = UserModel(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        role: 'player',
      );
      final user2 = UserModel(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        role: 'player',
      );
      final user3 = UserModel(
        id: 2,
        name: 'Jane Doe',
        email: 'jane@example.com',
        role: 'player',
      );

      // Assert
      expect(user1, equals(user2));
      expect(user1, isNot(equals(user3)));
      expect(user1.hashCode, equals(user2.hashCode));
    });

    test('should handle toString correctly', () {
      // Arrange
      final user = UserModel(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        role: 'player',
      );

      // Act
      final result = user.toString();

      // Assert
      expect(result, 'UserModel(id: 1, name: John Doe, email: john@example.com, role: player)');
    });

    test('should throw FormatException for invalid JSON structure', () {
      // Arrange
      final invalidJson = {
        'invalid_field': 'value',
      };

      // Act & Assert
      expect(() => UserModel.fromJson(invalidJson), throwsA(isA<TypeError>()));
    });
  });
}