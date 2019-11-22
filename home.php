<?php
// Include necessary file
include_once './dbconn.php';

// // Check if user is not logged in
// if (!$user->is_logged_in()) {
//     $user->redirect('login.php');
// }

try {
    // Define query to select values from the users table
    $sql = "SELECT * FROM users WHERE user_id=:user_id";

    // Prepare the statement
    $query = $db_conn->prepare($sql);

    // Bind the parameters
    $query->bindParam(':user_id', $_SESSION['user_session']);

    // Execute the query
    $query->execute();

    // Return row as an array indexed by both column name
    $returned_row = $query->fetch(PDO::FETCH_ASSOC);


    //echo"<script>console.log(".json_encode($sql).")</script>";
    //echo"<script>console.log('estoy en home.php')</script>";
} catch (PDOException $e) {
    array_push($errors, $e->getMessage());
}

if (isset($_GET['logout']) && ($_GET['logout'] == 'true')) {
    $user->log_out();
    $user->redirect('login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    
    <!--<link rel="stylesheet" href="css/style.css">-->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web | Home</title>
</head>
<body>
<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="company.html">Companies</a></li>
            <li class="breadcrumb-item"><a href="route.html">Routes</a></li>
            <li class="breadcrumb-item"><a href="queries.html">Queries</a></li>
            <li class="breadcrumb-item"><a href="companyLog.html">Companies Log</a></li>
            <li class="breadcrumb-item"><a href="routeLog.html">Routes Log</a></li>
            <li class="breadcrumb-item"><a href="pointLog.html">Point Log</a></li>
            <li class="breadcrumb-item"><a href="costLog.html">Cost Log</a></li>
            <li class="breadcrumb-item"><a href="password.php">Account</a></li>
            <li class="breadcrumb-item"><a href="?logout=true">Log Out</a></li>
    </ol>
<h1>Home</h1>

<?php if (count($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $error):
            echo $error; endforeach
        ?>
    </ul>
<?php endif ?>

<p>Welcome  <?= $returned_row['user_name']; ?> </p>
</body>
</html>