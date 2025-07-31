<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (($handle = fopen("products.csv", "r")) !== false) {
    fgetcsv($handle); // Skip header row

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        list($id, $name, $brand, $cost, $image, $description, $rating, $department_name) = $data;

        // Insert department if not exists
        $dept_stmt = $conn->prepare("INSERT IGNORE INTO department_list (name) VALUES (?)");
        $dept_stmt->bind_param("s", $department_name);
        $dept_stmt->execute();
        $dept_stmt->close();

        // Fetch department ID
        $dept_id_stmt = $conn->prepare("SELECT id FROM department_list WHERE name = ?");
        $dept_id_stmt->bind_param("s", $department_name);
        $dept_id_stmt->execute();
        $dept_id_result = $dept_id_stmt->get_result();
        $department_id = $dept_id_result->fetch_assoc()['id'];
        $dept_id_stmt->close();

        // Insert product
        $prod_stmt = $conn->prepare("INSERT INTO product_list (id, name, brand, cost, description, rating, department_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $prod_stmt->bind_param("issdsdi", $id, $name, $brand, $cost, $description, $rating, $department_id);
        $prod_stmt->execute();
        $prod_stmt->close();
    }

    fclose($handle);
    echo "Data loaded successfully.";
} else {
    echo "Failed to open file.";
}

$conn->close();
?>
