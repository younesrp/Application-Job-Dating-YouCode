<?php
// debug.php

$directory = 'assets/logos/';
$fullPath = __DIR__ . '/' . $directory;

echo "<h1>🔍 تشخيص الصور (Diagnostic)</h1>";

// 1. واش الدوسي كاين؟
if (is_dir($fullPath)) {
    echo "<p style='color:green'>✅ المجلد assets/logos موجود!</p>";
    echo "<p>المسار الكامل: " . $fullPath . "</p>";
} else {
    echo "<p style='color:red'>❌ المجلد assets/logos غير موجود!</p>";
    echo "<p>PHP كيقلب هنا: " . $fullPath . "</p>";
    exit; // وقف هنا
}

// 2. شنو كاين وسط الدوسي؟
$files = scandir($fullPath);
echo "<h2>📂 محتوى المجلد:</h2><ul>";

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "<li>📄 <strong>" . $file . "</strong>";
        
        // جرب تعرض التصويرة مباشرة
        echo "<br><img src='" . $directory . $file . "' style='height:50px; border:1px solid red;'>";
        echo " <a href='" . $directory . $file . "'>رابط مباشر</a>";
        echo "</li><hr>";
    }
}
echo "</ul>";
?>