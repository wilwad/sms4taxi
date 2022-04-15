<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
<style>
#map {
    width: 100%;
    height: 90vh;
}   

.main {
    display: grid;
    grid-template-columns: 1fr auto;
    grid-template-areas: "map drivers";
}

.drivers {
    display: flex;
    flex-direction: column;
    padding: 1em;
}

.drivers a {
    margin-top: 10px;
  background-color: gray;
  padding: 4px;
  border-radius: 5px;
  color: #FFF;
  text-align: center;
}
</style>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin="" defer></script>       
<div class='main'>
    <div id='map'></div>
    <div id='drivers' class='drivers'></div>
</div>

<script>
var map     = undefined;
var markers = {}
let zoom    = 16;
let loc     = document.querySelector('h2')
let drivers = document.querySelector('#drivers')

function fetchLocations(){
    let url = 'app.php';
    let data = {action: 'getdriverslocations'}
    //console.log('fetchLocations', data)
    
    var data_ = []
    
    for(let id in data){
        data_.push(`${id}=${data[id]}`)
    }
    data_ = data_.join('&')
    //console.log(data_)
    
    // we are busy
    document.body.style.outline = '2px solid red';

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let resp = this.responseText
            //console.log('server response:', resp);
            
            if ( resp.indexOf('error') > -1){
                // failure
            } else {
                drivers.innerHTML = '';
                var li = ''
                console.log(resp)
                let data = JSON.parse(resp)
                //console.log(data)
                for(let itm of data ){
                    let lat = itm.latlng.split(',')[0]
                    let lng = itm.latlng.split(',')[1]
                    let drivername = `<b>${itm.name}</b> <BR> ${itm.lastupdate}`
                    li += `<a href='#' onClick='panToDriver(${itm.driver_id}); return false;'>${itm.name}</a>`;
                    setMapCoords(itm.driver_id, lat, lng, drivername)
                }
                drivers.innerHTML = li;
            }
            
            // no longer busy
            document.body.style.outline = 'initial';
        }
    };

    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data_);
}

function panToDriver(driverId){
        let marker = markers[driverId];
        if (!marker) return;
       map.flyTo(marker.getLatLng(), zoom)
}

function setMapCoords(id, lat, lng, name){
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
                //console.log('Marker updated')
                markers[id].setLatLng(newLatLng);
                markers[id].setTooltipContent(name)
            }
        }
}	

window.addEventListener('load', fetchLocations, false);
window.setInterval(fetchLocations, 60000);
</script>