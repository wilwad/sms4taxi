<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
<style>
#map {
    width: 96vw;
    height: 90vh;
}   
</style>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin="" defer></script>       
<div id='map'></div>

<script>
var map = undefined;
var markers = {}

function fetchLocations(){
    console.log(new Date().toLocaleString(), 'fetchLocations')

    let url = 'app.php';
    let data = {action: 'getdriverslocations'}

    var data_ = []
    
    for(let id in data){
        data_.push(`${id}=${data[id]}`)
    }
    data_ = data_.join('&')

    // user feedback
    document.body.style.outline = '2px solid red';

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let resp = this.responseText
            //console.log('server response:', resp);
            
            if ( resp.indexOf('error') > -1){
                // failure
            } else {
                try {
                    let data = JSON.parse(resp)

                    for(let itm of data ){
                        let lat = itm.latlng.split(',')[0]
                        let lng = itm.latlng.split(',')[1]
                        let drivername = `<b>${itm.name}</b> <BR> ${itm.lastupdate}`
                        setMapCoords(itm.driver_id, lat, lng, drivername)
                    }

                } catch (e){
                    console.error('JSON parse', e.message)
                }
            }
            
            // user feedback
            document.body.style.outline = 'initial';
        }
    };

    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data_);
}

function setMapCoords(id, lat, lng, name){
        //console.log('setMapCoords', id, lat, lng, name)
        
        if (!map){
            map = L.map('map', {
            center: [lat, lng],
            zoom: 12//22
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);
        } 
        
        if (!markers[id]){
            markers[id] = L.marker([lat, lng]).addTo(map); 
            //markers[id].bindPopup(name).openPopup();   
            markers[id].bindTooltip(name,  {permanent: true, direction : 'bottom'});
            //console.log('#1')
            
        } else {
            let newLatLng = new L.LatLng(lat, lng);
            let oldLatLng = markers[id].getLatLng();
            
            if (newLatLng.lat == oldLatLng.lat &&
                newLatLng.lng == oldLatLng.lng){
                //console.log('No change in position')
            } else {
                //console.log('#1')
                //console.log('Marker updated')
                markers[id].setLatLng(newLatLng);   //setLatLng([0,0])
                //markers[id].bindTooltip(name,  {permanent: true, direction : 'bottom'});
                
                //map.panTo(newLatLng);
                //map.setCenter(newLatLng, zoom)
                //updateServer(`${lat},${lng}`);
            }
        }
        
        //console.log(markers[id].getLatLng())
}	

window.addEventListener('load', fetchLocations, false);
window.setInterval(fetchLocations, 60000);
</script>