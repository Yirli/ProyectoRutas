<?php
// Database details
$db_server   = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name     = 'proyectoRutas';



// Prepare array
$mysql_data = array();

  
  // Connect to database
  $db_connection = mysqli_connect($db_server, $db_username, $db_password, $db_name);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
  }
  
    // Get companies
    $query = "SELECT l.log_id,c.name, l.action, l.time, u.user_name FROM CompanyLog l, Company c, users u where c.company_id=l.company_id and u.user_id = l.user_id";

    //echo"<script>console.log(".json_encode($query).")</script>";
    $query = mysqli_query($db_connection, $query);
    
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {
      $result  = 'success';
      $message = 'query success';
      while ($company = mysqli_fetch_array($query)){

        $mysql_data[] = array(
          "log_id"            => $company['log_id'],
          "name"              => $company['name'],
          "action"            => $company['action'],
          "time"              => $company['time'],
          "user"              => $company['user_name']
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