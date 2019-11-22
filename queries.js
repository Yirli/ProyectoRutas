var filtro = "";
$(document).ready(function(){

    // On page load: datatable
    var table_routes = $('#table_routes').dataTable({
      "ajax": "route.php?job=get_routes",
      "columns": [
        { "data": "route_id" },
        { "data": "description",   "sClass": "description" },
        { "data": "duration" },
        { "data": "cost",        "sClass": "integer" },
        { "data": "startHour",    "sClass": "integer" },
        { "data": "endHour",      "sClass": "integer" },
        { "data": "handicap",     "sClass": "integer" },
        { "data": "name" }
      ],
      "aoColumnDefs": [
        { "bSortable": false, "aTargets": [-1] }
      ],
      "lengthMenu": [[5, 10, -1], [5, 10, "All"]],
      "oLanguage": {
        "oPaginate": {
          "sFirst":       " ",
          "sPrevious":    " ",
          "sNext":        " ",
          "sLast":        " ",
        },
        "sLengthMenu":    "Records per page: _MENU_",
        "sInfo":          "Total of _TOTAL_ records (showing _START_ to _END_)",
        "sInfoFiltered":  "(filtered from _MAX_ total records)"
      }
    });
  
    var table = $('#table_routes').DataTable();
  
    $('#table_routes').on('click', 'tr', function () {
      console.log( table.row( this ).data().route_id);   
      var $id = table.row(this).data().route_id;
      showRoute($id);
      
      //var data = table.row(this).data('id');
      //alert( 'You clicked on '+data.index()+ ' row' );
        } );

        $('#table_routes').on('search.dt', function() {
            filtro = $('.dataTables_filter input').val();
            console.log(filtro); // <-- the value
        }); 

var markers = [];

var map = L.map('map').setView([10.0146108,-84.1223332],10);
	L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{
	attribution:'&copy; <a href="https://www.openstreetmap.org/copyright'
    }).addTo(map);	

        //number of filtered rows
        console.log(table.rows( { filter : 'applied'} ).nodes().length);
        //filtered rows data as arrays
        console.log(table.rows( { filter : 'applied'} ).data());                                  
        showRoutesxCompany('Lumaca');

});


function showRoute(id){
    console.log("id show route: "+id);
    $.ajax({
        type:"GET",
        url:"maps.php?job=showRoute&id="+id
    }).done(function(data){
        markers = [];
        markers = JSON.parse(data);
        console.log("markers: "+markers);

        // create custom icon
        
        var greenIcon = new L.Icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
          });
          

        var iconStyleTwo = L.icon({
            iconUrl: 'img/finishIcon.jpg'});

        new L.marker(markers[0],{icon: greenIcon}).addTo(map).on('click',editMarker);


        for(i=1;i<markers.length-1;i++){
            new L.marker(markers[i],{icon: greenIcon}).addTo(map).on('click',editMarker);
        }

        new L.marker(markers[markers.length],{icon: iconStyleTwo}).addTo(map).on('click',editMarker);


        //map.setView([markers[0].lat,markers[0].lng],15);
        paint();
        
    });
}

function showRoutesxCompany(company_name){
    console.log("Company name: "+company_name);
    $.ajax({
        type:"GET",
        url:"maps.php?job=showRoutexCompany&id="+company_name
    }).done(function(data){
        var rutas = JSON.parse(data);
        console.log(rutas.length);
        console.log(rutas);
        for(i=0;i<rutas.length;i++)
            showRoute(rutas[i].route_id);
    });
}

function showRoutesxDestiny(){
    $.ajax({
        type:"GET",
        url:"maps.php?job=showRoutexDestiny",
        data: {"latDestiny":markers[markers.length-1].lat,"lngDestiny":markers[markers.length-1].lng}
    }).done(function(data){
        var rutas = JSON.parse(data);
        console.log(rutas.length);
        console.log(rutas);
        for(i=0;i<rutas.length;i++)
            showRoute(rutas[i].route_id);
    });
}

function showRoutesxInterBusStop(){
    var json = JSON.stringify(markers);
    $.ajax({
        type:"GET",
        url:"maps.php?job=showRoutexInterBusStop",
        data: {"coords":json}
    }).done(function(data){
        var rutas = JSON.parse(data);
        console.log(rutas.length);
        console.log(rutas);
        for(i=0;i<rutas.length;i++)
            showRoute(rutas[i].route_id);
    });
}
// VARIABLES PARA SERVICIO ESPECIFICO 

var endPoint;

function showServicioEspecificoUno(){
    console.log("END POINT: "+ endPoint);
    console.log("DISTANCE: "+getDistance(currentMarker._latlng.lat,currentMarker._latlng.lng,endPoint.lat,endPoint.lng));
    if(getDistance(currentMarker._latlng.lat,currentMarker._latlng.lng,endPoint.lat,endPoint.lng) > 1 )
    showServicioEspecificoUnoParteDos(endPoint);
}
function showServicioEspecificoUnoParteDos(endPoint){
    $.ajax({
        type:"GET",
        url:"maps.php?job=showRoutexDestiny",
        data: {"latDestiny":endPoint.lat,"lngDestiny":endPoint.lng}
    }).done(function(data){ // trae las rutas que llevan a ese destino
        var paradas = JSON.parse(data);
        var distance = getDistance(currentMarker._latlng.lat,currentMarker._latlng.lng,paradas[0].lat,paradas[0].lng);
        iteracionMenor = 0;
        for(i=1;i<paradas.length;i++){ 
            nuevoMinimo = getDistance(currentMarker._latlng.lat,currentMarker._latlng.lng,paradas[i].lat,paradas[i].lng);
            if( nuevoMinimo < distance){
                distance = nuevoMinimo;
                iteracionMenor = i;
            }
        }
        showRoute(paradas[iteracionMenor].route_id); //dibuja la ruta
        setEndPoint(paradas[iteracionMenor]);
        showServicioEspecificoUno(); //envia parada inicial
    });
}
function setEndPoint(param){
    endPoint = param;
}



function paint(){
    L.Routing.control({
        waypoints: markers
    }).addTo(map);
}

function queryOne(){
    if(!filtro)
    alert("Escriba el nombre de la empresa en el campo Search y vuelva a presionar este botón");
    else
    showRoutesxCompany(filtro);
}

function queryTwo(){
    alert("Seleccione una de las rutas en la tabla y se mostrará en el mapa");
}

function queryThree(){
    if(markers.length == 1)
        showRoutesxDestiny();
    else
        alert("Debe seleccionar un destino");

}

function queryFour(){
    if(markers.length == 1)
        showRoutesxInterBusStop();
    else
    alert("Debe seleccionar una parada intermedia");
}

function queryFive(){
    if(markers.length == 1)
        showServicioEspecificoUno();
    else
        alert("Debe seleccionar su ubicacion actual y un destino");

}

function getDistance(lat1, lon1, lat2, lon2) {
	if ((lat1 == lat2) && (lon1 == lon2)) {
		return 0;
	}
	else {
		var radlat1 = Math.PI * lat1/180;
		var radlat2 = Math.PI * lat2/180;
		var theta = lon1-lon2;
		var radtheta = Math.PI * theta/180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		if (dist > 1) {
			dist = 1;
		}
		dist = Math.acos(dist);
		dist = dist * 180/Math.PI;
		dist = dist * 60 * 1.1515;
		return dist*1.609344;
	}
}

