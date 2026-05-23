<?php
// 1. إعداد الاستجابة والاتصال
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization'); 

// تضمين ملف الاتصال بقاعدة البيانات
require 'includes/db.php'; 

// التحقق من وجود متغير 'action' لتحديد الوظيفة المطلوبة
$action = $_GET['action'] ?? '';

// 2. منطق التوجيه (Routing Logic)
switch ($action) {
    
    // --- 1. جلب التراكات (GET) ---
    case 'get_tracks':
        try {
            // جلب track_id, track_name, description من جدول Tracks
            $stmt = $pdo->query('SELECT track_id, track_name, description FROM Tracks');
            $tracks = $stmt->fetchAll();
            echo json_encode(["success" => true, "data" => $tracks]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Failed to fetch tracks: " . $e->getMessage()]);
        }
        break;

    // --- 2. جلب تفاصيل تراك محدد (GET) ---
    case 'get_track_details':
        if (!isset($_GET['track_id']) || !is_numeric($_GET['track_id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Track ID is missing or invalid."]);
            break;
        }
        $trackId = $_GET['track_id'];

        try {
            // جلب اسم التراك
            $stmt = $pdo->prepare('SELECT track_name FROM Tracks WHERE track_id = ?');
            $stmt->execute([$trackId]);
            $track = $stmt->fetch();

            if (!$track) {
                http_response_code(404);
                echo json_encode(["success" => false, "error" => "Track not found."]);
                break;
            }
            
            // جلب المهارات
            $stmt = $pdo->prepare('SELECT skill_id, skill_name FROM Skills WHERE track_id = ?');
            $stmt->execute([$trackId]);
            $skills = $stmt->fetchAll();
            
            // جلب المصادر والمنصات المرتبطة بالتراك
            $stmt = $pdo->prepare('
                SELECT 
                    r.resource_id, r.resource_name, r.resource_url, p.platform_name 
                FROM Resources r
                JOIN Platforms p ON r.platform_id = p.platform_id
                WHERE r.track_id = ?
            ');
            $stmt->execute([$trackId]);
            $resources = $stmt->fetchAll();

            echo json_encode([
                "success" => true,
                "track_name" => $track['track_name'],
                "skills" => $skills,
                "resources" => $resources
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Failed to fetch track details: " . $e->getMessage()]);
        }
        break;

    // --- 3. تسجيل تقييم (POST) ---
    case 'submit_rating':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Only POST requests are allowed."]);
            break;
        }

        // قراءة بيانات الـ JSON المُرسلة
        $input = json_decode(file_get_contents('php://input'), true);

        // التحقق من صحة المدخلات
        if (
            !isset($input['resource_id']) || !is_numeric($input['resource_id']) ||
            !isset($input['rating']) || !is_numeric($input['rating']) || 
            $input['rating'] < 1 || $input['rating'] > 5
        ) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Invalid or missing Resource ID or Rating (must be 1-5)."]);
            break;
        }

        $resourceId = $input['resource_id'];
        $ratingValue = $input['rating'];

        try {
            // تخزين التقييم في جدول Ratings
            $stmt = $pdo->prepare("INSERT INTO Ratings (resource_id, rating_value) VALUES (?, ?)");
            $stmt->execute([$resourceId, $ratingValue]);
            
            echo json_encode([
                "success" => true,
                "message" => "Rating submitted successfully."
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Failed to submit rating: " . $e->getMessage()]);
        }
        break;

    // --- 4. جلب متوسط تقييم (GET) ---
    case 'get_average_rating':
        if (!isset($_GET['resource_id']) || !is_numeric($_GET['resource_id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Resource ID is missing or invalid."]);
            break;
        }

        $resourceId = $_GET['resource_id'];

        try {
            // حساب متوسط التقييم
            $stmt = $pdo->prepare('
                SELECT 
                    AVG(rating_value) AS average_rating, 
                    COUNT(rating_id) AS total_ratings
                FROM Ratings 
                WHERE resource_id = ?
            ');
            $stmt->execute([$resourceId]);
            $result = $stmt->fetch();
            
            $averageRating = $result['average_rating'] ? round($result['average_rating'], 2) : 0;
            
            echo json_encode([
                "success" => true,
                "resource_id" => (int)$resourceId,
                "average_rating" => $averageRating,
                "total_ratings" => (int)$result['total_ratings']
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Failed to retrieve average rating: " . $e->getMessage()]);
        }
        break;

    default:
        // إذا لم يتم تحديد action صحيح
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Action not found. Please specify ?action=..."]);
        break;
}

?>
