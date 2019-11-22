<?php
// Database details
$db_server   = 'localhost';
$db_userdescription = 'root';
$db_password = '';
$db_description     = 'proyectoRutas';


// Prepare array
$mysql_data = array();

  
  // Connect to database
  $db_connection = mysqli_connect($db_server, $db_userdescription, $db_password, $db_description);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
  }
  

    // Get Log
    $query = "SELECT l.log_id,r.description, l.action, l.time, u.user_name FROM RouteLog l, Ruta r, users u where r.route_id=l.route_id and u.user_id = l.user_id";
    //echo "<script>console.log(".json_encode($query).")</script>";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {
      $result  = 'success';
      $message = 'query success';
      while ($route = mysqli_fetch_array($query)){

          $mysql_data[] = array(
            "log_id"            => $route['log_id'],
            "description"       => $route['description'],
            "action"            => $route['action'],
            "time"              => $route['time'],
            "user"              => $route['user_name']
          );
      }
    }
    
  
  // Close database connection
  mysqli_close($db_connection);



// Prepare data
$data = array(
  "result"  => $result,
  "message" => $message,
  "data"    => $mysql_data
);

// Convert PHP array to JSON array
$json_data = json_encode($data);
print $json_data;


?>