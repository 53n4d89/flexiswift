import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../services/api_service.dart';
import '/pages/signin_page.dart';
import 'package:provider/provider.dart';

import '../constants.dart';
import '../controller/simple_ui_controller.dart';

class DashboardPage extends StatefulWidget {
  final String userRole; // Add this line

  // Update constructor to include `userRole`
  const DashboardPage({Key? key, required this.userRole}) : super(key: key);

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  ApiService apiService = ApiService();
  SimpleUIController simpleUIController = Get.put(SimpleUIController());
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  int _selectedIndex = 0;
  static const TextStyle optionStyle =
  TextStyle(fontSize: 30, fontWeight: FontWeight.bold);
  static const List<Widget> _widgetOptions = <Widget>[
    Text(
      'Index 0: Home',
      style: optionStyle,
    ),
    Text(
      'Index 1: Business',
      style: optionStyle,
    ),
    Text(
      'Index 2: School',
      style: optionStyle,
    ),
  ];

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    var size = MediaQuery.of(context).size;
    var theme = Theme.of(context);

    return GestureDetector(
      onTap: () => FocusManager.instance.primaryFocus?.unfocus(),
      child: Scaffold(
        key: _scaffoldKey,
        backgroundColor: Colors.white,
        resizeToAvoidBottomInset: false,
        body: SafeArea(
          child: Stack(
            children: [
              _buildMainBody(size, simpleUIController, theme),
              Positioned(
                top: 10,
                left: 10,
                child: IconButton(
                  icon: const Icon(
                    Icons.menu_rounded,
                    color: Color(0xFF4A9385),
                  ),
                    iconSize: 30.0,
                  onPressed: () => _scaffoldKey.currentState?.openDrawer(),
                ),
              ),
            ],
          ),
        ),
    drawer: Container(
      width: MediaQuery.of(context).size.width,
      child: Drawer(
          child: ListView(
            padding: EdgeInsets.zero,
            children: [
              Container(
                height: 200.0,
                child: DrawerHeader(
                  decoration: BoxDecoration(
                    color: Color(0xFF4A9385),
                  ),
                  margin: EdgeInsets.zero,
                  padding: EdgeInsets.zero,
                  child: Stack(
                    children: <Widget>[
                      Positioned(
                        top: 100.0, // adjust this as needed
                        left: 16.0,
                        child: Text(
                          "FlexiSwift",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 20.0,
                          ),
                        ),
                      ),
                      Positioned(
                        top: 10.0, // adjust this as needed
                        left: 5.0, // adjust this as needed
                        child: Image.asset(
                          'assets/sarajevoweblogo.png',
                          height: 80, // adjust this as needed
                          width: 80, // adjust this as needed
                        ),
                      ),
                      Positioned(
                        top: 10.0,
                        right: 10.0,
                        child: IconButton(
                          icon: Icon(Icons.arrow_back, color: Colors.white),
                          onPressed: () {
                            Navigator.of(context).pop();
                          },
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              ListTile(
                title: const Text('Home',
                  style: TextStyle(
                    color: Color(0xFF4A9385),
                    fontSize: 18,
                  ),
                ),
                selected: _selectedIndex == 0,
                onTap: () {
                  // Update the state of the app
                  _onItemTapped(0);
                  // Then close the drawer
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: const Text('Profile',
                  style: TextStyle(
                    color: Color(0xFF4A9385),
                    fontSize: 18,
                  ),
                ),
                selected: _selectedIndex == 1,
                onTap: () {
                  // Update the state of the app
                  _onItemTapped(1);
                  // Then close the drawer
                  Navigator.pop(context);
                },
              ),
              ExpansionTile(
                title: Text(
                  'Blog',
                  style: TextStyle(
                    color: _selectedIndex == 2 ? Color(0xFF1CC29C) : Color(0xFF4A9385),
                    fontSize: 18,
                  ),
                ),
                backgroundColor: _selectedIndex == 2 ? Color(0xFF4A9385) : Colors.white,
                children: <Widget>[
                  ListTile(
                    title: Padding(
                      padding: EdgeInsets.only(left: 20.0),
                      child: Text(
                        'All Posts',
                        style: TextStyle(
                          color: _selectedIndex == 3 ? Color(0xFF1CC29C) : Color(0xFF4A9385),
                          fontSize: 18,
                        ),
                      ),
                    ),
                    selected: _selectedIndex == 3,
                    onTap: () {
                      _onItemTapped(3);
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Padding(
                      padding: EdgeInsets.only(left: 20.0),
                      child: Text(
                        'Published Posts',
                        style: TextStyle(
                          color: _selectedIndex == 4 ? Color(0xFF1CC29C) : Color(0xFF4A9385),
                          fontSize: 18,
                        ),
                      ),
                    ),
                    selected: _selectedIndex == 4,
                    onTap: () {
                      _onItemTapped(4);
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Padding(
                      padding: EdgeInsets.only(left: 20.0),
                      child: Text(
                        'Create Posts',
                        style: TextStyle(
                          color: _selectedIndex == 5 ? Color(0xFF1CC29C) : Color(0xFF4A9385),
                          fontSize: 18,
                        ),
                      ),
                    ),
                    selected: _selectedIndex == 5,
                    onTap: () {
                      _onItemTapped(5);
                      Navigator.pop(context);
                    },
                  ),
                  ListTile(
                    title: Padding(
                      padding: EdgeInsets.only(left: 20.0),
                      child: Text(
                        'Edit Posts',
                        style: TextStyle(
                          color: _selectedIndex == 6 ? Color(0xFF1CC29C) : Color(0xFF4A9385),
                          fontSize: 18,
                        ),
                      ),
                    ),
                    selected: _selectedIndex == 6,
                    onTap: () {
                      _onItemTapped(6);
                      Navigator.pop(context);
                    },
                  ),
                ],
              ),
              ListTile(
                title: const Text(
                  'Sign Out',
                  style: TextStyle(
                    color: Color(0xFF4A9385),
                    fontSize: 18,
                  ),
                ),
                selected: _selectedIndex == 7,
                onTap: () async {
                  final apiService = Provider.of<ApiService>(context, listen: false);
                  try {
                    final response = await apiService.post(
                      'https://senad-cavkusic.sarajevoweb.com/api/signout/',
                      data: {},
                    );
                    if (response.statusCode == 200) {
                      Navigator.pushReplacement(
                        context,
                        MaterialPageRoute(builder: (context) => SigninView()),
                      );
                    } else {
                      final jsonResponse = response.data;
                      final message = jsonResponse['message'];
                      final errorMessage = message is List ? message.join(' ') : message;
                      final snackBar = SnackBar(content: Text(errorMessage.toString()));
                      ScaffoldMessenger.of(context).showSnackBar(snackBar);
                    }
                  } catch (e) {
                    final snackBar = SnackBar(content: Text('An error occurred. Please try again later.'));
                    ScaffoldMessenger.of(context).showSnackBar(snackBar);
                  }
                },
              ),
            ],
          ),
        ),
    ),
      ),
    );
  }

  Widget _buildMainBody(
      Size size, SimpleUIController simpleUIController, ThemeData theme) {
    return SafeArea(
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            SizedBox(height: size.height * 0.03),
            Padding(
              padding: const EdgeInsets.only(left: 20.0, top:40.0),
              child: Text(
                'Dashboard',
                style: kSigninTitleStyle(size),
              ),
            ),
            SizedBox(height: size.height * 0.03),
            Padding(
              padding: const EdgeInsets.only(left: 20.0),
              child: Text(
                'Your journey to innovation begins here. Welcome to FlexiSwift!',
                style: kSigninSubtitleStyle(size),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(left: 20.0, top: 10.0), // Modify padding as per your needs
              child: Text(
                'Hi: ${widget.userRole}', // Access the user's role here
                style: TextStyle(fontSize: 16), // Modify text style as per your needs
              ),
            ),
            SizedBox(height: size.height * 0.03),
            // Add your desired content here
          ],
        ),
      ),
    );
  }


}