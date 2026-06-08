class RoomTypeModel {
  final int id;
  final String name;
  final String? description;
  final num pricePerNight;
  final int maxOccupancy;
  final List<String> facilities;
  final String? imageUrl;

  RoomTypeModel({
    required this.id,
    required this.name,
    this.description,
    required this.pricePerNight,
    required this.maxOccupancy,
    required this.facilities,
    this.imageUrl,
  });

  factory RoomTypeModel.fromJson(Map<String, dynamic> json) {
    final rawFacilities = json['facilities'];
    return RoomTypeModel(
      id: json['id'] as int,
      name: (json['name'] ?? '-') as String,
      description: json['description'] as String?,
      pricePerNight: (json['price_per_night'] ?? 0) as num,
      maxOccupancy: (json['max_occupancy'] ?? 0) as int,
      facilities: rawFacilities is List
          ? rawFacilities.map((e) => e.toString()).toList()
          : <String>[],
      imageUrl: json['image_url'] as String?,
    );
  }
}
