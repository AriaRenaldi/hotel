class AppConfig {
  static const String appName = 'HotelKu';
  static const String defaultApiBase = 'http://10.0.2.2:8000/api';

  static String get apiBase {
    const fromDefine = String.fromEnvironment('API_BASE');
    if (fromDefine.isNotEmpty) return fromDefine;
    return defaultApiBase;
  }

  static String absoluteUrl(String? pathOrUrl) {
    if (pathOrUrl == null || pathOrUrl.isEmpty) return '';
    if (pathOrUrl.startsWith('http://') || pathOrUrl.startsWith('https://')) {
      return pathOrUrl;
    }

    final baseRoot = apiBase.replaceFirst(RegExp(r'/api/?$'), '');
    if (pathOrUrl.startsWith('/')) {
      return '$baseRoot$pathOrUrl';
    }
    return '$baseRoot/$pathOrUrl';
  }
}
