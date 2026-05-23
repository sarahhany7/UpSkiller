<?php
// إعدادات قاعدة البيانات
$host = 'localhost';
$db   = 'UpSkiller'; // تم التعديل إلى اسم قاعدة البيانات الخاص بكِ
$user = 'root'; 
$pass = ''; // كلمة المرور الافتراضية لـ XAMPP (عادةً فارغة)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // إرجاع رسالة خطأ واضحة
     http_response_code(500);
     echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
     exit();
}
// لا يتم إرسال Header هنا، بل في ملف api.php
?>