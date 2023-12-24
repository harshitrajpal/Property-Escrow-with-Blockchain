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
    <title>Property Registry</title>
    <script src="https://cdn.jsdelivr.net/npm/web3@1.3.0/dist/web3.min.js"></script>
</head>

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

.hash {
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
.button-card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Creates a responsive grid */
    gap: 20px; /* Space between cards */
    padding: 20px;
    word-break: break-all;
}

.button-card {
    background-color: white; /* Card background */
    border-radius: 10px; /* Rounded corners for the card */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Card shadow */
    padding: 20px;
    text-align: center;
    word-break: break-all;
}

.button-card button {
    background-color: #ff0000; /* Blue color */
    color: white;
    border: none;
    padding: 10px 15px;
    margin-top: 10px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%; /* Full width */
    transition: background-color 0.3s;
    word-break: break-all;
}

.button-card button:hover {
    background-color: #1487cc; /* Slightly darker blue on hover */
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
<h4>Explore our awesome listings now! Remember $1=1NYUD. Mint some coins right now!</h4>
    <h1>Escrow Operation for Property ID: <?php echo $propertyId; ?></h1>
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
            echo '<p class="card-text hash">Property Hash: ' . htmlspecialchars($row['paper_hash']) . '</p>';
            echo '<input type="hidden" id="propertyHash" value="' . htmlspecialchars($row['paper_hash']) . '">';
            echo '</div>'; // End of card-content

            echo '</div>'; // End of card

            // Close link tag if not in detailed view
            if (!$isDetailView) {
                echo '</a>'; 
            }

            // Buttons for detailed view
            if ($isDetailView) {
            	/*
                echo '<div class="buttons-container">';
                echo '<h3>Choose an operation</h3>';
                echo '<button onclick="verifyProperty(' . $currentPropertyId . ', \'' . $propertyHash . '\');">Verify</button>';
                echo '<div id="verificationResult"></div>';
                echo '<button onclick="window.location.href=\'escrow.php?id=' . $currentPropertyId . '\'">Buy</button>';
                echo '</div>';*/

            }
        }
    } else {
        echo "<p>No properties found.</p>";
    }
    ?>
</div>
    <h1>Property Registry Interaction</h1>
    <div class="button-card-container">
    <div class="button-card">
        <h3>Step 1</h3>
    </div>
    <div class="button-card">
        <h3>Step 2</h3>
        
    </div>
    <div class="button-card">
        <h3>Step 3</h3>

    </div>
    <div class="button-card">
        <h3>Step 4</h3>
    </div>
    <div class="button-card">
        <h3>Step 5</h3>
    </div>
    <div class="button-card">
        <h3>Step 6</h3>
    </div>
</div>
<div class="button-card-container">
    <div class="button-card">
        <h3>Connect MetaMask</h3>
        <button id="walletButtonColor" onclick="connectMetaMask()">Connect</button>
        <br><p id="walletAddressDisplay"></p>
    </div>
    <div class="button-card">
        <h3>Mint NYUD Tokens</h3>
        <button id="mintTokens">Mint Tokens</button>
    </div>
    <div class="button-card">
        <h3>Check Balance</h3>
        <button id="checkBalance">Check</button>
        <br><p id="tokenBalanceDisplay"></p>
    </div>
    <div class="button-card">
        <h3>Pay to Escrow(Wait:1 min)</h3>
        <button id="sendToEscrow">Pay</button>
    </div>
    <div class="button-card">
        <h3>Release Escrow</h3>
        <button id="releaseEscrow">Release</button>
    </div>
    <div class="button-card">
        <h3>Finish Transaction</h3>
        <button id="finishTransaction" onclick="finishTransaction(<?php echo $propertyId; ?>)">Finish</button>
    </div>
</div>
    <script>
        let web3;
        let contract;
        const contractABI = [{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowDeposits","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowPropertyHashes","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"escrowTimestamps","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyOwners","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"propertyPrices","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"releaseEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"sendToEscrow","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"},{"internalType":"uint256","name":"price","type":"uint256"}],"name":"storePropertyHash","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"propertyHash","type":"string"}],"name":"verifyPropertyOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"}];
        const contractAddress = '0xB9D1F8D5FEae704f4Cd243C2A3E4faEa41CF02a6';

        window.addEventListener('load', () => {
            if (typeof window.ethereum !== 'undefined') {
                web3 = new Web3(window.ethereum);
                contract = new web3.eth.Contract(contractABI, contractAddress);
            } else {
                console.error("MetaMask is not installed!");
            }
        });

        async function connectMetaMask() {
        	console.log("connect called");
        if (typeof window.ethereum !== 'undefined') {
            console.log('MetaMask is installed!');

            try {
                // Request account access
                const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
                const account = accounts[0];
                console.log('Connected account:', account);
                document.getElementById('walletAddressDisplay').textContent = 'Connected Wallet: ' + account;
                setCookie('walletAddress', account, 1);
                document.getElementById('walletButtonColor').style.backgroundColor = 'green';
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
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
        document.getElementById('mintTokens').addEventListener('click', async () => {
    const accounts = await web3.eth.getAccounts();
    contract.methods.mintTokens().send({ from: accounts[0] })
        .on('receipt', function(receipt) {
            alert('Minting successful! Transaction ID: ' + receipt.transactionHash);
            document.getElementById('mintTokens').style.backgroundColor = 'green';
        })
        .on('error', function(error) {
            alert('Minting failed: ' + error.message);
        });
});


        document.getElementById('checkBalance').addEventListener('click', async () => {
    	const accounts = await web3.eth.getAccounts();
    	const account = accounts[0];
    	contract.methods.balanceOf(account).call()
        .then(function(balance) {
            document.getElementById('tokenBalanceDisplay').textContent = 'Balance: ' + balance + ' NYUD Tokens';
            document.getElementById('checkBalance').style.backgroundColor = 'green';
        })
        .catch(function(error) {
            console.error("Error getting token balance: ", error);
            document.getElementById('tokenBalanceDisplay').textContent = 'Error getting token balance.';
        });
});


        //document.getElementById('storePropertyHash').addEventListener('click', async () => {
        //   const propertyHash = document.getElementById('propertyHash').value;
        //    const accounts = await web3.eth.getAccounts();
        //    contract.methods.storePropertyHash(propertyHash).send({ from: accounts[0] });
        //});

        document.getElementById('sendToEscrow').addEventListener('click', async () => {
        	console.log("send to escrow called");
            const propertyHash = document.getElementById('propertyHash').value;
            if (!propertyHash) {
            	console.log("no hash");
        		console.error("Property hash is required.");
        		return;
    		}
            const accounts = await web3.eth.getAccounts();

            contract.methods.sendToEscrow(propertyHash).send({ from: accounts[0] })
            .on('receipt', function(receipt) {
            	alert('Sending to Escrow successful! Transaction ID: ' + receipt.transactionHash);
            	document.getElementById('sendToEscrow').style.backgroundColor = 'green';
        	})
        	.on('error', function(error) {
            	alert('Sending to Escrow failed: ' + error.message);
        	});

        });

        document.getElementById('releaseEscrow').addEventListener('click', async () => {
            const accounts = await web3.eth.getAccounts();
            contract.methods.releaseEscrow().send({ from: accounts[0] })
            .on('receipt', function(receipt) {
            	alert('Releasing from Escrow successful! Transaction ID: ' + receipt.transactionHash);
            	document.getElementById('releaseEscrow').style.backgroundColor = 'green';
        	})
        	.on('error', function(error) {
            	alert('Releasing from Escrow failed: ' + error.message);
        	});
        });

        function finishTransaction(propertyId) {
    	if (propertyId > 0) {
        	// Redirect to success.php with the property ID
        	window.location.href = 'success.php?id=' + propertyId;
    	} else {
        	console.error("No property ID provided.");
        	// Handle the case where no property ID is available
    	}
}

    </script>


</body>
</html>
