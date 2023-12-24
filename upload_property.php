<?php
require_once "config.php";

session_start();

// Check if the user is not logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if (!file_exists('image_uploads')) {
    mkdir('image_uploads', 0777, true);
}
if (!file_exists('registry_uploads')) {
    mkdir('registry_uploads', 0777, true);
}

$walletAddress = isset($_COOKIE['walletAddress']) ? $_COOKIE['walletAddress'] : null;
// User is logged in, retrieve the username and user_id
$username = $_SESSION["username"];
$user_id = $_SESSION["id"];
$file_upload_error = "";
$upload_success = "";

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = trim($_POST["address"]);
    $price = trim($_POST["price"]);
    $image_path = "";
    $registry_path = "";

    // Check if file was uploaded without errors
    if(isset($_FILES["papers"]) && $_FILES["papers"]["error"] == 0) {
        // Accept PDF files only
        $allowed = array("pdf" => "application/pdf");
        $filename = $_FILES["papers"]["name"];
        $filetype = $_FILES["papers"]["type"];
        $filesize = $_FILES["papers"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) $file_upload_error = "Error: Please select a valid file format.";

        // Verify file size - 10MB maximum
        $maxsize = 10 * 1024 * 1024;
        if($filesize > $maxsize) $file_upload_error = "Error: File size is larger than the allowed limit.";

        // Verify MIME type of the file
        if(in_array($filetype, $allowed) && empty($file_upload_error)){
            // Check whether file exists before uploading it
            if(file_exists("registry_uploads/" . $filename)){
                $file_upload_error = $filename . " is already exists.";
            } else {
                // Move the file to the upload directory
                move_uploaded_file($_FILES["papers"]["tmp_name"], "registry_uploads/" . $filename);
                $registry_path = "registry_uploads/" . $filename;

                // Generate hash of the file
                $hash = hash_file('sha256', $registry_path);

                // Handle image upload if present
                if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                    // Define allowed file types for images
                    $allowed_image = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
                    $filename_image = $_FILES["image"]["name"];
                    $filetype_image = $_FILES["image"]["type"];
                    $filesize_image = $_FILES["image"]["size"];

                    // Verify file extension and size for image
                    // Similar checks as for papers

                    // Check whether image file exists before uploading it
                    if(file_exists("image_uploads/" . $filename_image)){
                        $file_upload_error = $filename_image . " is already exists.";
                    } else {
                        // Move the image to the upload directory
                        move_uploaded_file($_FILES["image"]["tmp_name"], "image_uploads/" . $filename_image);
                        $image_path = "image_uploads/" . $filename_image;
                    } 
                }

                // Prepare an insert statement
                $sql = "INSERT INTO properties (user_id, address, price, image_path, paper_hash, paper_path, wallet_addr) VALUES (?, ?, ?, ?, ?, ?, ?)";

                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "isdssss", $param_user_id, $param_address, $param_price, $param_image_path, $param_hash, $param_paper_path, $param_wallet_addr);

                    // Set parameters
                    $param_user_id = $user_id;
                    $param_address = $address;
                    $param_price = $price;
                    $param_image_path = $image_path;
                    $param_hash = $hash; // binding the generated hash
                    $param_paper_path = $registry_path;
                    $param_wallet_addr = $walletAddress;

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to success page or display success message
                        $upload_success = "Property uploaded successfully.";
                        echo "<script>var isFormSubmitted = true;</script>";
                    } else{
                        echo "<script>var isFormSubmitted = false;</script>";
                        $file_upload_error = "Error: Could not execute query: $sql. " . mysqli_error($link);
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
                mysqli_close($link);
            } 
        } else {
            $file_upload_error .= "Error: There was a problem uploading your file. Please try again."; 
        }
    } else {
        $file_upload_error .= "Error: " . $_FILES["papers"]["error"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload - Smart Estate</title>
    <link rel="stylesheet" href="style.css">
    <!-- Include Bootstrap CSS if you're using Bootstrap components -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1ba5ff; /* Blue color for the navbar */
    color: white;
    padding: 10px 20px;
}

nav .logo img {
    height: 60px; /* Height of the logo */
    width: 250px; /* Width of the logo */
}

nav .nav-links {
    list-style: none;
}

nav .nav-links li {
    display: inline;
    margin-left: 20px;
}

nav .nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
}

nav .nav-links a:hover {
    text-decoration: underline;
}

.centered-title h1 {
    text-align: center;
}

.centered-title {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

</style>
</head>

<body>
    <main>
        <nav>
        <div class="logo">
            <!-- Wrap the image in an anchor tag pointing to the homepage -->
            <a href="/">
                <img src="logo.png" alt="Logo">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="login.php">Login/Register</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        </nav>
        <div class="jumbotron centered-title">
            <div class="container centered-title">
                <h1><b>Upload Property Listing - Smart Estate</b></h1>
            </div>
        </div>
        <!-- Display the greeting with username -->
        <div class="container">
            <h4 style="text-align: center;">Hello, <?php echo htmlspecialchars($username); ?>, connect your metamask, refresh page, and put your listing now!</h4>
            <!-- <br><button onclick="connectMetaMask()" style="text-align: center;">Connect MetaMask</button> -->
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/web3@1.3.0/dist/web3.min.js"></script>
    <script>
    var contractABI = [{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowDeposits","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowPropertyHashes","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowTimestamps","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyOwners","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyPrices","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"releaseEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"sendToEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"},{"internalType":"uint256","name":"price","type":"uint256"}],"name":"storePropertyHash","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"verifyPropertyOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"}];
    var contractAddress = '0xB9D1F8D5FEae704f4Cd243C2A3E4faEa41CF02a6';

    var walletAddress = '<?php echo $walletAddress; ?>';

    // Initialize web3 instance if MetaMask is present
    if (typeof window.ethereum !== 'undefined') {
        window.web3 = new Web3(window.ethereum);
    } else {
        console.log('MetaMask not detected. Please install MetaMask.');
    }

    // Function to connect to MetaMask
    async function connectMetaMask() {
        if (typeof window.ethereum !== 'undefined') {
            console.log('MetaMask is installed!');

            try {
                // Request account access
                const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
                const account = accounts[0];
                console.log('Connected account:', account);
                document.getElementById('walletAddressDisplay').textContent = 'Connected Wallet: ' + account;
                setCookie('walletAddress', account, 1);
                return account;
            } catch (error) {
                console.error('Error connecting to MetaMask:', error);
                document.getElementById('walletAddressDisplay').textContent = 'Error connecting to MetaMask.';
            }
        } else {
            console.log('MetaMask is not installed.');
            document.getElementById('walletAddressDisplay').textContent = 'MetaMask is not installed.';
        }
    }

    // Function to set a cookie
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    document.addEventListener('DOMContentLoaded', function() {
        //console.log("function called");
        //var priceValue = document.getElementById('price').value;
        <?php if(isset($price)): ?>
            var priceFromPHP = '<?php echo $price; ?>';
            console.log("Price from PHP: ", priceFromPHP);
        <?php endif; ?>

        console.log("function called. Price: ", priceFromPHP);
        // Check if form was submitted and hash is set
        if (typeof isFormSubmitted !== 'undefined' && isFormSubmitted) {
            <?php if (isset($hash)): ?>
                var propertyHash = '<?php echo $hash; ?>';
                
                //console.log("Price: "+priceValue);
                //var price = parseInt(priceValue, 10); // Convert to integer; use parseFloat if decimal is allowed\
                //console.log(price);
                /*if (!priceValue || isNaN(price)) {
                    alert("Please enter a valid price.");
                    return;
                }*/
                if (propertyHash) {
                    storePropertyHashOnBlockchain(propertyHash, priceFromPHP);
                }
            <?php endif; ?>
        }
    });

    async function storePropertyHashOnBlockchain(propertyHash, price) {
        let account = walletAddress || await connectMetaMask();

        if (!account) {
            updateLogOutput("MetaMask account not connected.");
            return false;
        }

        const contract = new web3.eth.Contract(contractABI, contractAddress);
        contract.methods.storePropertyHash(propertyHash, price).send({ from: account })
            .on('receipt', function(receipt) {
                updateLogOutput("Hash stored in block: " + receipt.blockNumber);
                updateTransactionOutput("Transaction ID: " + receipt.transactionHash);
            })
            .on('error', function(error) {
                updateLogOutput("Error storing hash: " + error.message);
            });
    }

    function updateTransactionOutput(message) {
        alert(message); // Show the message in an alert box
        window.location.href = 'index.php'; // Redirect to index.php after clicking "OK"
    }

    function updateLogOutput(message) {
        alert(message); // Show the message in an alert box
    }

    async function onFormSubmit() {
        var priceValue = document.getElementById('price').value;
        console.log("function called. Price: " + priceValue);
        <?php if (isset($hash)): ?>
            var propertyHash = '<?php echo $hash; ?>';
            var priceValue = document.getElementById('price').value;
            console.log("Price: "+priceValue);
            var price = parseInt(priceValue, 10); // Convert to integer; use parseFloat if decimal is allowed
            console.log(price);
            if (!priceValue || isNaN(price)) {
                alert("Please enter a valid price.");
                return;
            }
            if (propertyHash) {
                await storePropertyHashOnBlockchain(propertyHash, price);
            }
        <?php endif; ?>
    }
</script>


    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="col-md-6">
                        <button onclick="connectMetaMask()" style="text-align: center;">Connect MetaMask</button>
                        <?php 
                            if(isset($_COOKIE['walletAddress'])) {
                                $walletAddress = $_COOKIE['walletAddress'];
                                // Process or store the wallet address as needed
                            }
                        ?>
                        <br><p id="walletAddressDisplay"></p>
                        <div id="logOutput"></div>
                        <div id="transactionOutput"></div>
                        
                </div>
                <form method="post" action="" enctype="multipart/form-data" autocomplete="off">
                    <div class="form-group">
                        <b><label for="address">Property Address</label></b>
                        <input autofocus class="form-control" id="addreess" name="address" required type="text">
                    </div>
                    <div class="form-group">
                        <b><label for="price">Price in USD</label></b>
                        <input class="form-control" id="price" name="price" required type="number">
                    </div>
                    <div class="form-group">
                        <b><label for="image">Property Image</label></b>
                        <input class="form-control" id="image" name="image" required type="file">
                    </div>
                    <div class="form-group">
                        <b><label for="papers">Registry Papers</label></b>
                        <input class="form-control" id="papers" name="papers" required type="file">
                    </div>
                    <div class="col-md-6">
                        <input class="btn btn-md btn-primary btn-outlined float-right" id="_upload" name="_upload" type="Submit" value="Upload">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>