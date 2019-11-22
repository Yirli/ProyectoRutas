<?php
// Database details
$db_server   = 'localhost';
$db_userdescription = 'root';
$db_password = '';
$db_description     = 'proyectoRutas';

session_start();

// Get job (and id)
$job = '';
$id  = '';
if (isset($_GET['job'])){
  $job = $_GET['job'];
  if ($job == 'get_routes' ||
      $job == 'get_route'   ||
      $job == 'add_route'   ||
      $job == 'edit_route'  ||
      $job == 'fill_companySelect'||
      $job == 'get_companuId'||
      $job == 'delete_route'){
    if (isset($_GET['id'])){
      $id = $_GET['id'];
      if (!is_numeric($id)){
        $id = '';
      }
    }
  } else {
    $job = '';
  }
}

// Prepare array
$mysql_data = array();

// Valid job found
if ($job != ''){
  
  // Connect to database
  $db_connection = mysqli_connect($db_server, $db_userdescription, $db_password, $db_description);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
  }
  
  // Execute job
  if ($job == 'get_routes'){
    
    // Get routes
    $query = "SELECT r.route_id, r.description, r.duration, r.cost, r.startHour, r.endHour,r.handicap, c.name
     FROM Ruta r, Company c WHERE c.company_id = r.company_id and r.isActive=1 ORDER BY route_id";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {
      $result  = 'success';
      $message = 'query success';
      while ($route = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        if(isset($_SESSION['user_session'])){
          $functions .= '<li class="function_edit"><a data-id="'   . $route['route_id'] . '" data-description="' . $route['description'] . '"><span>Edit</span></a></li>';
          $functions .= '<li class="function_delete"><a data-id="' . $route['route_id'] . '" data-description="' . $route['description'] . '"><span>Delete</span></a></li>';
        }
        $functions .= '</ul></div>';

          $mysql_data[] = array(
          "route_id"             => $route['route_id'],
          "description"          => $route['description'],
          "duration"             => $route['duration'],
          "cost"                 => $route['cost'],
          "startHour"            => $route['startHour'],
          "endHour"              => $route['endHour'],
          "handicap"             => $route['handicap'],
          "name"                 => $route['name']
          );
      }
    }
    
  } elseif ($job == 'get_route'){
    
    // Get Route
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {
      $query = "SELECT * FROM Company, Ruta WHERE route_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
        while ($route = mysqli_fetch_array($query)){
          $functions  = '<div class="function_buttons"><ul>';

          if(isset($_SESSION['user_session'])){
          $functions .= '<li class="function_edit"><a data-id="'   . $route['route_id'] . '" data-description="' . $route['description'] . '"><span>Edit</span></a></li>';
          $functions .= '<li class="function_delete"><a data-id="' . $route['route_id'] . '" data-description="' . $route['description'] . '"><span>Delete</span></a></li>';
          }
          $functions .= '</ul></div>';

          $mysql_data[] = array(
          "route_id"             => $route['route_id'],
          "description"          => $route['description'],
          "duration"             => $route['duration'],
          "cost"                 => $route['cost'],
          "startHour"            => $route['startHour'],
          "endHour"              => $route['endHour'],
          "handicap"             => $route['handicap'],
          "name"                 => $route['name']
          );
        }
      }
    }
  
  } elseif ($job == 'add_route'){
    

    // Add Route
    $query = "INSERT INTO Ruta SET ";
    if (isset($_GET['description']))    { $query .= "description = '" . mysqli_real_escape_string($db_connection, $_GET['description']) . "', "; }
    if (isset($_GET['duration']))       { $query .= "duration   = '" . mysqli_real_escape_string($db_connection, $_GET['duration'])   . "', "; }
    if (isset($_GET['cost']))           { $query .= "cost      = '" . mysqli_real_escape_string($db_connection, $_GET['cost'])      . "', "; }
    $query .= "isActive = '1', ";
    if (isset($_GET['startHour']))      { $query .= "startHour  = '" . mysqli_real_escape_string($db_connection, $_GET['startHour'])  . "', "; }
    if (isset($_GET['endHour']))        { $query .= "endHour    = '" . mysqli_real_escape_string($db_connection, $_GET['endHour'])    . "', "; }
    if (isset($_GET['handicap']))       { $query .= "handicap   = '" . mysqli_real_escape_string($db_connection, $_GET['handicap'])   . "', "; }
    if (isset($_GET['company_id']))     { $query .= "company_id   = '" . mysqli_real_escape_string($db_connection, $_GET['company_id']). "'";  }

    $query = mysqli_query($db_connection, $query) or trigger_error("Error: " .mysqli_error($db_connection));
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {

       $sesion = $_SESSION['user_session'];
       $sql = "UPDATE RouteLog SET user_id=".$sesion." WHERE log_id = (select max(log_id) from RouteLog)";
       //echo"<script>console.log(".json_encode($sql).")</script>";
       $sql = mysqli_query($db_connection, $sql) or trigger_error(mysqli_error($db_connection));

      $result  = 'success';
      $message = 'query success';
    }
    
  
  } elseif ($job == 'edit_route'){
    
   // Edit Route
   if ($id == ''){
    $result  = 'error';
    $message = 'id missing';
  } else {
    $query = "UPDATE Ruta SET ";
    if (isset($_GET['description']))    { $query .= "description = '" . mysqli_real_escape_string($db_connection, $_GET['description']) . "', "; }
    if (isset($_GET['duration']))       { $query .= "duration   = '" . mysqli_real_escape_string($db_connection, $_GET['duration'])   . "', "; }
    if (isset($_GET['cost']))           { $query .= "cost      = '" . mysqli_real_escape_string($db_connection, $_GET['cost'])      . "', "; }
    $query .= "isActive = '1', ";
    if (isset($_GET['startHour']))      { $query .= "startHour  = '" . mysqli_real_escape_string($db_connection, $_GET['startHour'])  . "', "; }
    if (isset($_GET['endHour']))        { $query .= "endHour    = '" . mysqli_real_escape_string($db_connection, $_GET['endHour'])    . "', "; }
    if (isset($_GET['handicap']))       { $query .= "handicap   = '" . mysqli_real_escape_string($db_connection, $_GET['handicap'])   . "', "; }
    if (isset($_GET['company_id']))     { $query .= "company_id   = '" . mysqli_real_escape_string($db_connection, $_GET['company_id']). "'";  }
    $query .= "WHERE route_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
    $query = mysqli_query($db_connection, $query) or trigger_error("Error: " .mysqli_error($db_connection));

    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {

      $sesion = $_SESSION['user_session'];
      $sql = "UPDATE RouteLog SET user_id=".$sesion." WHERE log_id = (select max(log_id) from RouteLog)";
      //echo"<script>console.log(".json_encode($sql).")</script>";
      $sql = mysqli_query($db_connection, $sql) or trigger_error(mysqli_error($db_connection));

      $result  = 'success';
      $message = 'query success';
    }
  }
    
  } elseif ($job == 'delete_route'){
  
    // Delete Route
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {
      $query = "UPDATE Ruta SET isActive = 0 WHERE route_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
      }
    }
  
  }else if($job =='fill_companySelect'){

      $query = "SELECT company_id,name FROM Company";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
        while ($route = mysqli_fetch_array($query)){
          $mysql_data[] = array(
          "company_id"    => $route['company_id'],
          "name"          => $route['name']
          );
        }
      }
    

  }
  
  // Close database connection
  mysqli_close($db_connection);

}

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