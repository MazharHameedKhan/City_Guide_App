// lib/screens/auth/register_screen.dart

import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'login_screen.dart';

class RegisterScreen extends StatefulWidget {
  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _pass = TextEditingController();
  bool loading = false;
  String msg = '';

  void doRegister() async {
    setState(() => loading = true);
    final res = await ApiService.post('register.php', {'name': _name.text, 'email': _email.text, 'password': _pass.text});
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
      return;
    }

    setState(() => msg = (res != null ? (res['message'] ?? 'Error') : 'Network error'));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Register')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(children:[
          TextField(controller: _name, decoration: const InputDecoration(labelText: 'Name')),
          TextField(controller: _email, decoration: const InputDecoration(labelText: 'Email')),
          TextField(controller: _pass, decoration: const InputDecoration(labelText: 'Password'), obscureText: true),
          const SizedBox(height: 12),
          ElevatedButton(onPressed: loading ? null : doRegister, child: loading ? const CircularProgressIndicator(color: Colors.white) : const Text('Register')),
          if (msg.isNotEmpty) Padding(padding: const EdgeInsets.only(top:8), child: Text(msg, style: const TextStyle(color: Colors.red))),
        ]),
      ),
    );
  }
}
