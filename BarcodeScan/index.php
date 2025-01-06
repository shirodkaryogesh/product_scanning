<?php
//connection to database
$mysqli = new mysqli('localhost', 'u748706600_scan_product', 'Edpl@123', 'u748706600_scan_product');
// $mysqli = new mysqli('localhost', 'root', '', 'scan_product');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// Fetch data from products and insert in products array
$stmt = $mysqli->prepare('SELECT product_id, name, quantity, price FROM product');
$products = [];
if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    //echo "<pre>";
    //print_r($products); // Print array using print_r
    // echo json_encode($transactions); // Print array as JSON
    //echo "</pre>";
    $stmt->close();
}


// Fetch data from transactions and insert in transaction array 
//here i will booked quantities of particular product with respect to id

$stmt = $mysqli->prepare('SELECT product_id, SUM(quantity) AS total_quantity FROM transaction GROUP BY product_id');
$transactions = [];
if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[$row['product_id']] = $row['total_quantity'];
        }
    }
    //echo "<pre>";
    //print_r($transactions); // Print array using print_r
    // echo json_encode($transactions); // Print array as JSON
    // echo "</pre>";
    $stmt->close();
}


// Calculate final quantities,fetching from products array and subtraction transaction array quantity with product table quantity
foreach ($products as &$product_val) {
    $product_id = $product_val['product_id'];
    $product_quantity = $product_val['quantity'];
    $booked_quantity = isset($transactions[$product_id]) ? $transactions[$product_id] : 0;
    $remaining_quantity = $product_quantity - $booked_quantity;
    $product_val['quantity'] = $remaining_quantity;
}
// isset($transactions[$product_id]) ? $transactions[$product_id] : 0
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
    <style>
        .header{
            padding: 14px 0px 12px 0px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a class="btn btn-success mt-1 mb-2 mx-2" href="addProduct.php">Add Product</a>
    </div>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <th class="text-center" style="width: 166px;">Sr.No</th>
                <th class="text-center" style="width: 295px;">Product</th>
                <th class="text-center" style="width: 295px;">Available Quantity</th>
                <th class="text-center" style="width: 295px;">Price</th>
            </thead>
            <tbody>
                <?php
                if (!empty($products)) {
                    $count = 1;
                    foreach ($products as $product) {?>
                        <tr>
                            <td><?php echo $count;?></td>
                            <td><?php echo $product['name'];?></td>
                            <td><?php echo $product['quantity'];?></td>
                            <td>Rs.<?php echo $product['price'];?></td>
                            <td class="text-center"><a class="btn btn-success" href="book.php?id=<?php echo $product['product_id'];?>">Book</a></td>
                        </tr>
                    <?php
                    $count++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
