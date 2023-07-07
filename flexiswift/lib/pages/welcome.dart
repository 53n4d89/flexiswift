import 'package:flutter/material.dart';
import 'package:lottie/lottie.dart';

import '/pages/access_page.dart';

class Welcome extends StatefulWidget {
  @override
  _WelcomeState createState() => _WelcomeState();
}

class _WelcomeState extends State<Welcome> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Future.delayed(Duration(seconds: 2), () {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => AccessPage()),
        );
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: LayoutBuilder(
        builder: (BuildContext context, BoxConstraints constraints) {
          return Lottie.asset(
            'assets/splash screen.json',
            width: constraints.maxWidth,
            height: constraints.maxHeight,
            fit: BoxFit.fill,
          );
        },
      ),
    );
  }
}
