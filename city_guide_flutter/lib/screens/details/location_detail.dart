import 'package:flutter/material.dart';
import '../../models/location_item.dart';
import '../../models/user.dart';
import '../../services/api_service.dart';
import 'package:url_launcher/url_launcher.dart';

class LocationDetail extends StatefulWidget {
  final LocationItem location;
  final User user;

  const LocationDetail({required this.location, required this.user, super.key});

  @override
  State<LocationDetail> createState() => _LocationDetailState();
}

class _LocationDetailState extends State<LocationDetail> {
  List reviews = [];
  final TextEditingController _comment = TextEditingController();
  int _rating = 5;
  bool loading = false;

  @override
  void initState() {
    super.initState();
    loadReviews();
  }

  Future<void> loadReviews() async {
    setState(() => loading = true);
    final res = await ApiService.get('get_reviews.php', {
      'location_id': widget.location.id.toString(),
    });
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      final list = res['reviews'] as List? ?? [];
      setState(() => reviews = list);
    } else {
      setState(() => reviews = []);
    }
  }

  Future<void> addReview() async {
    if (widget.user.id == null) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Login required')));
      return;
    }

    setState(() => loading = true);
    final res = await ApiService.post('add_review.php', {
      'user_id': widget.user.id.toString(),
      'location_id': widget.location.id.toString(),
      'rating': _rating.toString(),
      'comment': _comment.text,
    });
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      _comment.clear();
      loadReviews();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content:
              Text(res != null ? (res['message'] ?? 'Error') : 'Network error'),
        ),
      );
    }
  }

  void openMaps() async {
    final url = widget.location.mapLink;
    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    } else {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Invalid map link')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final l = widget.location;
    return Scaffold(
      appBar: AppBar(title: Text(l.name)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ✅ FIXED IMAGE LOADING
            if (l.image.isNotEmpty)
              Image.network(
                l.image.startsWith('http')
                    ? l.image
                    : 'http://10.0.2.2/cityguide_api/uploads/${l.image}',
                height: 200,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => const Column(
                  children: [
                    Icon(Icons.broken_image, size: 80, color: Colors.grey),
                    SizedBox(height: 8),
                    Text('Image not available',
                        style: TextStyle(color: Colors.grey)),
                  ],
                ),
              ),
            const SizedBox(height: 8),
            Text(
              l.name,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            Text('${l.type} • ${l.openHours}'),
            const SizedBox(height: 8),
            Text(l.fullDesc),
            const SizedBox(height: 8),
            ElevatedButton(
              onPressed: openMaps,
              child: const Text('Open in Google Maps'),
            ),
            const Divider(),
            const Text(
              'Reviews',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            if (loading) const Center(child: CircularProgressIndicator()),
            ...reviews.map(
              (r) => ListTile(
                title: Text(r['user_name'] ?? 'User'),
                subtitle: Text(r['comment'] ?? ''),
                trailing: Text((r['rating'] ?? '').toString()),
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _comment,
              decoration:
                  const InputDecoration(labelText: 'Write a review'),
            ),
            Row(
              children: [
                DropdownButton<int>(
                  value: _rating,
                  items: [1, 2, 3, 4, 5]
                      .map((e) =>
                          DropdownMenuItem(value: e, child: Text('$e')))
                      .toList(),
                  onChanged: (v) {
                    if (v != null) setState(() => _rating = v);
                  },
                ),
                const SizedBox(width: 8),
                ElevatedButton(
                  onPressed: addReview,
                  child: const Text('Post'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
