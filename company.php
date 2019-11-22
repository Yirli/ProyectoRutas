<?php
// Database details
$db_server   = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name     = 'proyectoRutas';

session_start();

// Get job (and id)
$job = '';
$id  = '';


if (isset($_GET['job'])){
  $job = $_GET['job'];
  if ($job == 'get_companies' ||
      $job == 'get_company'   ||
      $job == 'add_company'   ||
      $job == 'edit_company'  ||
      $job == 'delete_company'){
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
  $db_connection = mysqli_connect($db_server, $db_username, $db_password, $db_name);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
  }
  
  // Execute job
  if ($job == 'get_companies'){
    
    // Get companies
    $query = "SELECT * FROM Company ORDER BY name";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {
      $result  = 'success';
      $message = 'query success';
      while ($company = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        if(isset($_SESSION['user_session'])){
        $functions .= '<li class="function_edit"><a data-id="'   . $company['company_id'] . '" data-name="' . $company['name'] . '"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="' . $company['company_id'] . '" data-name="' . $company['name'] . '"><span>Delete</span></a></li>';
        }
        
        $functions .= '</ul></div>';

        $weekSchedule =  $company['startWeek']." to ".$company['endWeek'];
        $weekendSchedule =  $company['startWeekend']." to ".$company['endWeekend'];


        $mysql_data[] = array(
          "company_id"        => $company['company_id'],
          "name"              => $company['name'],
          "origin"            => $company['origin'],
          "destiny"           => $company['destiny'],
          "physicalAddress"   => $company['physicalAddress'],
          "lat"               => $company['lat'],
          "lng"               => $company['lng'],
          "contactName"       => $company['contactName'],
          "email"             => $company['email'],
          "phone"             => $company['phone'],
          "week"              => $weekSchedule,
          "weekend"           => $weekendSchedule,
          "functions"         => $functions
        );
      }
    }
    
  } elseif ($job == 'get_company'){
    
    // Get company
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {
      $query = "SELECT * FROM Company WHERE company_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
        while ($company = mysqli_fetch_array($query)){
          $functions  = '<div class="function_buttons"><ul>';
          if(isset($_SESSION['user_session'])){
          $functions .= '<li class="function_edit"><a data-id="'   . $company['company_id'] . '" data-name="' . $company['name'] . '"><span>Edit</span></a></li>';
          $functions .= '<li class="function_delete"><a data-id="' . $company['company_id'] . '" data-name="' . $company['name'] . '"><span>Delete</span></a></li>';
          }
          
          $functions .= '</ul></div>';
  
          $weekSchedule =  $company['startWeek']." to ".$company['endWeek'];
          $weekendSchedule =  $company['startWeekend']." to ".$company['endWeekend'];

          $mysql_data[] = array(
            "company_id"        => $company['company_id'],
            "name"              => $company['name'],
            "origin"            => $company['origin'],
            "destiny"           => $company['destiny'],
            "physicalAddress"   => $company['physicalAddress'],
            "lat"               => $company['lat'],
            "lng"               => $company['lng'],
            "contactName"       => $company['contactName'],
            "email"             => $company['email'],
            "phone"             => $company['phone'],
            "week"              => $weekSchedule,
            "weekend"           => $weekendSchedule,
            "functions"         => $functions
          );
        }
      }
    }
  
  } elseif ($job == 'add_company'){

    
    // Add company
    $query = "INSERT INTO Company SET ";
    if (isset($_GET['name']))              { $query .= "name = '" . mysqli_real_escape_string($db_connection, $_GET['name']) . "', "; }
    if (isset($_GET['origin']))            { $query .= "origin   = '" . mysqli_real_escape_string($db_connection, $_GET['origin'])   . "', "; }
    if (isset($_GET['destiny']))           { $query .= "destiny      = '" . mysqli_real_escape_string($db_connection, $_GET['destiny'])      . "', "; }
    if (isset($_GET['physicalAddress']))   { $query .= "physicalAddress  = '" . mysqli_real_escape_string($db_connection, $_GET['physicalAddress'])  . "', "; }
    if (isset($_GET['lat']))               { $query .= "lat    = '" . mysqli_real_escape_string($db_connection, $_GET['lat'])    . "', "; }
    if (isset($_GET['lng']))               { $query .= "lng    = '" . mysqli_real_escape_string($db_connection, $_GET['lng'])    . "', "; }
    if (isset($_GET['contactName']))       { $query .= "contactName   = '" . mysqli_real_escape_string($db_connection, $_GET['contactName'])   . "', "; }
    if (isset($_GET['email']))             { $query .= "email   = '" . mysqli_real_escape_string($db_connection, $_GET['email'])   . "', "; }
    if (isset($_GET['phone']))             { $query .= "phone   = '" . mysqli_real_escape_string($db_connection, $_GET['phone'])   . "', "; }
    if (isset($_GET['startWeek']))         { $query .= "startWeek   = '" . mysqli_real_escape_string($db_connection, $_GET['startWeek'])   . "', "; }
    if (isset($_GET['endWeek']))           { $query .= "endWeek   = '" . mysqli_real_escape_string($db_connection, $_GET['endWeek'])   . "', "; }
    if (isset($_GET['startWeekend']))      { $query .= "startWeekend   = '" . mysqli_real_escape_string($db_connection, $_GET['startWeekend'])   . "', "; }
    if (isset($_GET['endWeekend']))        { $query .= "endWeekend   = '" . mysqli_real_escape_string($db_connection, $_GET['endWeekend'])   . "'"; }
 
    $query = mysqli_query($db_connection, $query);

    if (!$query){
      $result  = 'error';
      $message = 'query error';

    } else {
      $sesion = $_SESSION['user_session'];
      //echo"<script>console.log(".json_encode($sesion).")</script>";
      $sql = "UPDATE CompanyLog SET user_id=".$sesion." WHERE log_id = (select max(log_id) from CompanyLog)";
      //echo"<script>console.log(".json_encode($sql).")</script>";
      $sql = mysqli_query($db_connection, $sql) or trigger_error(mysqli_error($db_connection));

      $result  = 'success';
      $message = 'query success';

    }
  
  } elseif ($job == 'edit_company'){
    
   // Edit company
   if ($id == ''){
    $result  = 'error';
    $message = 'id missing';
  } else {
    $query = "UPDATE Company SET ";
    if (isset($_GET['name']))              { $query .= "name = '" . mysqli_real_escape_string($db_connection, $_GET['name']) . "', "; }
    if (isset($_GET['origin']))            { $query .= "origin   = '" . mysqli_real_escape_string($db_connection, $_GET['origin'])   . "', "; }
    if (isset($_GET['destiny']))           { $query .= "destiny      = '" . mysqli_real_escape_string($db_connection, $_GET['destiny'])      . "', "; }
    if (isset($_GET['physicalAddress']))   { $query .= "physicalAddress  = '" . mysqli_real_escape_string($db_connection, $_GET['physicalAddress'])  . "', "; }
    if (isset($_GET['lat']))               { $query .= "lat    = '" . mysqli_real_escape_string($db_connection, $_GET['lat'])    . "', "; }
    if (isset($_GET['lng']))               { $query .= "lng    = '" . mysqli_real_escape_string($db_connection, $_GET['lng'])    . "', "; }
    if (isset($_GET['contactName']))       { $query .= "contactName   = '" . mysqli_real_escape_string($db_connection, $_GET['contactName'])   . "', "; }
    if (isset($_GET['email']))             { $query .= "email   = '" . mysqli_real_escape_string($db_connection, $_GET['email'])   . "', "; }
    if (isset($_GET['phone']))             { $query .= "phone   = '" . mysqli_real_escape_string($db_connection, $_GET['phone'])   . "', "; }
    if (isset($_GET['startWeek']))         { $query .= "startWeek   = '" . mysqli_real_escape_string($db_connection, $_GET['startWeek'])   . "', "; }
    if (isset($_GET['endWeek']))           { $query .= "endWeek   = '" . mysqli_real_escape_string($db_connection, $_GET['endWeek'])   . "', "; }
    if (isset($_GET['startWeekend']))      { $query .= "startWeekend   = '" . mysqli_real_escape_string($db_connection, $_GET['startWeekend'])   . "', "; }
    if (isset($_GET['endWeekend']))        { $query .= "endWeekend   = '" . mysqli_real_escape_string($db_connection, $_GET['endWeekend'])   . "'"; }
    $query .= "WHERE company_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
    $query  = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
    } else {

      $sesion = $_SESSION['user_session'];
      $sql = "UPDATE CompanyLog SET user_id=".$sesion." WHERE log_id = (select max(log_id) from CompanyLog)";
      //echo"<script>console.log(".json_encode($sql).")</script>";
      $sql = mysqli_query($db_connection, $sql) or trigger_error(mysqli_error($db_connection));

      $result  = 'success';
      $message = 'query success';
    }
  }
    
  } elseif ($job == 'delete_company'){
  
    // Delete company
    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
    } else {
      $query = "DELETE FROM Company WHERE company_id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
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