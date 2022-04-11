<?php
/*
 * class to hold configuration data for the project
*/
class settings {
        public $title          = 'sms4taxi';     
        public $showPHPerrors  = true;
        
        // database connection
        public $database_host  = 'localhost';
        public $database_name  = 'sms4taxi';
        public $database_user  = 'sms4taxi';
        public $database_pwd   = '';

         // tables
        public $tables_drivers  = 'drivers';
        public $tables_users    = 'users';
        public $tables_driverlocations = 'driver_locations';
        public $tables_requests = 'requests';
                                
         // errors         
        public $error_norequests  = "No request(s) found";
        public $error_nodrivers   = "No driver(s) found";
        
		// buttons
        public $buttons_home      = "<a href='?view=home'>Home</a>";
        public $buttons_map       = "<a href='?view=map'>Taxi Locations</a>";
        public $buttons_drivers   = "<a href='?view=drivers'>Taxi Drivers</a>";
        public $buttons_requests  = "<a href='?view=requests'>SMS Requests</a>";
        public $buttons_newdriver = "<a href='?view=drivers&action=add' class='button'>+ Add new driver</a><BR>";
    		
		// HTML 
		public $html_author         = "<small class='float-right'>mockup created by William Sengdara &copy; 2022</small>";   
		public $html_requests_title = "<h1 class='align-center'>All SMS requests</h1>";
		public $html_drivers_title  = "<h1 class='align-center'>All taxi drivers</h1>";
        public $html_hr             = '<HR>';
        public $html_slash          = ' / ';
        public $html_p              = '<p></p>';  
        public $html_searchbox      = "<input name='term' style='padding:10px; width: 80vw' maxlength=200 placeholder='Enter something to search the list'>";
		        
        // actions
        public $html_actions_drivers = "<a href='?view=drivers&action=edit&id={driverid}'>Edit</a> /
						                <a href='#' onclick='return confirmDelete({driverid})'>Delete</a>";

		public $html_actions_requests = "<a href='?view=requests&action=details&id={requestid}'>Details</a> /
										 <a href='?view=requests&action=edit&id={requestid}'>Edit</a> /
										 <a href='#' onclick='return confirmDelete({requestid});'>Delete</a>";

        // font awesome icons
        public $icons_person = "<span class='fa fa-fw fa-user'></span>";
        public $icons_bank   = "<span class='fa fa-fw fa-bank'></span>";
        public $icons_list   = "<span class='fa fa-fw fa-list'></span>";
        public $icons_play   = "<span class='fa fa-fw fa-play'></span>";
        public $icons_link   = "<span class='fa fa-fw fa-link'></span>";
        public $icons_unlink = "<span class='fa fa-fw fa-unlink'></span>";
        public $icons_video  = "<span class='fa fa-fw fa-video'></span>";

		// SQL
        public $sql_getdrivers  = "SELECT 
                                        d.driver_id AS id,
                                        d.name,
                                        d.cellphone,
                                        (case when l.latlng is null then '' else l.latlng end) as latlng,
                                        (case when l.lastupdate is null then '' else l.lastupdate end) as lastupdate
                                    FROM
                                        drivers d
                                        LEFT   JOIN
                                        driver_locations l ON d.driver_id = l.driver_id
                                    ORDE BY d.name ASC;"; 
	
		public $sql_getdriverslocations = "SELECT 
                                                l.driver_id, d.name, l.latlng, l.lastupdate
                                            FROM
                                                `drivers` d
                                            JOIN `driver_locations` l
                                            ON
                                                d.driver_id = l.driver_id
                                            ORDER BY l.lastupdate DESC;";					
		
        public $sql_getrequests = "SELECT 
										*
									FROM
										`requests` c
									ORDER BY `entrydate` DESC;";
		
		public $sql_getpendingrequests = "SELECT *
										  FROM requests
										  WHERE driver_id <> 0;";
                									
        public $sql_getrequestsfordriver = "SELECT * 
        									FROM requests 
        									WHERE driver_id={driver_id};";												 
 }
