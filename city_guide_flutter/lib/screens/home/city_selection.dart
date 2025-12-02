import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/city.dart';
import 'home/home_screen.dart';
import '../models/user.dart';

class CitySelectionScreen extends StatefulWidget {
  final User user;
  const CitySelectionScreen({Key? key, required this.user}) : super(key: key);

  @override
  State<CitySelectionScreen> createState() => _CitySelectionScreenState();
}

class _CitySelectionScreenState extends State<CitySelectionScreen> {
  List<City> cities = [];
  bool loading = true;
  String error = '';

  @override
  void initState() {
    super.initState();
    loadCities();
  }

  Future<void> loadCities() async {
    setState(() {
      loading = true;
      error = '';
    });

    final res = await ApiService.get('get_cities.php');
    if (res != null && res['status'] == true) {
      final list = res['cities'] as List? ?? [];
      setState(() {
        cities = list.map((e) => City.fromJson(e)).toList();
        loading = false;
      });
    } else {
      setState(() {
        error = res != null
            ? (res['message'] ?? 'Failed to load cities')
            : 'Network error';
        loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Select Your City'),
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : error.isNotEmpty
              ? Center(
                  child: Text(
                    error,
                    style: const TextStyle(color: Colors.red),
                  ),
                )
              : ListView.builder(
                  itemCount: cities.length,
                  itemBuilder: (context, index) {
                    final city = cities[index];
                    return Card(
                      margin:
                          const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      elevation: 2,
                      child: ListTile(
                        leading: const Icon(Icons.location_city,
                            color: Colors.blueAccent),
                        title: Text(city.name),
                        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                        onTap: () {
                          Navigator.pushReplacement(
                            context,
                            MaterialPageRoute(
                              builder: (_) =>
                                  HomeScreen(user: widget.user),
                            ),
                          );
                        },
                      ),
                    );
                  },
                ),
    );
  }
}
