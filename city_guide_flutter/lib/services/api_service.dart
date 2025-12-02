// lib/services/api_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config.dart';

class ApiService {
  static Future<Map<String,dynamic>?> post(String url, Map body) async {
    try {
      final res = await http.post(Uri.parse("$API_BASE/$url"),
        body: jsonEncode(body),
        headers: {'Content-Type':'application/json'},
      ).timeout(const Duration(seconds: 15));
      return jsonDecode(res.body) as Map<String,dynamic>;
    } catch (e) {
      print('ApiService.post error: $e');
      return {'status': false, 'message': 'Network error: $e'};
    }
  }

  static Future<Map<String,dynamic>?> get(String url, [Map<String,String>? params]) async {
    try {
      String u = "$API_BASE/$url";
      if (params != null && params.isNotEmpty) {
        u = u + "?" + params.entries.map((e) => "${Uri.encodeComponent(e.key)}=${Uri.encodeComponent(e.value)}").join("&");
      }
      final res = await http.get(Uri.parse(u)).timeout(const Duration(seconds: 15));
      return jsonDecode(res.body) as Map<String,dynamic>;
    } catch (e) {
      print('ApiService.get error: $e');
      return {'status': false, 'message': 'Network error: $e'};
    }
  }
}
