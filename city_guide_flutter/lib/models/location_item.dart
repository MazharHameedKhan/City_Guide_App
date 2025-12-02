class LocationItem {
  int id, cityId;
  String type,name,shortDesc,fullDesc,contact,openHours,mapLink,image;
  double rating;
  LocationItem({required this.id, required this.cityId, required this.type, required this.name, required this.shortDesc,
    required this.fullDesc, required this.contact, required this.openHours, required this.mapLink, required this.image, required this.rating});
  factory LocationItem.fromJson(Map<String,dynamic> j) {
    return LocationItem(
      id:int.parse(j['id'].toString()),
      cityId: int.parse(j['city_id'].toString()),
      type: j['type'] ?? '',
      name: j['name'] ?? '',
      shortDesc: j['short_desc'] ?? '',
      fullDesc: j['full_desc'] ?? '',
      contact: j['contact'] ?? '',
      openHours: j['open_hours'] ?? '',
      mapLink: j['map_link'] ?? '',
      image: j['image'] ?? '',
      rating: j['rating']!=null?double.parse(j['rating'].toString()):0.0
    );
  }
}
