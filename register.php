<?php
// Include necessary file
require './dbconn.php';

// Check if user is already logged in
if ($user->is_logged_in()) {
    // Redirect logged in user to their home page
    $user->redirect('home.php');
}

// Check if register form is submitted
if (isset($_POST['register'])) {
    // Retrieve form input
    $user_name = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $user_password = trim($_POST['user_password']);
    $name = trim($_POST['name']);
    $lastName = trim($_POST['lastName']);
    $sLastName = trim($_POST['sLastName']);
    $phoneCode = trim($_POST['phoneCode']);
    $cellphone = trim($_POST['cellphone']);
    $phone = trim($_POST['phone']);

    // Check for empty and invalid inputs
    if (empty($user_name)) {
        array_push($errors, "Please enter a valid username.");
    } elseif (empty($user_email)) {
        array_push($errors, "Please enter a valid e-mail address.");
    } elseif (empty($user_password)) {
        array_push($errors, "Please enter a valid password.");
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Please enter a valid e-mail address.");
    } elseif (!$user->isValidPassword($user_password)) {
        array_push($errors, "Password is not strong enough. You have to include 8 characters, at least 1 Uppercase, 1 Lowercase and 1 number.");
    } else {
        try {
            // Define query to select matching values
            $sql = "SELECT user_name, user_email FROM users WHERE user_name=:user_name OR user_email=:user_email";

            // Prepare the statement
            $query = $db_conn->prepare($sql);

            // Bind parameters
            $query->bindParam(':user_name', $user_name);
            $query->bindParam(':user_email', $user_email);

            // Execute the query
            $query->execute();

            // Return clashes row as an array indexed by both column name
            $returned_clashes_row = $query->fetch(PDO::FETCH_ASSOC);

            // Check for user names or e-mail addresses that have already been used
            if ($returned_clashes_row['user_name'] == $user_name) {
                array_push($errors, "That username is taken. Please choose something different.");
            } elseif ($returned_clashes_row['user_email'] == $user_email) {
                array_push($errors, "That e-mail address is taken. Please choose something different.");
            } else {
                // Check if the user may be registered
                if ($user->register($user_name, $user_email, $user_password, $name, $lastName, $sLastName, $phoneCode, $cellphone, $phone)) {
                } else {
                    array_push($errors, "User successfully created");
                }
            }
        } catch (PDOException $e) {
            array_push($errors, $e->getMessage());
        }
    }
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
<?php if (count($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $error):
            echo $error; endforeach
        ?>
    </ul>
<?php endif ?>

<!-- Register -->
<h2>Register</h2>
<form action="register.php" method="POST">
    <input name="name" type="text" placeholder="Nombre">
    <input name="lastName" type="text" placeholder="Primer Apellido">
    <input name="sLastName" type="text" placeholder="Segundo Apellido">
    <input name="user_name" type="text" placeholder="Nombre de Usuario">
    <input name="user_email" type="text" placeholder="Email">
    <input name="phoneCode" type="text" placeholder="Código de Área">
    <input name="cellphone" type="text" placeholder="Celular">
    <input name="phone" type="text" placeholder="Telefono">
    <input name="user_password" type="password" placeholder="Contraseña">
    <input type="submit" name="register" value="Register">
</form>
</body>

</html>