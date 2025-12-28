class BookingModel {
  final int? id;
  final int userId;
  final int venueId;
  final String bookingDate;
  final String startTime;
  final String endTime;
  final double totalPrice;
  final String status;
  final String? paymentProof;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  BookingModel({
    this.id,
    required this.userId,
    required this.venueId,
    required this.bookingDate,
    required this.startTime,
    required this.endTime,
    required this.totalPrice,
    required this.status,
    this.paymentProof,
    this.createdAt,
    this.updatedAt,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    return BookingModel(
      id: json['id'] as int?,
      userId: json['user_id'] as int,
      venueId: json['venue_id'] as int,
      bookingDate: json['booking_date'] as String,
      startTime: json['start_time'] as String,
      endTime: json['end_time'] as String,
      totalPrice: (json['total_price'] as num).toDouble(),
      status: json['status'] as String,
      paymentProof: json['payment_proof'] as String?,
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at'] as String) 
          : null,
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at'] as String) 
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      if (id != null) 'id': id,
      'user_id': userId,
      'venue_id': venueId,
      'booking_date': bookingDate,
      'start_time': startTime,
      'end_time': endTime,
      'total_price': totalPrice,
      'status': status,
      if (paymentProof != null) 'payment_proof': paymentProof,
      if (createdAt != null) 'created_at': createdAt!.toIso8601String(),
      if (updatedAt != null) 'updated_at': updatedAt!.toIso8601String(),
    };
  }

  // Helper method to create booking request data
  Map<String, dynamic> toCreateRequest() {
    return {
      'venue_id': venueId,
      'booking_date': bookingDate,
      'start_time': startTime,
      'end_time': endTime,
    };
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is BookingModel &&
        other.id == id &&
        other.userId == userId &&
        other.venueId == venueId &&
        other.bookingDate == bookingDate &&
        other.startTime == startTime &&
        other.endTime == endTime &&
        other.totalPrice == totalPrice &&
        other.status == status &&
        other.paymentProof == paymentProof &&
        other.createdAt == createdAt &&
        other.updatedAt == updatedAt;
  }

  @override
  int get hashCode {
    return id.hashCode ^
        userId.hashCode ^
        venueId.hashCode ^
        bookingDate.hashCode ^
        startTime.hashCode ^
        endTime.hashCode ^
        totalPrice.hashCode ^
        status.hashCode ^
        paymentProof.hashCode ^
        createdAt.hashCode ^
        updatedAt.hashCode;
  }

  @override
  String toString() {
    return 'BookingModel(id: $id, venueId: $venueId, bookingDate: $bookingDate, startTime: $startTime, endTime: $endTime, totalPrice: $totalPrice, status: $status)';
  }
}