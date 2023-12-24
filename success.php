<?php
require_once "config.php"; // Include the database connection

// Fetch property ID from the URL
$propertyId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if a valid property ID is provided
if ($propertyId > 0) {
    // Prepare a delete SQL statement
    $sql = "DELETE FROM properties WHERE id = $propertyId";

    // Execute the query
    $result = mysqli_query($link, $sql);

    if (!$result) {
        die("ERROR: Could not able to execute $sql. " . mysqli_error($link));
    }

    // Optional: Add additional logic after successful deletion
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
<h4>Explore our awesome listings now! Remember $1=1NYUD. Mint some coins right now!</h4>
    <br><br>Property sold! Redirecting to homepage now...