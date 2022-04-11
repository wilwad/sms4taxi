<?php 
 /**
  * Class specific to a specific process
  */
 class App {
    private $settings;
    public $crud;  // database CRUD operations
    
    // class constructor 
    public function __construct($appsettings){
            $this->crud = new CRUD( $appsettings->database_host, 
                                    $appsettings->database_user, 
                                    $appsettings->database_pwd, 
                                    $appsettings->database_name );
                                    
            $this->settings = $appsettings;
    }
    
    // handling drivers
    public function getDrivers(){
            return $this->crud->readSQL($this->settings->sql_getdrivers);
    }
    public function getDriver($id){
            return $this->crud->read($this->settings->tables_drivers, 'driver_id', $id);
    }
    public function getDriverLocation($id){
            return $this->crud->read($this->settings->tables_driverlocations, 'driver_id', $id);
    }    
    public function createDriver($postdata){
            return $this->crud->create( $this->settings->tables_drivers, $postdata);
    }
    public function updateDriver($id, $postdata){
            return $this->crud->update( $this->settings->tables_drivers, $postdata, 'driver_id', $id);
    }
    public function updateDriverLocation($id, $postdata){
            // we only ever have 1 entry for a driver in driver_locations
            // if not exists, create else do an update
            $ret = $this->crud->read( $this->settings->tables_driverlocations, 'driver_id', $id);
            if (!$ret['ok']){
                $data = [
                         'driver_id'=>$id, 
                         'lastupdate'=>$postdata['lastupdate'], 
                         'latlng'=>$postdata['latlng'] 
                        ];
                return  $this->crud->create($this->settings->tables_driverlocations, $data);
            }
            
            return $this->crud->update( $this->settings->tables_driverlocations, $postdata, 'driver_id', $id);
    }    
    public function getDriversLocations(){
            return $this->crud->readSQL($this->settings->sql_getdriverslocations);
    }    
    public function deleteDriver($id){
            return $this->crud->delete($this->settings->tables_drivers, 'driver_id', $id);
    }    
    public function getRequestsForDriver($id){
            $sql = $this->settings->sql_getRequestsForDriver;
            $sql = str_replace('{driver_id}', $id, $sql);             
            return $this->crud->readSQL($sql);
    }   
    
    // handling requests
    public function getRequests(){
            return $this->crud->readSQL($this->settings->sql_getrequests);
    }
    public function createRequest($postdata){
            return $this->crud->create( $this->settings->tables_requests, $postdata);
    }
    public function updateRequest($id, $postdata){
            return $this->crud->update( $this->settings->tables_requests, $postdata, 'request_id', $id);
    }
    public function deleteRequest($id){
            return $this->crud->delete($this->settings->tables_requests, 'request_id', $id);
    } 
            
 } // class App end 