import 'dart:async';
import 'package:flutter/material.dart';
import 'auth/login_screen.dart';

class SplashScreen extends StatefulWidget {
  @override State<SplashScreen> createState() => _S();
}
class _S extends State<SplashScreen> {
  @override void initState(){
    super.initState();
    Timer(Duration(seconds:3), () {
      Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
    });
  }
  @override Widget build(BuildContext c) {
    return Scaffold(
      body: Center(child: Column(mainAxisSize:MainAxisSize.min, children:[
        Image.asset('assets/splash.png', width:400, height:400),
        SizedBox(height:20),
        Text('City Guide', style: TextStyle(fontSize:24, fontWeight: FontWeight.bold))
      ]))
    );
  }
}
