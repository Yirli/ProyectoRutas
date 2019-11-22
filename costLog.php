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
    $query = "SELECT r.description as ruta, c.name as company, cl.old_cost, cl.new_cost, cl.fecha FROM CostLog cl, Ruta r, Company c
    WHERE c.company_id = cl.company_id and r.route_id = cl.route_id";
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
            "ruta"              => $route['ruta'],
            "company"           => $route['company'],
            "old_cost"          => $route['old_cost'],
            "new_cost"          => $route['new_cost'],
            "fecha"             => $route['fecha']
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