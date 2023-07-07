import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

TextStyle kSigninTitleStyle(Size size) => GoogleFonts.ubuntu(
  fontSize: size.height * 0.060,
  fontWeight: FontWeight.bold,
);

TextStyle kSigninSubtitleStyle(Size size) => GoogleFonts.ubuntu(
  fontSize: size.height * 0.025,
  height: 1.5,
);

TextStyle kSigninTermsAndPrivacyStyle(Size size) =>
    GoogleFonts.ubuntu(fontSize: 15, color: Colors.grey, height: 1.5);

TextStyle kHaveAnAccountStyle(Size size) =>
    GoogleFonts.ubuntu(fontSize: size.height * 0.022, color: Colors.black);

TextStyle kSigninOrSignUpTextStyle(
    Size size,
    ) =>
    GoogleFonts.ubuntu(
      fontSize: size.height * 0.022,
      fontWeight: FontWeight.w500,
      color: const Color(0xFF4A9385),
    );

TextStyle kTextFormFieldStyle() => const TextStyle(color: Colors.black);
