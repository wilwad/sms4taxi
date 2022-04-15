 <?php
 /*
 * This handles POST requests from AJAX
 * 
 * parameters:
 *      action: updategps, driverid: integer, lat: GPS_lat, lng: GPST_lng
 *      action: getdriverlocation, driverid: integer
 *      action: getdriverslocations, no_parameters expected
 * 
 */

  // corret date
  date_default_timezone_set('Africa/Windhoek');
  
  //if ( $settings->showPHPerrors ){
    ini_set('display_startup_errors',1);
    ini_set('display_errors',1);
    error_reporting(-1);  
  //}

    if (!empty($_POST['action'])){    
        $action = $_POST['action'];
        
        require('classes/settings.php');
        require('classes/CRUD.php');
        require('classes/App.php');
        $settings = new settings();
        $app = new App( $settings );  
        
        switch ($action){
            case 'updategps':
                // writes lat,lng,driverid,lastupdate to db
                // returns 'true' or error
                $driverid = (int) $_POST['driverid'];
                
                // sanity check
                $ret = $app->getDriver($driverid);
                if (!$ret['ok']) die('error: invalid driver');
                
                $lat = $_POST['lat'];
                $lng = $_POST['lng'];
                $date = date('Y-m-d H:i:s');
                $arr = ['latlng'=>"$lat,$lng", 'lastupdate'=>$date];
                $ret = $app->updateDriverLocation($driverid, $arr);
                echo $ret['ok'] ? 'true' : 'error: '.$ret['error'];
                break;
                
            case 'getdriverlocation':
                // returns lat,lng,driverid,lastupdate from db
                // returns 'driverid,latlng,lastupdate,drivername' or error
                $driverid = (int) $_POST['driverid'];
                
                // sanity check
                $ret = $app->getDriver($driverid);
                if (!$ret['ok']) die('error: invalid driver');
                
                $name = $ret['data']['rows'][0]['name'];                
                $ret = $app->getDriverLocation($driverid);
                $latlng = $ret['data']['rows'][0]['latlng'];
                $lastupdate = $ret['data']['rows'][0]['lastupdate'];                
                echo $ret['ok'] ? "$driverid,$latlng,$lastupdate,$name" : 'error: '.$ret['error'];
                break;
                
            case 'getdriverslocations':
                $ret = $app->getDriversLocations();
                if (!$ret['ok']){
                    die( 'error: '.$ret['error']);
                }

                // sanity check
                if (!$ret['total_rows']) die('error: no data');

                $json = [];
                foreach($ret['data']['rows'] as $row){
                    $json[] = $row;
                }
                echo json_encode($json);
                break;
                
            default:
                echo 'error: unhandled action';
                
        }
    } else {
        echo 'error: action is empty';
    }
