import 'package:flutter/material.dart';
import '../../models/user.dart';
import '../../models/city.dart';
import '../../models/location_item.dart';
import '../../services/api_service.dart';
import '../details/location_detail.dart';
import '../profile/profile_screen.dart';
import '../../widgets/location_card.dart';

class HomeScreen extends StatefulWidget {
  final User user;
  const HomeScreen({Key? key, required this.user}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<City> cities = [];
  City? selectedCity;
  List<LocationItem> locations = [];

  List<Map<String, dynamic>> categories = [];
  String? selectedCategoryId; // ✅ Changed from Map to String

  bool loading = false;
  String errorMsg = '';

  @override
  void initState() {
    super.initState();
    loadCities();
    loadCategories();
  }

  // 🏙️ Load all cities
  Future<void> loadCities() async {
    setState(() {
      loading = true;
      errorMsg = '';
    });

    final res = await ApiService.get('get_cities.php');
    setState(() {
      loading = false;
    });

    if (res != null && res['status'] == true) {
      final list = res['cities'] as List? ?? [];
      setState(() {
        cities = list.map((e) => City.fromJson(e as Map<String, dynamic>)).toList();
        if (cities.isNotEmpty) {
          selectedCity = cities.first;
          loadLocations();
        }
      });
    } else {
      setState(() {
        errorMsg = res != null
            ? (res['message'] ?? 'Failed to load cities')
            : 'Network error while loading cities';
      });
    }
  }

  // 📂 Load all categories
  Future<void> loadCategories() async {
    final res = await ApiService.get('get_categories.php');
    if (res != null && res['status'] == true) {
      final list = res['categories'] as List? ?? [];
      setState(() {
        categories = List<Map<String, dynamic>>.from(list);
      });
    }
  }

  // 📍 Load locations based on selected city & category
  Future<void> loadLocations() async {
    if (selectedCity == null) {
      setState(() => locations = []);
      return;
    }

    setState(() {
      loading = true;
      errorMsg = '';
    });

    final params = {
      'city_id': selectedCity!.id.toString(),
      if (selectedCategoryId != null) 'category_id': selectedCategoryId!,
    };

    final res = await ApiService.get('get_locations.php', params);
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      final list = res['locations'] as List? ?? [];
      setState(() => locations =
          list.map((e) => LocationItem.fromJson(e as Map<String, dynamic>)).toList());
    } else {
      setState(() {
        locations = [];
        errorMsg = res != null
            ? (res['message'] ?? 'Failed to load locations')
            : 'Network error while loading locations';
      });
    }
  }

  // 🔍 Search locations
  Future<void> doSearch(String text) async {
    if (text.trim().isEmpty) {
      loadLocations();
      return;
    }

    setState(() {
      loading = true;
      errorMsg = '';
    });

    final res = await ApiService.get('search.php', {
      'q': text.trim(),
      'city_id': selectedCity?.id.toString() ?? '',
      if (selectedCategoryId != null) 'category_id': selectedCategoryId!,
    });

    setState(() {
      loading = false;
    });

    if (res != null && res['status'] == true) {
      final list = res['results'] as List? ?? [];
      setState(() => locations =
          list.map((e) => LocationItem.fromJson(e as Map<String, dynamic>)).toList());
    } else {
      setState(() {
        locations = [];
        errorMsg = res != null
            ? (res['message'] ?? 'Search returned error')
            : 'Network error during search';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color.fromARGB(255, 139, 69, 224),
        elevation: 2,
        titleSpacing: 0,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Image.asset(
            'assets/splash.png',
            fit: BoxFit.contain,
            height: 40,
          ),
        ),
        title: const Text(
          'City Guide',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.person, color: Colors.white),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => ProfileScreen(user: widget.user),
                ),
              );
            },
          ),
        ],
      ),

      // 🏗️ Body Section
      body: Column(
        children: [
          // 🔍 Search + City selection
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: Column(children: [
              Row(children: [
                // 🔍 Search box
                Expanded(
                  child: TextField(
                    decoration: const InputDecoration(
                      hintText: 'Search restaurants, mosque, cafe...',
                      prefixIcon: Icon(Icons.search),
                      border: OutlineInputBorder(),
                    ),
                    onSubmitted: doSearch,
                  ),
                ),
                const SizedBox(width: 8),

                // 🏙️ City Dropdown
                Expanded(
                  child: DropdownButtonFormField<City>(
                    decoration: const InputDecoration(
                      border: OutlineInputBorder(),
                      labelText: 'Select City',
                    ),
                    value: selectedCity,
                    items: cities.map((c) {
                      return DropdownMenuItem(value: c, child: Text(c.name));
                    }).toList(),
                    onChanged: (v) {
                      setState(() => selectedCity = v);
                      loadLocations();
                    },
                  ),
                ),
              ]),

              const SizedBox(height: 8),

              // 🏷️ Category Dropdown (Fixed)
              DropdownButtonFormField<String>(
                decoration: const InputDecoration(
                  border: OutlineInputBorder(),
                  labelText: 'Filter by Category',
                ),
                value: selectedCategoryId,
                items: [
                  const DropdownMenuItem<String>(
                    value: null,
                    child: Text('All Categories'),
                  ),
                  ...categories.map((cat) {
                    return DropdownMenuItem<String>(
                      value: cat['id'].toString(),
                      child: Text(cat['name']),
                    );
                  }).toList(),
                ],
                onChanged: (v) {
                  setState(() => selectedCategoryId = v);
                  loadLocations();
                },
              ),
            ]),
          ),

          // ⚠️ Error Message
          if (errorMsg.isNotEmpty)
            Padding(
              padding: const EdgeInsets.all(8),
              child: Text(errorMsg, style: const TextStyle(color: Colors.red)),
            ),

          // 📍 Locations List
          Expanded(
            child: loading
                ? const Center(child: CircularProgressIndicator())
                : locations.isEmpty
                    ? const Center(child: Text('No places found'))
                    : ListView.builder(
                        itemCount: locations.length,
                        itemBuilder: (_, i) {
                          final it = locations[i];
                          return LocationCard(
                            item: it,
                            onTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => LocationDetail(
                                  location: it,
                                  user: widget.user,
                                ),
                              ),
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
