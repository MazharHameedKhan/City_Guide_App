import 'package:flutter/material.dart';
import 'screens/splash_screen.dart';
void main(){ runApp(MyApp()); }
class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext c) {
    return MaterialApp(
      title: 'City Guide',
      theme: ThemeData(primarySwatch: Colors.blue),
      home: SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}
