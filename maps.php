<?php
$job = 'VACIO';
$id = 'VACIO';
if (isset($_GET['job'])){
  $job = $_GET['job'];
}

// Connect to database
$db_server   = 'localhost';
$db_userdescription = 'root';
$db_password = '';
$db_description     = 'proyectoRutas';
$db_connection = mysqli_connect($db_server, $db_userdescription, $db_password, $db_description);
 if (mysqli_connect_errno()){
   $result  = 'error';
   $message = 'Failed to connect to database: ' . mysqli_connect_error();
   $job     = '';
 }

//echo "<script>console.log('Debug Objects: " . $job . "' );</script>";

if($job == 'createRoute'){
  $query = "SELECT count(*) from Ruta";
  
    $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
      } else {
        $result  = 'success';
        $message = 'query success';
      }
      $id = mysqli_fetch_array($query);
      $id = $id['count(*)'];
      //echo "<script>console.log('Id Ruta: " . json_encode($id) . "' );</script>";
    //echo "<script>console.log('Id Ruta: " . $id . "' );</script>";

  $coords = json_decode( $_GET["coords"]);
  var_dump($coords);

  $conexion = new PDO('mysql:host=localhost;dbname=proyectoRutas', 'root', '');
    
    $sql = "INSERT INTO BusStop(route_id,lat,lng) VALUES(?,?,?);";
    $statement = $conexion->prepare($sql);
    $resp = false;
    foreach($coords as $coord){
      $statement->bindParam(1, $id, PDO::PARAM_INT);
      $statement->bindParam(2, $coord->{"lat"}, PDO::PARAM_STR);
      $statement->bindParam(3, $coord->{"lng"}, PDO::PARAM_STR);
      $resp = $statement->execute();
    }
    
}

elseif($job == 'editRoute'){

  if (isset($_GET['id']))
    $id = $_GET['id'];

  $coords = json_decode( $_GET["coords"]);
  var_dump($coords);

  $conexion = new PDO('mysql:host=localhost;dbname=proyectoRutas', 'root', '');
    
    $sql = "INSERT INTO BusStop(route_id,lat,lng) VALUES(?,?,?);";
    $statement = $conexion->prepare($sql);
    $resp = false;
    foreach($coords as $coord){
      $statement->bindParam(1, $id, PDO::PARAM_INT);
      $statement->bindParam(2, $coord->{"lat"}, PDO::PARAM_STR);
      $statement->bindParam(3, $coord->{"lng"}, PDO::PARAM_STR);
      $resp = $statement->execute();
    }
}

elseif ($job == 'showRoute'){
  if (isset($_GET['id']))
    $id = $_GET['id'];
    //echo "<script>console.log('Id ruta: " . $id . "' );</script>";

    $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
    if (mysqli_connect_errno())
      echo 'Failed to connect to database: ' . mysqli_connect_error();
    
    $sql = "SELECT lat,lng FROM BusStop WHERE route_id='$id'";

    $result = mysqli_query($conexion,$sql);

    $coords = array();

    while($data =  mysqli_fetch_assoc($result))
        $coords[] = $data;
    
    echo json_encode($coords);
}

elseif ($job == 'showRoutexCompany'){
  if (isset($_GET['id']))
  $companyName = $_GET['id'];

  $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
  if (mysqli_connect_errno())
    echo 'Failed to connect to database: ' . mysqli_connect_error();
  
  $sql = "SELECT company_id FROM Company WHERE name='$companyName'";

  $result = mysqli_query($conexion,$sql);

  //$companyId = mysqli_fetch_assoc($result);

  $companyId = mysqli_fetch_array($result);
      
  $companyId = $companyId['company_id'];

  $sql = "SELECT route_id FROM Ruta WHERE company_id='$companyId'";

  $result = mysqli_query($conexion,$sql);

  $rutas = array();

    while($data =  mysqli_fetch_assoc($result))
        $rutas[] = $data;

    echo json_encode($rutas);
}

elseif ($job == 'showRoutexDestiny'){
  $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
  if (mysqli_connect_errno())
    echo 'Failed to connect to database: ' . mysqli_connect_error();
    
    $latMarker = $_GET["latDestiny"];
    $lngMarker=  $_GET["lngDestiny"];
    
    $sql = "SELECT count(*) FROM Ruta";
    $result = mysqli_query($conexion,$sql);
    $cantRutas = mysqli_fetch_array($result);
    $cantRutas = $cantRutas['count(*)'];
    $resultadoRutas = array();

    for($i = 1; $i<=$cantRutas; $i++){
      $sql = "SELECT * FROM BusStop WHERE busStop_id = (SELECT MAX(busStop_id) from BusStop WHERE route_id='$i')";
      $result = mysqli_query($conexion,$sql);
      $data= mysqli_fetch_array($result);
      $latBD = $data['lat'];
      $lngBD = $data['lng'];
      $rutaID = $data['route_id'];
      $flagLat = $latBD - 0.001 <= $latMarker or $latMarker <= $latBD + 0.001;
      $flagLng = $lngBD - 0.001 <= $lngMarker or $lngMarker <= $lngBD + 0.001;
      if($flagLat and $flagLng){
      $sql = "SELECT * FROM BusStop WHERE busStop_id = (SELECT MIN(busStop_id) from BusStop WHERE route_id='$i')";
      $result = mysqli_query($conexion,$sql);
      $data= mysqli_fetch_array($result);
      array_push($resultadoRutas,$data);
      }
    }
    echo json_encode($resultadoRutas);
}
elseif($job == 'showServicioEspecificoUno'){
  
}
else if($job == 'addPoint'){
  $pointName =  $_GET["pointName"];
  $pointDescription =  $_GET["pointDescription"];
  $lat =  $_GET["lat"];
  $lng =  $_GET["lng"];

  // echo "<script>console.log('pointName: " . $pointName . "' );</script>";
  // echo "<script>console.log('pointDescription: " . $pointDescription . "' );</script>";
  // echo "<script>console.log('lat: " . $lat . "' );</script>";
  // echo "<script>console.log('lng: " . $lng . "' );</script>";

  $conexion = new PDO('mysql:host=localhost;dbname=proyectoRutas', 'root', '');
    
    $sql = "INSERT INTO PointLog(pointName,pointDescription,lat,lng) VALUES(?,?,?,?);";
    $statement = $conexion->prepare($sql);
   
      $statement->bindParam(1, $pointName, PDO::PARAM_STR);
      $statement->bindParam(2, $pointDescription, PDO::PARAM_STR);
      $statement->bindParam(3, $lat, PDO::PARAM_STR);
      $statement->bindParam(4, $lng, PDO::PARAM_STR);
      $resp = $statement->execute();
}


elseif ($job == 'showRoutexInterBusStop'){
  $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
  if (mysqli_connect_errno())
    echo 'Failed to connect to database: ' . mysqli_connect_error();
  
    $coords = json_decode( $_GET["coords"]);
    //var_dump($coords);

    $lat = 0;
    $lng=  0;

    foreach($coords as $coord){
    $lat= $coord->{"lat"};
    $lng= $coord->{"lng"};
    }

    $lat = round($lat,2);
    $lng = round($lng,2);
   
    $sql = "SELECT route_id FROM BusStop WHERE ROUND(lat,2)='$lat' AND ROUND(lng,2)='$lng'";
    $result = mysqli_query($conexion,$sql);

  $rutas = array();

    while($data =  mysqli_fetch_assoc($result))
        $rutas[] = $data;

    echo json_encode($rutas);
}

elseif ($job == 'showOfficeLocation'){
  if (isset($_GET['id']))
    $id = $_GET['id'];
    //echo "<script>console.log('Id ruta: " . $id . "' );</script>";

    $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
    if (mysqli_connect_errno())
      echo 'Failed to connect to database: ' . mysqli_connect_error();
    
    $sql = "SELECT lat,lng FROM Company WHERE company_id='$id'";

    $result = mysqli_query($conexion,$sql);

    $coords = array();

    while($data =  mysqli_fetch_assoc($result))
        $coords[] = $data;
    
    echo json_encode($coords);
}

elseif ($job == 'deleteBusStop'){
  if (isset($_GET['id']))
    $id = $_GET['id'];
    //echo "<script>console.log('Id ruta: " . $id . "' );</script>";

    $conexion = mysqli_connect('localhost', 'root', '', 'proyectoRutas');
    if (mysqli_connect_errno())
      echo 'Failed to connect to database: ' . mysqli_connect_error();
    
    $sql = "DELETE FROM BusStop WHERE route_id='$id'";

    $result = mysqli_query($conexion,$sql);
}

  
?>