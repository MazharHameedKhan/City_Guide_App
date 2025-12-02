class User {
  int? id;
  String name,email,city,profilePic,bio;
  User({this.id, required this.name, required this.email, this.city='', this.profilePic='', this.bio=''});
  factory User.fromJson(Map<String,dynamic> j) {
    return User(
      id: j['id']!=null?int.parse(j['id'].toString()):null,
      name: j['name'] ?? '',
      email: j['email'] ?? '',
      city: j['city'] ?? '',
      profilePic: j['profile_pic'] ?? '',
      bio: j['bio'] ?? ''
    );
  }
}
