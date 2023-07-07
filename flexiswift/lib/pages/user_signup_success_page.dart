import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../constants.dart';
import '../controller/simple_ui_controller.dart';

class UserSignUpSuccessPage extends StatefulWidget {
  const UserSignUpSuccessPage({Key? key}) : super(key: key);

  @override
  State<UserSignUpSuccessPage> createState() => _UserSignUpSuccessPageState();
}

class _UserSignUpSuccessPageState extends State<UserSignUpSuccessPage> {
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
              ),
            ),
            SizedBox(height: size.height * 0.03),
            Padding(
              padding: const EdgeInsets.only(left: 20.0),
              child: Text(
                'Your journey to innovation begins here. Welcome to FlexiSwift!',
                style: kSigninTitleStyle(size),
              ),
            ),
            SizedBox(height: size.height * 0.03),
            Padding(
              padding: const EdgeInsets.only(left: 20.0, right: 10.0),
              child: Text(
                'Congratulations on successfully creating your account! We are delighted to have you as part of our exclusive '
                    'community of editors, where we delve into the captivating realms of cyber security and software engineering. '
                    'To ensure the security and integrity of our platform, we have initiated an email verification process. '
                    'Please take a moment to check your email inbox for a message from us. Inside, you will find a verification '
                    'link that will allow you to confirm your registration.In case the email does not appear in your primary inbox, '
                    'kindly check your spam or junk folder, as occasionally legitimate emails may be filtered there by mistake. '
                    'If you require any assistance or encounter any issues during the registration process, please don\'t hesitate '
                    'to reach out to our dedicated support team.Once you have successfully confirmed your registration, you will '
                    'gain access to our captivating blog posts, expert insights, and informative articles on cyber security and '
                    'software engineering. We are thrilled to have you on board and look forward to your active participation within '
                    'our vibrant community.Thank you for choosing us as your destination for valuable cyber security and software '
                    'engineering content. We are confident that your journey with us will be enlightening and rewarding.',
                style: kSigninSubtitleStyle(size),
              ),
            ),
            SizedBox(
              height: size.height * 0.03,
            ),
          ],
        ),
      ),
    );
  }
}
