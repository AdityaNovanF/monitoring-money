# Monitoring Money - Android App (Flutter WebView)

Aplikasi Android dari web app **Monitoring Money**, dibungkus menggunakan Flutter WebView.

## Cara Build

### Debug APK
```bash
cd flutter_app
flutter pub get
flutter build apk --debug
```
APK akan tersedia di: `build/app/outputs/flutter-apk/app-debug.apk`

### Release APK
```bash
flutter build apk --release
```

### Cara Install ke HP
1. Copy file `.apk` ke HP Android
2. Buka file manager, tap file APK tersebut
3. Izinkan "Install from unknown sources" jika diminta
4. Install dan jalankan

## Update Konten Web
Jika ada perubahan pada web app (`index.html`, `script.js`, atau assets):
1. Copy file yang berubah ke `flutter_app/assets/web/`
2. Jika ada perubahan pada `assets/logo-mm.png`, copy juga ke `flutter_app/assets/web/assets/`
3. Build ulang APK

## Struktur Project
```
flutter_app/
├── assets/web/           # File web app yang di-bundle
│   ├── index.html
│   ├── script.js
│   ├── logo-mm.png
│   └── assets/
│       └── logo-mm.png
├── lib/
│   └── main.dart         # Flutter WebView wrapper
├── android/              # Konfigurasi Android
└── pubspec.yaml          # Dependencies
```
