
	var markers = [];
	var currentMarker = null;

	var map = L.map('map');
	L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{
	attribution:'&copy; <a href="https://www.openstreetmap.org/copyright'
	}).addTo(map);	

	map.locate({
		setView:true,
		maxZoom:17
	});

	function onLocationFound(e) {
		var radius = e.accuracy;
	
		currentMarker = L.marker(e.latlng).addTo(map).on('click',editMarkerCurrentLocation)
			.bindPopup("Current location ").openPopup();

		
	}
	
	map.on('locationfound', onLocationFound);


	map.on('click', function(e) {
		markers.push(e.latlng);
		endPoint = e.latlng;
		var marker = new L.marker(markers[markers.length-1]).addTo(map).on('click',editMarker);
	});

	function paint(id){
		var colores = ['#FF0099','#0000FF','#00CC00','#00FFFF','#9900CC','#FF6600','#FFCC33','#006633','#00CCFF','#660033','#6DC36D','#FF689D','#23BAC4','#024A86','#109DFA'];
		L.Routing.control({
			waypoints: markers,
			lineOptions:{styles: [{color: colores[id],opacity:1,weight:8}]}
		}).addTo(map);
		// routeControl.on('routesfound', function(e) {
		// 	var routes = e.routes;
		// 	var summary = routes[0].summary;
		// 	// alert distance and time in km and minutes
		// 	alert('Total distance is ' + summary.totalDistance / 1000 + ' km and total time is ' + Math.round(summary.totalTime % 3600 / 60) + ' minutes');
		//  });
	}

	function paintQueries(points){
		console.log("JORDAN");
		L.Routing.control({
			waypoints: points,
			lineOptions:{styles: [{color: 'blue',opacity:0.15,weight:9}]}
		}).addTo(map);
	}


	function __ajax(url, data){
		var ajax = $.ajax({
			"method": "get",
			"url": url,
			"data": data
		});
		return ajax;
	}

	function createRoute(){
		var json = JSON.stringify(markers);
		__ajax("maps.php?job=createRoute",{"coords": json});
	}

	function editRoute(id){4
		var json = JSON.stringify(markers);
		__ajax("maps.php?job=editRoute&id="+id,{"coords": json});
	}

	function showRoute(id){
		console.log("id show route: "+id);
		$.ajax({
			type:"GET",
			url:"maps.php?job=showRoute&id="+id
		}).done(function(data){
			markers = JSON.parse(data);
			console.log("markers: "+markers);
			
			var LeafIcon = L.Icon.extend({
				options: {
				   iconSize:     [25, 35],
				   shadowSize:   [5, 6],
				   iconAnchor:   [10, 20],
				   shadowAnchor: [5, 6],
				   popupAnchor:  [-3, -76]
				}
			});

			var greenIcon = new LeafIcon({
				iconUrl: 'inicio.png',
				shadowUrl: 'http://leafletjs.com/examples/custom-icons/leaf-shadow.png'
			})

			var redIcon = new LeafIcon({
				iconUrl: 'final.png',
				shadowUrl: 'http://leafletjs.com/examples/custom-icons/leaf-shadow.png'
			})

			//new L.marker(markers[0],{icon: greenIcon}).addTo(map).on('click',editMarker);


			for(i=0;i<markers.length;i++){
				new L.marker(markers[i]).addTo(map).on('click',editMarker);
			}

			//new L.marker(markers[markers.length-1],{icon: redIcon}).addTo(map).on('click',editMarker);

			map.setView([markers[0].lat,markers[0].lng],12);
			paint(id-1);
		});
	}

	function showOfficeLocation(id){
		console.log("id: "+id);
		$.ajax({
			type:"GET",
			url:"maps.php?job=showOfficeLocation&id="+id
		}).done(function(data){
			markers = JSON.parse(data);
			for(i=0;i<markers.length;i++){
				new L.marker(markers[i]).addTo(map).on('click',editMarker);
			}
			map.setView([markers[0].lat,markers[0].lng],15);
		});
	}

	function updateRoute(id){
		console.log(id);		
		$.ajax({
			type:"GET",
			url:"maps.php?job=deleteBusStop&id="+id
		}).done(function(data){
			editRoute(id);
		});

	}

	function editMarker(e){
		map.removeLayer(this);
		var editMarkers = [];
		for(i=0;i<markers.length;i++){
			var marker = markers.pop();
			if(marker != this)
			editMarkers.push(marker);
		}
		markers = editMarkers;
	}

	function editMarkerCurrentLocation(e){
		map.removeLayer(this);
	}

	function addPointX(){
		var pointName = $("#pointName").val();
		var pointDescription = $("#pointDescription").val();
		var marker = markers.pop();
		var lat = marker.lat;
		var lng = marker.lng;
		__ajax("maps.php?job=addPoint",{"pointName": pointName, "pointDescription":pointDescription, "lat":lat, "lng":lng});
		$("#pointName").val("");
		$("#pointDescription").val("");
	}

	
