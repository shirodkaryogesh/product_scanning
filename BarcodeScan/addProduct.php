<?php
//connection to database
// $mysqli = new mysqli('localhost', 'u748706600_scan_product', 'Edpl@123', 'u748706600_scan_product');
$mysqli = new mysqli('localhost', 'root', '', 'scan_product');

//using piquer to generate barcodes
require_once __DIR__ . '/vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

//randomly generating id's
function generateProductId() {
    return mt_rand(1000, 9999);
}

//to insert data in the products db
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $product_id = generateProductId();

    //generating barcode and inserting product_id,name,price as it require after scanning the barcode
    $generator = new BarcodeGeneratorPNG();
    $barcode_content = $product_id . '_' . $name . '_' . $price;
    $barcode_image = $generator->getBarcode($barcode_content, $generator::TYPE_CODE_128);

    //saving it in barcode directory
    $file_path = 'barcodes/' . $barcode_content . '.png';
    file_put_contents($file_path, $barcode_image);
    // $barcode_image_url = 'http://localhost/product_scanning/BarcodeScan/barcodes/' . $barcode_content . '.png';
    $barcode_image_url = 'https://edhaasdigisoft.co.in/interns/Yogesh/BarcodeScan/barcodes/' . $barcode_content . '.png';

    //insert in products table
    $sql = "INSERT INTO product (product_id, name, price, quantity, barcode, barcode_content) VALUES (?, ?, ?, ?, ?,?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('isssss', $product_id, $name, $price, $quantity, $barcode_image_url, $barcode_content);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Data added successfully</div>';
    } else {
        $message = '<div class="alert alert-danger">Error: ' . $mysqli->error . '</div>';
    }
    $stmt->close();
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Add Product</title>
    <style>
        .main-container{
            height:100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container{
            width: 674px;
            border: 2px solid black;
            padding: 17px;
            border-radius: 10px;
        }
        .btn {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container">
            <div class="header">
                <a class="btn btn-success mt-1 mb-2" href="index.php">Back</a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo isset($message)? $message: "";?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="">Price</label>
                            <input type="text" name="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group mt-2 d-flex justify-content-between">
                            <button class="btn btn-success" name="submit">Add</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
