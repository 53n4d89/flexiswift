import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'dart:core';
import 'package:dio/dio.dart';
import 'package:provider/provider.dart';
import '/services/api_service.dart';

import '../constants.dart';
import '../controller/simple_ui_controller.dart';
import 'signup_page.dart';
import 'dashboard.dart';

class SigninView extends StatefulWidget {
  const SigninView({Key? key}) : super(key: key);

  @override
  State<SigninView> createState() => _SigninViewState();
}

class _SigninViewState extends State<SigninView> {
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();

  Color emailPrefixColor = Colors.grey;
  Color passwordPrefixColor =
      Colors.grey; // Initialize with the appropriate initial color
  Color passwordSufixColor = Colors.grey;

  final _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  SimpleUIController simpleUIController = Get.put(SimpleUIController());

  @override
  Widget build(BuildContext context) {
    var size = MediaQuery.of(context).size;
    var theme = Theme.of(context);

    return GestureDetector(
      onTap: () => FocusManager.instance.primaryFocus?.unfocus(),
      child: Scaffold(
        backgroundColor: Colors.white,
        resizeToAvoidBottomInset: false,
        body: _buildMainBody(size, simpleUIController, theme),
      ),
    );
  }

  /// Main Body
  Widget _buildMainBody(
      Size size, SimpleUIController simpleUIController, ThemeData theme) {
    return SafeArea(
      child: SingleChildScrollView(
        //scrollDirection: Axis.vertical,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Align(
                alignment: Alignment.topLeft,
                child: ElevatedButton.icon(
                  onPressed: () => Navigator.of(context).pop(),
                  icon: const Icon(
                    Icons.arrow_left_sharp,
                    color: Color(0xFF4A9385),
                    size: 28,
                  ),
                  label: const Text(
                    'Back',
                    style: TextStyle(
                      color: Color(0xFF4A9385),
                      fontSize: 16,
                    ),
                  ),
                  style: ElevatedButton.styleFrom(
                    elevation: 0,
                    backgroundColor: Colors.transparent,
                  ),
                )),
            SizedBox(
              height: size.height * 0.03,
            ),
            Padding(
              padding: const EdgeInsets.only(left: 20.0),
              child: Text(
                'Signin',
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
            SizedBox(
              height: size.height * 0.03,
            ),
            Padding(
              padding: const EdgeInsets.only(left: 20.0, right: 20),
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    /// username or Gmail

                    Focus(
                      onFocusChange: (hasFocus) {
                        setState(() {
                          emailPrefixColor =
                              hasFocus ? const Color(0xFF4A9385) : Colors.grey;
                        });
                      },
                      child: TextFormField(
                        style: kTextFormFieldStyle(),
                        controller: emailController,
                        decoration: InputDecoration(
                          prefixIcon: Icon(
                            Icons.email_rounded,
                            color: emailPrefixColor,
                          ),
                          hintText: 'Enter your Email',
                          border: const OutlineInputBorder(
                            borderRadius: BorderRadius.all(Radius.circular(15)),
                          ),
                          focusedBorder: const OutlineInputBorder(
                            borderRadius: BorderRadius.all(Radius.circular(15)),
                            borderSide: BorderSide(
                                color: Color(0xFF4A9385), width: 2.0),
                          ),
                        ),
                        // The validator receives the text that the user has entered.
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Please enter your email';
                          } else if (!value.contains('@')) {
                            return 'Please enter valid email address';
                          }
                          return null;
                        },
                      ),
                    ),
                    SizedBox(
                      height: size.height * 0.02,
                    ),

                    /// password
                    Focus(
                      onFocusChange: (hasFocus) {
                        setState(() {
                          passwordPrefixColor =
                              hasFocus ? const Color(0xFF4A9385) : Colors.grey;
                          passwordPrefixColor =
                              hasFocus ? const Color(0xFF4A9385) : Colors.grey;
                        });
                      },
                      child: Obx(
                        () => TextFormField(
                          style: kTextFormFieldStyle(),
                          controller: passwordController,
                          obscureText: simpleUIController.isObscure.value,
                          decoration: InputDecoration(
                            prefixIcon: Icon(
                              Icons.lock_open,
                              color: passwordPrefixColor,
                            ),
                            suffixIcon: IconButton(
                              icon: Icon(
                                simpleUIController.isObscure.value
                                    ? Icons.visibility
                                    : Icons.visibility_off,
                                color: passwordPrefixColor,
                              ),
                              onPressed: () {
                                simpleUIController.isObscureActive();
                              },
                            ),
                            hintText: 'Enter Your Password',
                            border: OutlineInputBorder(
                              borderRadius:
                                  BorderRadius.all(Radius.circular(15)),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius:
                                  BorderRadius.all(Radius.circular(15)),
                              borderSide: BorderSide(
                                  color: Color(0xFF4A9385), width: 2.0),
                            ),
                            errorMaxLines: 3,
                          ),
                          // The validator receives the text that the user has entered.
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Please enter your password.';
                            } else if (value.length < 8) {
                              return 'Password should be a minimum of 8 characters.';
                            } else if (value.length > 15) {
                              return 'The password may contain up to 15 characters.';
                            }

                            bool hasUppercase =
                                value.contains(new RegExp(r'[A-Z]'));
                            bool hasLowercase =
                                value.contains(new RegExp(r'[a-z]'));
                            bool hasNumbers =
                                value.contains(new RegExp(r'[0-9]'));
                            bool hasSpecialCharacters = value.contains(
                                new RegExp(r'[!@#$%^&*(),.?":{}|<>]'));

                            List<String> errors = [];

                            if (!hasUppercase) {
                              errors.add("uppercase letters");
                            }
                            if (!hasLowercase) {
                              errors.add("lowercase letters");
                            }
                            if (!hasNumbers) {
                              errors.add("numbers");
                            }
                            if (!hasSpecialCharacters) {
                              errors.add("symbols");
                            }

                            if (errors.isNotEmpty) {
                              String errorsString = errors.join(', ');
                              return "Password should contain $errorsString.";
                            }

                            return null;
                          },
                        ),
                      ),
                    ),
                    SizedBox(
                      height: size.height * 0.01,
                    ),
                    Text(
                      'Creating an account means you\'re okay with our Terms of Services and our Privacy Policy',
                      style: kSigninTermsAndPrivacyStyle(size),
                      textAlign: TextAlign.center,
                    ),
                    SizedBox(
                      height: size.height * 0.02,
                    ),

                    /// Signin Button
                    signinButton(),
                    SizedBox(
                      height: size.height * 0.03,
                    ),

                    /// Navigate To SIgnin Screen
                    GestureDetector(
                      onTap: () {
                        Navigator.push(
                            context,
                            CupertinoPageRoute(
                                builder: (ctx) => const SignUpPage()));
                        emailController.clear();
                        passwordController.clear();
                        _formKey.currentState?.reset();
                        simpleUIController.isObscure.value = true;
                      },
                      child: RichText(
                        text: TextSpan(
                          text: 'Don\'t have an account?',
                          style: kHaveAnAccountStyle(size),
                          children: [
                            TextSpan(
                              text: " SignUp As",
                              style: kSigninOrSignUpTextStyle(
                                size,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    SizedBox(
                      height: size.height * 0.05,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Signin Button
  Widget signinButton() {
    return SizedBox(
      width: double.infinity,
      height: 60,
      child: ElevatedButton(
        style: ButtonStyle(
          backgroundColor: MaterialStateProperty.all(Color(0xFF4A9385)),
          shape: MaterialStateProperty.all(
            RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(15),
            ),
          ),
        ),
        onPressed: () async {
          if (_formKey.currentState!.validate()) {
            final email = emailController.text;
            final password = passwordController.text;
            await Future.delayed(Duration(seconds: 1));
            // Use the ApiService to make the POST request
            final apiService = Provider.of<ApiService>(context, listen: false);
            try {
              var response = await apiService.post(
                'https://senad-cavkusic.sarajevoweb.com/api/signin/',
                data: {'email': email, 'password': password},
              );
              if (response.statusCode == 200) {
                try {
                  final response = await apiService.get(
                    'https://senad-cavkusic.sarajevoweb.com/dashboard/data',
                  );
                  print('Response headers: ${response.headers}');
                  if (response.statusCode == 200) {
                    final jsonResponse = response.data as Map<String, dynamic>;
                    print(jsonResponse);
                    final userRole = jsonResponse['role'] as String;

                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (context) => DashboardPage(userRole: userRole),
                      ),
                    );
                  } else {
                    // Handle error response
                  }
                } catch (e) {
                  // Handle error
                }
              }
            } catch (e) {
              if (e is DioError) {
                var jsonResponse = e.response?.data;
                if (jsonResponse is Map<String, dynamic>) {
                  jsonResponse.forEach((key, value) {
                    final errorMessage = value is List ? value.join(' ') : value;
                    final snackBar =
                    SnackBar(content: Text(errorMessage.toString()));
                    ScaffoldMessenger.of(context).showSnackBar(snackBar);
                  });
                }
              }
            }
          }
        },
        child: const Text(
          'Signin',
          style: TextStyle(
            fontSize: 18,
          ),
        ),
      ),
    );
  }
}
