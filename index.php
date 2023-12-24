<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "config.php"; // Include the database connection

// Fetch properties from the database
$propertyId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Modify the SQL query to fetch a specific property
if ($propertyId > 0) {
    $sql = "SELECT properties.*, users.username, users.account, properties.paper_hash FROM properties JOIN users ON properties.user_id = users.id WHERE properties.id = $propertyId";
} else {
    // Fetch all properties if no specific ID is set
    $sql = "SELECT properties.*, users.username, users.account FROM properties JOIN users ON properties.user_id = users.id";
}

$result = mysqli_query($link, $sql);
if (!$result) {
    die("ERROR: Could not able to execute $sql. " . mysqli_error($link));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/web3@1.3.0/dist/web3.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Estate - Real Estate on Blockchain!</title>
    <link rel="stylesheet" href="style.css">

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

.card-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    padding: 20px;
}

.card {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    width: 300px;
    margin: 10px;
    border-radius: 5px;
    background-color: white;
    padding: 15px;
}

.card:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

.card img {
    width: 100%;
    height: 200px;
    border-radius: 5px 5px 0 0;
}

.card-content {
    padding: 15px;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
}

.card-text {
    font-size: 16px;
}

.wallet-address {
        word-break: break-all; /* This ensures long text will wrap and not overflow */
}

.enlarged {
    transform: scale(1.1);
    z-index: 10;
    position: relative;
    width: 500px; /* Adjusted width */
    height: auto; /* Adjust height as needed or keep it auto */
    /* Other properties */
}

/* Style for buttons */
.enlarged button {
    margin-top: 20px;
    cursor: pointer;
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card-link:hover .card {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.3);
}
.buttons-container {
    text-align: center; /* Center the buttons */
    margin-top: 10px; /* Add some space above the buttons */
}

.buttons-container button {
    margin: 5px; /* Space between buttons */
    padding: 10px 20px; /* Padding inside buttons */
    cursor: pointer; /* Change cursor on hover */
}

</style>
</head>
<body>
    <nav>
        <div class="logo">
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
<h4>Explore our awesome listings now!</h4>
<div class="card-container">
    <?php 
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $currentPropertyId = $row['id']; // Keep it as it is from the database, without htmlspecialchars
            $propertyHash = $row['paper_hash'];
            // Determine if it's a detailed view of a specific property
            $isDetailView = ($propertyId > 0) && ($propertyId == $currentPropertyId); // Use == for comparison


            // Wrap with link if not in detailed view
            if (!$isDetailView) {
                echo '<a href="?id=' . $currentPropertyId . '" class="card-link">';
            }

            echo '<div class="card' . ($isDetailView ? ' enlarged' : '') . '">';

            // Image and content
            if ($row['image_path']) {
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Property Image">';
            }
            echo '<div class="card-content">';
            echo '<h3 class="card-title">' . htmlspecialchars($row['address']) . '</h3>';
            echo '<p class="card-text">Price: $' . htmlspecialchars($row['price']) . '</p>';
            echo '<p class="card-text">Owner: ' . htmlspecialchars($row['username']) . '</p>';
            echo '<p class="card-text wallet-address">Web3 Address: ' . htmlspecialchars($row['wallet_addr']) . '</p>';
            echo '</div>'; // End of card-content

            echo '</div>'; // End of card

            // Close link tag if not in detailed view
            if (!$isDetailView) {
                echo '</a>'; 
            }

            // Buttons for detailed view
            if ($isDetailView) {
                echo '<div class="buttons-container">';
                echo '<h3>Choose an operation</h3>';
                echo '<button onclick="verifyProperty(' . $currentPropertyId . ', \'' . $propertyHash . '\');">Verify</button>';
                echo '<div id="verificationResult"></div>';
                echo '<button onclick="window.location.href=\'escrow.php?id=' . $currentPropertyId . '\'">Buy</button>';
                echo '</div>';
            }
        }
    } else {
        echo "<p>No properties found.</p>";
    }
    ?>
</div>

<script>
var contractABI = [{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowDeposits","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowPropertyHashes","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowTimestamps","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyOwners","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyPrices","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"releaseEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"sendToEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"},{"internalType":"uint256","name":"price","type":"uint256"}],"name":"storePropertyHash","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"verifyPropertyOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"}];  // Your contract ABI
var contractAddress = '0xB9D1F8D5FEae704f4Cd243C2A3E4faEa41CF02a6';  // Your contract address

let web3;
if (typeof window.ethereum !== 'undefined') {
    web3 = new Web3(window.ethereum);
    // Request account access if necessary
    ethereum.request({ method: 'eth_requestAccounts' });
} else {
    // Handle the case where the user doesn't have MetaMask
    console.log('MetaMask is not installed!');
}


// Assuming web3 is initialized and connected to MetaMask or other provider
var contract = new web3.eth.Contract(contractABI, contractAddress);

function verifyProperty(propertyId, propertyHash) {
    if (!web3) {
        console.log("Web3 instance is not available.");
        return;
    }

    const contract = new web3.eth.Contract(contractABI, contractAddress);
    contract.methods.verifyPropertyOwner(propertyHash).call()
    .then(function(ownerAddress) {
        var verificationResultElement = document.getElementById('verificationResult');

        if (ownerAddress && ownerAddress !== '0x0000000000000000000000000000000000000000') {
            // Display the actual owner address from the contract
            verificationResultElement.innerHTML = "Owner Address: " + ownerAddress;
            verificationResultElement.style.color = "green";
        } else {
            // If the hash is not found, display a message
            verificationResultElement.innerHTML = "Property hash not found in contract. Counterfeit detected!";
            verificationResultElement.style.color = "red";
        }
    })
    .catch(function(error) {
        console.error("Error verifying property: ", error);
        document.getElementById('verificationResult').innerHTML = "Error occurred during verification.";
    });
}


function buyProperty(propertyId) {
    console.log("Buying property with ID: " + propertyId);
    // Implement your logic to buy the property
}
</script>
</body>
</html>