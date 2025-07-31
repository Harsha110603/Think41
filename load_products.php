<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load CSV
if (($handle = fopen("products.csv", "r")) !== false) {
    fgetcsv($handle); // skip header

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        list($id, $name, $brand, $cost, $image, $description, $rating, $department_name) = $data;

        // Insert department if not exists
        $dept_stmt = $conn->prepare("INSERT IGNORE INTO department_list (name) VALUES (?)");
        $dept_stmt->bind_param("s", $department_name);
        $dept_stmt->execute();
        $dept_stmt->close();

        // Get department id
        $dept_id_stmt = $conn->prepare("SELECT id FROM department_list WHERE name = ?");
        $dept_id_stmt->bind_param("s", $department_name);
        $dept_id_stmt->execute();
        $dept_id_result = $dept_id_stmt->get_result();

        if (!$dept_id_result) {
            die("Department query failed: " . $dept_id_stmt->error);
        }

        $dept_id_row = $dept_id_result->fetch_assoc();
        $department_id = $dept_id_row['id'];
        $dept_id_stmt->close();

        // Insert product
        $prod_stmt = $conn->prepare("INSERT INTO product_list (id, name, brand, cost, image, description, rating, department_id)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$prod_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $prod_stmt->bind_param("issdssdi", $id, $name, $brand, $cost, $image, $description, $rating, $department_id);
        $prod_stmt->execute();
        $prod_stmt->close();
    }

    fclose($handle);
    echo "Products loaded successfully.";
} else {
    echo "Failed to open CSV file.";
}

$conn->close();
?>
