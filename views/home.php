 <div id='location'></div>
 <p>&nbsp;</p>
 <small>Your coordinates were sent to the server. <a href='?view=map'>View on Map</a></small>
 <script>
 let loc = document.querySelector('#location')
var map = undefined
var marker1 = undefined
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
                        driverid: 1, 
                        lat: lat, 
                        lng: lng
                       };
                       
            updateServer(data);
        }
}

function updateServer(data){
    let url = 'app.php';
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
            console.log('server response:',this.responseText);
            document.body.style.outline = 'initial';
        }
    };

    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data_);
}

window.addEventListener('load', getLocation, false);
window.setInterval(getLocation, 60000); 
 </script>
