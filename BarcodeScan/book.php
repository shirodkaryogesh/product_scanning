<?php
//connection to database
$mysqli = new mysqli('localhost', 'u748706600_scan_product', 'Edpl@123', 'u748706600_scan_product');
// $mysqli = new mysqli('localhost', 'root', '', 'scan_product');

    //fetching id from the url
    if(isset($_GET['id'])){
        $product_id = $_GET['id'];
        
        //fetching the product content with respect to fetched id
        $stmt = $mysqli->prepare('SELECT product_id,name,price,quantity,barcode, barcode_content from product WHERE product_id = ?');
        $stmt->bind_param('s',$product_id,);
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result-> num_rows > 0){
                //inserting fetched content of product in product_data variable
                $product_data = $result->fetch_assoc();
            }else{
                echo "no data found";
            }
        }
        $stmt->close();
    }

    //inserting in transaction table 
    if(isset($_POST['submit'])){
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $sql = 'INSERT INTO transaction (product_id, quantity) VALUES (?, ?)';
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ss',$product_id, $quantity);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Book Product</title>
    <style>
        .main-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container{
            width: auto;
            border: 1px solid gray;
            padding: 10px 16px 10px 16px;
            border-radius: 15px;
        }
        .left-col, .right-col{
            padding: 21px 23px 20px 30px;
        }
        form{
            width: 297px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col left-col">
                    <p>please scan the below barcode to update product</p>
                    <?php if(isset($product_data)) :?>
                        <!-- fetching the barcode img from products table -->
                        <img src="<?php echo $product_data['barcode']; ?>" alt="Barcode" style="height: 80px; width: 351px;">
                    <?php endif; ?>
                </div>
                <div class="col right-col">
                    <p>product details</p>
                    <form action="" method="post">
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="scannedData">Scanned Data</label>
                            <input type="text" id="scannedData" name="product_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="scannedName">Name</label>
                            <input type="text" id="scannedName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="scannedPrice">Price</label>
                            <input type="text" id="scannedPrice" name="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group mt-3 d-flex justify-content-between">
                            <button class="btn btn-success" name="submit">Update</button>
                            <a href="index.php" class="btn btn-success">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        // Handle barcode scanning
        $(document).on('keypress', function(event) {
            // Check if the Enter key is pressed
            if (event.which === 13) {
                // Get the scanned data
                let scannedData = "<?php echo isset($product_data) ? $product_data['barcode_content'] : ''; ?>";
                
                // splitting the scanned data into product ID, name, and price
                let scannedParts = scannedData.split('_');
                let scannedProductId = scannedParts[0];
                let scannedName = scannedParts[1];
                let scannedPrice = scannedParts[2];
                
                //inserting value in id,name,price
                $('#scannedData').val(scannedProductId);
                $('#scannedName').val(scannedName);
                $('#scannedPrice').val(scannedPrice);
            }
        });
    });
</script>

</body>
</html>