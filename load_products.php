<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db"; // Change this if your DB is named differently

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$file = fopen("products.csv", "r");
fgetcsv($file); // skip header

while (($data = fgetcsv($file)) !== FALSE) {
    $stmt = $conn->prepare("INSERT INTO products (id, cost, category, name, brand, retail_price, department, sku, distribution_center_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("idssssssi", 
        $data[0], // id
        $data[1], // cost
        $data[2], // category
        $data[3], // name
        $data[4], // brand
        $data[5], // retail_price
        $data[6], // department
        $data[7], // sku
        $data[8]  // distribution_center_id
    );

    $stmt->execute();
}

fclose($file);
$conn->close();

echo "Data loaded successfully.";
?>
