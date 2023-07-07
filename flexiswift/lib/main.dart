import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '/services/api_service.dart';
import '/pages/welcome.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        Provider<ApiService>(
          create: (_) => ApiService(),
        ),
      ],
      child: const FlexiSwift(),
    ),
  );
}

class FlexiSwift extends StatelessWidget {
  const FlexiSwift({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'FlexiSwift',
      home: Welcome(),
    );
  }
}
