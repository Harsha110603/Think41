<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once "db.php";

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove the script name from URI
$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');

$parts = explode("/", $path);
$method = $_SERVER['REQUEST_METHOD'];

// ROUTES
if ($method === "GET" && count($parts) === 1 && $parts[0] === "api") {
    // GET /product-ui/departments.php/api
    getAllDepartments($conn);

} elseif ($method === "GET" && count($parts) === 2 && $parts[0] === "api") {
    // GET /product-ui/departments.php/api/{id}
    $id = (int)$parts[1];
    getDepartmentById($conn, $id);

} elseif ($method === "GET" && count($parts) === 3 && $parts[0] === "api" && $parts[2] === "products") {
    // GET /product-ui/departments.php/api/{id}/products
    $id = (int)$parts[1];
    getProductsByDepartment($conn, $id);

} else {
    http_response_code(404);
    echo json_encode(["error" => "Invalid request"]);
}

// FUNCTIONS

function getAllDepartments($conn) {
    $query = "
        SELECT d.id, d.name, COUNT(p.id) AS product_count
        FROM department_list d
        LEFT JOIN product_list p ON d.id = p.department_id
        GROUP BY d.id
    ";
    $result = $conn->query($query);
    $departments = [];

    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }

    echo json_encode(["departments" => $departments]);
}

function getDepartmentById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, name FROM department_list WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Department not found"]);
    } else {
        echo json_encode($result->fetch_assoc());
    }
}

function getProductsByDepartment($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM department_list WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $deptResult = $stmt->get_result();

    if ($deptResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Department not found"]);
        return;
    }

    $department = $deptResult->fetch_assoc()['name'];

    $stmt = $conn->prepare("SELECT id, name, brand, cost, rating FROM product_list WHERE department_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $prodResult = $stmt->get_result();

    $products = [];
    while ($row = $prodResult->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode([
        "department" => $department,
        "products" => $products
    ]);
}
?>
