import 'dart:io';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:http/http.dart' as http;
import 'package:path/path.dart' as path;

import '../../models/user.dart';
import '../../services/api_service.dart';
import '../../config.dart';

class EditProfileScreen extends StatefulWidget {
  final User user;
  const EditProfileScreen({Key? key, required this.user}) : super(key: key);

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final TextEditingController _name = TextEditingController();
  final TextEditingController _bio = TextEditingController();
  String selectedCity = '';
  File? _image;
  bool loading = false;

  final ImagePicker _picker = ImagePicker();

  // local copy of user to reflect updates in UI
  late User localUser;

  @override
  void initState() {
    super.initState();
    localUser = User(
      id: widget.user.id,
      name: widget.user.name,
      email: widget.user.email,
      city: widget.user.city,
      profilePic: widget.user.profilePic,
      bio: widget.user.bio,
    );
    _name.text = localUser.name;
    _bio.text = localUser.bio;
    selectedCity = localUser.city;
  }

  @override
  void dispose() {
    _name.dispose();
    _bio.dispose();
    super.dispose();
  }

  Future<void> pickImage() async {
    final XFile? picked = await _picker.pickImage(source: ImageSource.gallery, maxWidth: 1200);
    if (picked != null) {
      setState(() {
        _image = File(picked.path);
      });
    }
  }

  Future<Map<String, dynamic>?> uploadProfilePic(int userId) async {
    if (_image == null) return null;
    try {
      final uri = Uri.parse(API_BASE + '/upload_profile.php');
      final request = http.MultipartRequest('POST', uri);
      request.fields['user_id'] = userId.toString();
      final fileName = path.basename(_image!.path);
      request.files.add(await http.MultipartFile.fromPath('profile', _image!.path, filename: fileName));

      final streamedResponse = await request.send();
      final respStr = await streamedResponse.stream.bytesToString();
      final decoded = json.decode(respStr);
      return decoded as Map<String, dynamic>?;
    } catch (e) {
      debugPrint('uploadProfilePic error: $e');
      return null;
    }
  }

  void save() async {
    final userId = localUser.id;
    if (userId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('User ID missing')));
      return;
    }

    setState(() => loading = true);

    try {
      // 1) update profile fields
      final res = await ApiService.post('profile_update.php', {
        'user_id': userId.toString(),
        'name': _name.text.trim(),
        'city': selectedCity.trim(),
        'bio': _bio.text.trim(),
      });

      if (res == null || res['status'] != true) {
        final message = (res != null) ? (res['message'] ?? 'Update failed') : 'No response from server';
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
        setState(() => loading = false);
        return;
      }

      // 2) upload image if selected
      if (_image != null) {
        final uploadRes = await uploadProfilePic(userId);
        if (uploadRes == null || uploadRes['status'] != true) {
          final msg = (uploadRes != null) ? (uploadRes['message'] ?? 'Upload failed') : 'Upload error';
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
          // continue even if upload failed
        } else {
          final newUrl = uploadRes['profile_pic'] ?? uploadRes['image'];
          if (newUrl != null) {
            setState(() {
              localUser = User(
                id: localUser.id,
                name: _name.text.trim(),
                email: localUser.email,
                city: selectedCity.trim(),
                profilePic: newUrl,
                bio: _bio.text.trim(),
              );
            });
          }
        }
      } else {
        // update localUser fields if only text changed
        setState(() {
          localUser = User(
            id: localUser.id,
            name: _name.text.trim(),
            email: localUser.email,
            city: selectedCity.trim(),
            profilePic: localUser.profilePic,
            bio: _bio.text.trim(),
          );
        });
      }

      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profile updated')));
      Navigator.pop(context, localUser); // return updated user if caller wants it
    } catch (e) {
      debugPrint('save error: $e');
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('An error occurred')));
    } finally {
      setState(() => loading = false);
    }
  }

  Widget _profileImageWidget() {
    if (_image != null) {
      return ClipOval(child: Image.file(_image!, width: 120, height: 120, fit: BoxFit.cover));
    } else if (localUser.profilePic.isNotEmpty) {
      return ClipOval(child: Image.network(localUser.profilePic, width: 120, height: 120, fit: BoxFit.cover));
    } else {
      return const CircleAvatar(radius: 60, child: Icon(Icons.person, size: 60));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Edit Profile'),
        actions: [
          TextButton(
            onPressed: loading ? null : save,
            child: loading ? const SizedBox(width:20, height:20, child: CircularProgressIndicator(color: Colors.white, strokeWidth:2)) : const Text('Save', style: TextStyle(color: Colors.white)),
          )
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            GestureDetector(
              onTap: pickImage,
              child: _profileImageWidget(),
            ),
            const SizedBox(height: 12),
            TextButton.icon(
              onPressed: pickImage,
              icon: const Icon(Icons.photo_library),
              label: const Text('Choose Photo'),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _name,
              decoration: const InputDecoration(labelText: 'Name', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _bio,
              decoration: const InputDecoration(labelText: 'Bio', border: OutlineInputBorder()),
              maxLines: 3,
            ),
            const SizedBox(height: 12),
            // Simple city input. You can replace with a dropdown loaded from API.
            TextField(
              controller: TextEditingController(text: selectedCity),
              readOnly: true,
              onTap: () async {
                // For simplicity: show input dialog to change city
                final newCity = await showDialog<String>(
                  context: context,
                  builder: (ctx) {
                    final tCtrl = TextEditingController(text: selectedCity);
                    return AlertDialog(
                      title: const Text('Edit City'),
                      content: TextField(controller: tCtrl, decoration: const InputDecoration(labelText: 'City')),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
                        TextButton(onPressed: () => Navigator.pop(ctx, tCtrl.text.trim()), child: const Text('OK')),
                      ],
                    );
                  },
                );
                if (newCity != null) setState(() => selectedCity = newCity);
              },
              decoration: const InputDecoration(labelText: 'City (tap to change)', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: loading ? null : save,
              child: loading ? const CircularProgressIndicator(color: Colors.white) : const Text('Save Changes'),
            ),
          ],
        ),
      ),
    );
  }
}
