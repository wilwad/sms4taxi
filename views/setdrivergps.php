<!DOCTYPE html>
 <html lang='en'>
  <head>
   <meta charset='utf-8'>
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />   
   <title>Update Driver Location</title>
  </head>
  <body>
  <?php 
    $driverid = (int) @ $_GET['id'];
    if (!$driverid){
        $driverid = 1;
        echo "id for driver was not set. id set to 1";
    }
  ?>
 <div id='location'></div>
 <p>&nbsp;</p>
 <small id='success'></small>
 <script>
 let loc = document.querySelector('#location')
 let success = document.querySelector('#success')
var oldLatLng = {lat:0, lng:0}

function getLocation() {
    if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(geoSuccess, function(){
                alert("Failed to get GPS location. Try another browser.");
            });
    } else {
            alert("Geolocation is not supported by this browser.");
    }
}

function geoSuccess(position) {
        var lat = position.coords.latitude //-22.5781481//-22.5612405;//
        var lng = position.coords.longitude // 17.0556997//17.0490881;//
        
        if (oldLatLng.lat == lat && oldLatLng.lng == lng){
            // no change
        } else {
            oldLatLng.lat = lat;
            oldLatLng.lng = lng;
            var text = `You are here - lat: ${lat}, lng: ${lng}`;
            loc.innerText = text
            //setMapCoords(lat, lng);
            let data = {
                        action: 'updategps', 
                        driverid: <?php echo $driverid; ?>, 
                        lat: lat, 
                        lng: lng
                       };
                       
            updateServer(data);
        }
}

function updateServer(data){
    let url = '../app.php';
    console.log('updateServer', data)
    
    var data_ = []
    
    for(let id in data){
        data_.push(`${id}=${data[id]}`)
    }
    data_ = data_.join('&')
    console.log(data_)
    
    document.body.style.outline = '1px solid red';

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let resp = this.responseText
            console.log('server response:', resp);
            
            if (resp.indexOf('true') > -1) success.innerText = 'Your coordinates were sent to the server.';
            
            document.body.style.outline = 'initial';
        }
    };
    success.innerText = '';
    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data_);
}

window.addEventListener('load', getLocation, false);
window.setInterval(getLocation, 60000); 
 </script>
  </body>
 </html>