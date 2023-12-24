<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Estate</title>
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
                <h1><b>Signup - Smart Estate</b></h1>
            </div>
        </div>
        <p>Under Construction! Right now only admins can add users on the portal</p>
</body>
</html>
