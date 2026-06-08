class UserModel {
  final int id;
  final String? username;
  final String fullName;
  final String email;
  final String role;
  final String? phone;
  final String? address;
  final String? photoUrl;

  UserModel({
    required this.id,
    this.username,
    required this.fullName,
    required this.email,
    required this.role,
    this.phone,
    this.address,
    this.photoUrl,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      username: json['username'] as String?,
      fullName: (json['full_name'] ?? json['username'] ?? '-') as String,
      email: (json['email'] ?? '-') as String,
      role: (json['role'] ?? 'guest') as String,
      phone: json['phone'] as String?,
      address: json['address'] as String?,
      photoUrl: (json['photo_url'] ?? json['photo']) as String?,
    );
  }
}
