class City {
  int id; String name, description, image;
  City({required this.id, required this.name, this.description='', this.image=''});
  factory City.fromJson(Map<String,dynamic> j) => City(id:int.parse(j['id'].toString()), name:j['name'] ?? '', description:j['description'] ?? '', image:j['image'] ?? '');
}
