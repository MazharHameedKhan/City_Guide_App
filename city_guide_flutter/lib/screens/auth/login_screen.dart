import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'register_screen.dart';
import '../home/home_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../models/user.dart';
import 'dart:convert';
import 'forgot_password_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _email = TextEditingController();
  final _pass = TextEditingController();
  bool _loading = false;
  String _msg = '';
  bool _obscureText = true;

  @override
  void dispose() {
    _email.dispose();
    _pass.dispose();
    super.dispose();
  }

  void _doLogin() async {
    if (_email.text.isEmpty || _pass.text.isEmpty) {
      setState(() => _msg = 'Please enter both email and password');
      return;
    }

    setState(() => _loading = true);
    final res = await ApiService.post('login.php', {
      'email': _email.text.trim(),
      'password': _pass.text,
    });
    setState(() => _loading = false);

    if (res != null && res['status'] == true) {
      final userMap = res['user'];
      if (userMap is Map<String, dynamic>) {
        final user = User.fromJson(userMap);
        final sp = await SharedPreferences.getInstance();
        await sp.setString('user', jsonEncode(userMap));
        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (_) => HomeScreen(user: user)),
          );
        }
      } else {
        setState(() => _msg = 'Invalid user data received from server');
      }
    } else {
      setState(() => _msg = res?['message'] ?? 'Network error. Please try again.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final buttonShape = RoundedRectangleBorder(borderRadius: BorderRadius.circular(12));

    return Scaffold(
      appBar: AppBar(
        title: const Text('Login'),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 40),
              Image.asset(
                'assets/splash.png',
                width: MediaQuery.of(context).size.width * 0.3,
                height: MediaQuery.of(context).size.width * 0.3,
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) {
                  return const Icon(Icons.error, size: 100, color: Colors.red);
                },
              ),
              const SizedBox(height: 20),
              TextField(
                controller: _email,
                decoration: const InputDecoration(
                  labelText: 'Email',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.email),
                ),
                keyboardType: TextInputType.emailAddress,
                textInputAction: TextInputAction.next,
              ),
              const SizedBox(height: 12),
              TextField(
                controller: _pass,
                decoration: InputDecoration(
                  labelText: 'Password',
                  border: const OutlineInputBorder(),
                  prefixIcon: const Icon(Icons.lock),
                  suffixIcon: IconButton(
                    icon: Icon(_obscureText ? Icons.visibility : Icons.visibility_off),
                    onPressed: () {
                      setState(() => _obscureText = !_obscureText);
                    },
                  ),
                ),
                obscureText: _obscureText,
                textInputAction: TextInputAction.done,
                onSubmitted: (_) => _doLogin(),
              ),
              const SizedBox(height: 10),

              // Forgot Password Button
              Align(
                alignment: Alignment.centerRight,
                child: TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => ForgotPasswordScreen()),
                    );
                  },
                  child: const Text(
                    'Forgot Password?',
                    style: TextStyle(color: Colors.blue, fontSize: 14),
                  ),
                ),
              ),

              const SizedBox(height: 10),

              // ✅ Login Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _loading ? null : _doLogin,
                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        _loading ? Colors.grey : Theme.of(context).primaryColor,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: buttonShape,
                    textStyle: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  child: _loading
                      ? const SizedBox(
                          height: 18,
                          width: 18,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text(
                          'Login',
                          style: TextStyle(color: Colors.white),
                        ),
                ),
              ),

              const SizedBox(height: 12),

              // ✅ Register Button (matching shape & size)
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => RegisterScreen()),
                    );
                  },
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: buttonShape,
                    side: BorderSide(color: Theme.of(context).primaryColor, width: 2),
                    textStyle: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  child: Text(
                    'Register',
                    style: TextStyle(color: Theme.of(context).primaryColor),
                  ),
                ),
              ),

              const SizedBox(height: 12),

              if (_msg.isNotEmpty)
                Text(
                  _msg,
                  style: const TextStyle(color: Colors.red, fontSize: 14),
                  textAlign: TextAlign.center,
                ),
            ],
          ),
        ),
      ),
    );
  }
}
