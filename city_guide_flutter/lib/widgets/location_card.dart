import 'package:flutter/material.dart';
import '../models/location_item.dart';

class LocationCard extends StatelessWidget {
  final LocationItem item;
  final VoidCallback onTap;
  LocationCard({required this.item, required this.onTap});
  @override Widget build(BuildContext c) {
    return Card(
      child: ListTile(
        leading: item.image.isNotEmpty ? Image.network(item.image, width:60, height:60, fit:BoxFit.cover) : Icon(Icons.location_on),
        title: Text(item.name),
        subtitle: Text('${item.type} • ${item.shortDesc}'),
        trailing: Text(item.rating.toString()),
        onTap: onTap,
      ),
    );
  }
}
