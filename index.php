 <?php
 // correct date
  date_default_timezone_set('Africa/Windhoek');
  
  require('classes/settings.php');
  require('classes/CRUD.php');
  require('classes/CForm.php');  
  require('classes/App.php');  
  $settings = new settings();
  
  if ( $settings->showPHPerrors ){
    ini_set('display_startup_errors',1);
    ini_set('display_errors',1);
    error_reporting(-1);  
  }  

  $app = new App( $settings );  
 ?>
 <!DOCTYPE html>
 <html lang='en'>
  <head>
   <meta charset='utf-8'>
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />   
   <title><?php echo $settings->title; ?></title>
   
   <!-- font-awesome icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel='stylesheet' href='css/style.css?t=1'>
   <style>
       .menu {
           text-align: center;
       }
       
       .menu a {
           margin: 0 10px;
       }
   </style>   
  </head>
  <body>
  
   <?php
    echo "<div class='menu'>";   
    //echo $settings->buttons_home;
    //echo "&nbsp;";
    echo $settings->buttons_map;    
    echo "&nbsp;";
    echo $settings->buttons_requests;    
    echo "&nbsp;";
    echo $settings->buttons_drivers;
    echo '</div>';
    
    //echo $settings->html_author;
    echo $settings->html_hr;    

    /* using @ in case these parameters are not set */
    $view     = @ $_GET['view'];
    $action   = @ $_GET['action'];
    $actionid = (int) @ $_GET['id'];

    switch ($view){
        case 'home':
        case 'map':
        case 'requests':
        case 'drivers':
          require("views/$view.php");
          break;
			
        default:
          // default or when no view is set
          require('views/map.php');
    }
   ?>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin="" defer></script>    
    <!-- script src="js/main.js" defer></script -->
  </body>
 </html>