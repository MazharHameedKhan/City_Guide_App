<?php
// ===============================
// config.php
// Location: C:\xampp\htdocs\cityguide_api\config.php
// ===============================
// IMPORTANT: Do not add any blank lines or spaces before "<?php"

// ---- CORS HEADERS ----
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Allow the specific origin making the request
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    // Allow all origins (development mode)
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ---- ERROR HANDLING ----
ini_set('display_errors', 0); // Hide PHP warnings from frontend
error_reporting(E_ALL);

// ---- DATABASE CONFIG ----
$host = 'localhost';          // XAMPP default
$db   = 'cityguide';          // Change if your DB name differs
$user = 'root';               // XAMPP default
$pass = '';                   // No password in default XAMPP
$base_url = "http://localhost/cityguide_api"; // Adjust if your folder name differs

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    // Write error to Apache log and send JSON response
    error_log("DB connection failed: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Database connection error']);
    exit;
}

// ---- OPTIONAL: HELPER FUNCTION ----
function full_upload_url($filename) {
    global $base_url;
    return "$base_url/uploads/$filename";
}
