# Flutter WebView
-keep class io.flutter.** { *; }
-keep class io.flutter.plugins.** { *; }
-keep class androidx.webkit.** { *; }
-dontwarn io.flutter.embedding.**

# Missing Play Store classes
-dontwarn com.google.android.play.core.splitcompat.SplitCompatApplication
-dontwarn com.google.android.play.core.**
