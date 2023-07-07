import 'package:dio/dio.dart';
import 'package:dio_cookie_manager/dio_cookie_manager.dart';
import 'package:cookie_jar/cookie_jar.dart';

class ApiService {
  late final Dio _dio;
  late final CookieJar _cookieJar;

  ApiService() {
    _dio = Dio();
    _cookieJar = CookieJar();
    _dio.interceptors.add(CookieManager(_cookieJar));
  }

  Future<Response> post(String url, {required Map<String, dynamic> data}) async {
    return _dio.post(
      url,
      options: Options(headers: {'Content-Type': 'application/json; charset=UTF-8'}),
      data: data,
    );
  }

  Future<Response> get(String url) async {
    return _dio.get(
      url,
      options: Options(headers: {'Content-Type': 'application/json; charset=UTF-8'}),
    );
  }

  Future<Response> put(String url, {required Map<String, dynamic> data}) async {
    return _dio.put(
      url,
      options: Options(headers: {'Content-Type': 'application/json; charset=UTF-8'}),
      data: data,
    );
  }

  Future<Response> delete(String url) async {
    return _dio.delete(
      url,
      options: Options(headers: {'Content-Type': 'application/json; charset=UTF-8'}),
    );
  }
}
