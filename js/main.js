let loc = document.querySelector('#location')
var map = undefined
var marker1 = undefined
var oldLatLng = {lat:0, lng:0}

function getLocation() {
    if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
    } else {
            alert("Geolocation is not supported by this browser.");
    }
}

function geoError() {
    alert("Failed to get location.");
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
        }
}

function updateServer(data){
    let url = '../app/updatedb.php';
    fetch(url, {
        method: 'POST',
        mode:'cors',
        //headers:{'content-type':'application/json'},
        body: data
    })
    .then((response)=>{
        return response.text()
    })
    .then((data)=>{
        console.log('server response: ',data);
    });
}

function setMapCoords(lat, lng){
        if (!map){
            map = L.map('map', {
            center: [lat, lng],
            zoom: 22
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);
            marker1 = L.marker([lat, lng]).addTo(map); 
            
            updateServer(`${lat},${lng}`);
        } else {
            
            var newLatLng = new L.LatLng(lat, lng);
            var oldLatLng = marker1.getLatLng();
            
            if (newLatLng.lat == oldLatLng.lat &&
                newLatLng.lng == oldLatLng.lng){
                //console.log('No change in position')
            } else {
                console.log(new Date().toLocaleString(), 'Updated')
                document.querySelector('h2').innerText = `You are here: ${lat},${lng}`
                marker1.setLatLng(newLatLng);   
                updateServer(`${lat},${lng}`);
            }
        }
}	

window.addEventListener('load', getLocation, false);
window.setInterval(getLocation, 60000); 
