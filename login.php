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
                <h1><b>Login - Smart Estate</b></h1>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <!-- PHP code to handle login logic goes here -->
                    <?php

                    session_start();

                    require_once "config.php";
                    $username_err = $password_err = "";
                    // Check if form is submitted
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $username = $_POST['username']; // Get username
                        $password = $_POST['password']; // Get password

                        // Validate credentials here

                        $sql = "SELECT id, username, password FROM users WHERE username = ?";

                        if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                            //echo "Statement 1 executed";
                            mysqli_stmt_bind_param($stmt, "s", $param_username);

                        // Set parameters
                            $param_username = $username;

                        // Attempt to execute the prepared statement
                             if (mysqli_stmt_execute($stmt)) {
                                //echo "Statement 2 executed";
                            // Store result
                                mysqli_stmt_store_result($stmt);

                                // Check if username exists
                                if (mysqli_stmt_num_rows($stmt) == 1) {
                                    //echo "Statement 3 executed";
                                    // Bind result variables
                                    mysqli_stmt_bind_result($stmt, $id, $username, $retrieved_password);
                                        if (mysqli_stmt_fetch($stmt)) {
                                            //echo "Statement 4 executed";
                                            // Check if password is correct (for plain text passwords)
                                             if ($password == $retrieved_password) {
                                                //echo "Statement 5 executed";
                                                // Password is correct, so start a new session
                                                // Store data in session variables
                                                $_SESSION["loggedin"] = true;
                                                $_SESSION["id"] = $id;
                                                $_SESSION["username"] = $username;

                                                // Redirect user to upload property page
                                                header("location: upload_property.php");
                                                exit;
                                            } else {
                                                // Display an error message if password is not valid
                                                $password_err = "The password you entered was not valid.";
                                                echo "Error";
                                            }
                                         }
                                     } else {
                                         // Display an error message if username doesn't exist
                                        $username_err = "No account found with that username.";
                                    }
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }

                                // Close statement
                            mysqli_stmt_close($stmt);
                        }

                        // Close connection
                        mysqli_close($link);
                    }
                    
                    ?>

                    <form method="post" accept-charset="utf-8" autocomplete="off">
                        <div class="form-group">
                            <b><label for="name">User Name or Email</label></b>
                            <input autofocus class="form-control" id="username" name="username" required type="text">
                            <span class="text-danger"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group">
                            <b><label for="password">Password</label></b>
                            <input class="form-control" id="password" name="password" required type="password">
                            <span class="text-danger"><?php echo $password_err; ?></span>
                        </div>
                        <div class="row pt-3">
                            <div class="col-md-6">
                                <a class="float-left align-text-to-button" href="/reset_password.php">
                                    Forgot your password?
                                </a>
                                <a class="float-left align-text-to-button" href="/signup.php">
                                    Signup for an account
                                </a>
                            </div>
                            <div class="col-md-6">
                                <input class="btn btn-md btn-primary btn-outlined float-right" id="_submit" name="_submit" type="submit" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>