<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Enable CORS

include "db.php";

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Extract ID from URL if present
$path = explode("/", trim(parse_url($request, PHP_URL_PATH), "/"));

if ($method === "GET" && isset($path[1]) && $path[1] === "api" && isset($path[2]) && $path[2] === "products") {
    
    // GET /api/products
    if (!isset($path[3])) {
        $sql = "SELECT * FROM products LIMIT 100"; // You can implement pagination later
        $result = $conn->query($sql);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode($products);
        exit;
    }

    // GET /api/products/{id}
    elseif (is_numeric($path[3])) {
        $id = intval($path[3]);
        $sql = "SELECT * FROM products WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"]);
        }
        exit;
    }
}

// Invalid route
http_response_code(400);
echo json_encode(["error" => "Invalid request"]);
?>
