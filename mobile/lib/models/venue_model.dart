class VenueOwner {
  final int id;
  final String name;
  final String email;
  final String? phone;

  VenueOwner({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
  });

  factory VenueOwner.fromJson(Map<String, dynamic> json) {
    return VenueOwner(
      id: json['id'] as int,
      name: json['name'] as String,
      email: json['email'] as String,
      phone: json['phone'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
    };
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is VenueOwner &&
        other.id == id &&
        other.name == name &&
        other.email == email &&
        other.phone == phone;
  }

  @override
  int get hashCode {
    return id.hashCode ^ name.hashCode ^ email.hashCode ^ phone.hashCode;
  }
}

class VenueModel {
  final int id;
  final String name;
  final String address;
  final String? description;
  final String? facilities;
  final String? openHour;
  final String? closeHour;
  final double pricePerHour;
  final String status;
  final VenueOwner owner;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  VenueModel({
    required this.id,
    required this.name,
    required this.address,
    this.description,
    this.facilities,
    this.openHour,
    this.closeHour,
    required this.pricePerHour,
    required this.status,
    required this.owner,
    this.createdAt,
    this.updatedAt,
  });

  factory VenueModel.fromJson(Map<String, dynamic> json) {
    return VenueModel(
      id: json['id'] as int,
      name: json['name'] as String,
      address: json['address'] as String,
      description: json['description'] as String?,
      facilities: json['facilities'] as String?,
      openHour: json['open_hour'] as String?,
      closeHour: json['close_hour'] as String?,
      pricePerHour: _parseDouble(json['price_per_hour']),
      status: json['status'] as String,
      owner: VenueOwner.fromJson(json['owner'] as Map<String, dynamic>),
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at'] as String) 
          : null,
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at'] as String) 
          : null,
    );
  }

  // Helper method to safely parse double from various types
  static double _parseDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) {
      return double.tryParse(value) ?? 0.0;
    }
    return 0.0;
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'address': address,
      'description': description,
      'facilities': facilities,
      'open_hour': openHour,
      'close_hour': closeHour,
      'price_per_hour': pricePerHour,
      'status': status,
      'owner': owner.toJson(),
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is VenueModel &&
        other.id == id &&
        other.name == name &&
        other.address == address &&
        other.description == description &&
        other.facilities == facilities &&
        other.openHour == openHour &&
        other.closeHour == closeHour &&
        other.pricePerHour == pricePerHour &&
        other.status == status &&
        other.owner == owner &&
        other.createdAt == createdAt &&
        other.updatedAt == updatedAt;
  }

  @override
  int get hashCode {
    return id.hashCode ^
        name.hashCode ^
        address.hashCode ^
        description.hashCode ^
        facilities.hashCode ^
        openHour.hashCode ^
        closeHour.hashCode ^
        pricePerHour.hashCode ^
        status.hashCode ^
        owner.hashCode ^
        createdAt.hashCode ^
        updatedAt.hashCode;
  }

  @override
  String toString() {
    return 'VenueModel(id: $id, name: $name, address: $address, pricePerHour: $pricePerHour, status: $status)';
  }
}