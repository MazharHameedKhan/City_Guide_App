import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'reset_password_screen.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({Key? key}) : super(key: key);

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _email = TextEditingController();
  String msg = '';
  bool loading = false;

  Future<void> requestReset() async {
    if (_email.text.isEmpty) {
      setState(() => msg = 'Please enter your email');
      return;
    }

    setState(() => loading = true);
    final res = await ApiService.post('request_reset.php', {
      'email': _email.text.trim(),
    });
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      // ✅ Safe access to token
      final token = res['token'] ?? '';
      if (!mounted) return;
      Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => ResetPasswordScreen(token: token)),
      );
    } else {
      setState(() => msg = res?['message'] ?? 'Network error. Try again.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Forgot Password')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            const Text(
              'Enter your email to receive a password reset token.',
              style: TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 20),
            TextField(
              controller: _email,
              decoration: const InputDecoration(
                labelText: 'Email',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.email),
              ),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: loading ? null : requestReset,
              child: loading
                  ? const SizedBox(
                      width: 20, height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('Send Reset Token'),
            ),
            const SizedBox(height: 10),
            if (msg.isNotEmpty)
              Text(msg, style: const TextStyle(color: Colors.red)),
          ],
        ),
      ),
    );
  }
}
