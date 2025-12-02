import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'login_screen.dart';

class ResetPasswordScreen extends StatefulWidget {
  final String token;
  const ResetPasswordScreen({Key? key, required this.token}) : super(key: key);

  @override
  State<ResetPasswordScreen> createState() => _ResetPasswordScreenState();
}

class _ResetPasswordScreenState extends State<ResetPasswordScreen> {
  final _pass = TextEditingController();
  final _pass2 = TextEditingController();
  String msg = '';
  bool loading = false;

  Future<void> reset() async {
    if (_pass.text != _pass2.text) {
      setState(() => msg = 'Passwords do not match');
      return;
    }

    setState(() => loading = true);
    final res = await ApiService.post('reset_password.php', {
      'token': widget.token,
      'new_password': _pass.text,
    });
    setState(() => loading = false);

    if (res != null && res['status'] == true) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password reset successful! Please log in.'),
        ),
      );
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
        (route) => false,
      );
    } else {
      setState(() => msg = res?['message'] ?? 'Error resetting password.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Reset Password')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            const SizedBox(height: 30),
            Center(
              child: Image.asset(
                'assets/splash.png',
                width: 200, // adjust as you like
                height: 200,
                fit: BoxFit.contain,
              ),
            ),
            const SizedBox(height: 20),
            Text(
              'Token: ${widget.token}',
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _pass,
              decoration: const InputDecoration(
                labelText: 'New password',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _pass2,
              decoration: const InputDecoration(
                labelText: 'Confirm password',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: loading ? null : reset,
              child: loading
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: Colors.white,
                      ),
                    )
                  : const Text('Reset Password'),
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
