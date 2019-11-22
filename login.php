<?php
// Include necessary file
require_once('./dbconn.php');

// Check if user is already logged in
if ($user->is_logged_in()) {
    // Redirect logged in user to their home page
    $user->redirect('home.php');
}

// Check if log-in form is submitted
if (isset($_POST['log_in'])) {
    // Retrieve form input
    $user_name = trim($_POST['user_name_email']);
    $user_email = trim($_POST['user_name_email']);
    $user_password = trim($_POST['user_password']);

    echo"<script>console.log(estoy en login.php)</script>";
    // Check for empty and invalid inputs
    if (empty($user_name) || empty($user_email)) {
        array_push($errors, "Please enter a valid username or e-mail address");
    } elseif (empty($user_password)) {
        array_push($errors, "Please enter a valid password.");
    } else {
        // Check if the user may be logged in
        if ($user->login($user_name, $user_email, $user_password)) {
            // Redirect if logged in successfully
            $user->redirect('home.php');
        } else {
            array_push($errors, "Incorrect log-in credentials.");
        }
    }
}

elseif (isset($_POST['log_in_guest'])) {
    $returned_row['user_name'] = "guest";
    $user->redirect('home.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
        
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web | Login</title>
</head>

<body>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="login.php">Log In</a></li>
    <li class="breadcrumb-item"><a href="register.php">Register</a></li>
</ol>
<h1>Welcome</h1>

<?php if (count($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $error):
            echo $error; endforeach
        ?>
    </ul>
<?php endif ?>

<!-- Log in -->
<h2>Log in</h2>
<form action="login.php" method="POST">
    <input name="user_name_email" type="text" placeholder="Enter your username">
    <input name="user_password" type="password" placeholder="Enter your password">
    <input type="submit" name="log_in" value="Log in">
    <br>
    <input type="submit" name="log_in_guest" value="Continue as a guest">

</form>

</body>
</html>