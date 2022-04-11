<?php
 /*
  * handling of the requests operations
 */
 $icon_person = $settings->icons_person;
 $icon_play   = $settings->icons_play;
  			
 // when you click a Submit button on the forms
 // called during add
if (isset($_POST['add'])){
    unset($_POST['add']); // remove the submit button otherwise it will be added to the database
    // handling file uploads
    /*
    if (isset($_FILES)){        
        // we need to delete the existing file.
        // so let's get the current row
        $ret = $mydb->read('girls', 'id', $edit,1); // 1 record
        if (! $ret['ok']) die('Record not found');
        $row = $ret['data']['rows'][0]; // the record

        foreach ($_FILES as $name => $value) {
            $path = $_FILES[$name]['name'];
            $tmp  = $_FILES[$name]['tmp_name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // ignore if not an image
            //if (! getimagesize ($tmp)) continue;
            //if (!in_array(strtolower($ext), $imageeext)) continue;

            $random = $app->randomPassword(10);
            $to     = $settings->$uploaddir . "$random.$ext";

            if (move_uploaded_file( $tmp, $to)) {
                // only 1 record is returned
                $fileexisting = $row[$name];

                if (file_exists($fileexisting)){
                    @ unlink($fileexisting);
                    echo "Deleted $fileexisting";
                } 

                // value to save in the table
                $_POST[$name] = $to;
            } else {
                echo "Failed to move file $path to $to.<BR>";
            }
        }
    } */
    
    // set user_id
    $_POST['user_id'] = 1;
    
    $ret = $app->createRequest($_POST);
    if ($ret['ok']){       
        // go to the requests area
        die("<script>window.location.href='?view=requests';</script>");

    } else {
        die('Error: ' . $ret['error']);
    }
}
// called during edit
if (isset($_POST['edit'])){
    unset($_POST['edit']); // remove the submit button otherwise it will be added to the database

    // handling file uploads
    /*
    if (isset($_FILES)){        
        foreach ($_FILES as $name => $value) {
            $path = $_FILES[$name]['name'];
            $tmp  = $_FILES[$name]['tmp_name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // ignore if not an image
            //if (! getimagesize ($tmp)) continue;
            //if (!in_array(strtolower($ext), $imageeext)) continue;

            $random = $app->randomPassword(10);
            $to     = $settings->$uploaddir . "$random.$ext";

            if (move_uploaded_file( $tmp, $to)) {
                // value to save in the table
                $_POST[$name] = $to;
            } else {
                echo "Failed to move file $path to $to.<BR>";
            }
        }
    } */
    
    $ret = $app->updateRequest($actionid, $_POST);
    if ($ret['ok']){
        die("<script>window.location.href='?view=requests';</script>"); // navigate the page to this URL
    } else {
        die('Error: ' . $ret['error']);
    }    
}

// handling of ?view=requests&action=delete|edit|details|add|no_parameter
 switch ($action){
        case 'add': // add a new driver
            $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name);

            $metadata = [];
            $ret = $mydb->getTableMetadata($settings->tables_requests);
            if ($ret['ok']){
                $metadata = $ret['data'];
            } 
            
            // show a data entry form on the screen without any data
            $ret = $mydb->generateForm( $settings->tables_requests);        
            if ($ret['ok']){ 
                $body     = [];
                $ignored  = ['request_id', 'user_id', 'entrydate'];        
                $required = ['name', 'cellphone']; // these values in the array will have the required property added to the input field
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $type = $metadata[$id]['type'];
                        $size = $mydb->numbersFromString($type);                        
                        $extra = $size ? "maxlength=$size" : '';
                        
                        $data = "<input type='text' $extra $req name='$id' id='$id' value=\"$v\">";
                        /* handle some columns */
                        switch ($id){
                            case 'photo':
                            case 'picture':
                                $data = "<input $req type='file' accept='.jpg,.jpeg,.png' name='$id' id='$id'>";
                                break;
                                
                            case 'filename':
                                $data = "<input $req type='file' accept='.txt,.pdf,.doc' name='$id' id='$id'>";
                                break;
                                
                            case 'active':
                                $values = '';
                                $opts = [1=>'Active', 0=>'Disabled'];
                                foreach($opts as $id0=>$val0){
                                	$selected = $id0 == 1 ? 'selected' : '';
                                    $values .= "<option value='$id0' $selected>$val0</option>";
                                }
                                
                                $data = "<select $req name='$id' id='$id'>
                                          $values
                                         </select>";
                                break;
                                
                            case 'category_id*':
                                $ret = $mydb->getTableColumnData('categories', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id=>$val){
                                        $values .= "<option value='$id'>$val</option>";
                                    }
                                    
                                    $data = "<select $req id='$id'>$values</select>";
                                }           			
                                break;
                                
                            case 'email':
                            case 'email_address': 
                                $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                                break;
                                
                            default:
                                break;
                        } /* handle some columns */  
                        
                        if ($id == 'email_address'){
                            $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                        }
                        
                    $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
                }        
                $body = implode('', $body);
                
                echo $settings->html_p;
                echo "<form method='post'>
                    <fieldset>
                        <legend>driver Details</legend>
                        $body
                        <input type='submit' name='add'value='Add Record'>
                    </fieldset>
                    </form>
                    <p>&nbsp;</p>
                    ";
        
            } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
            }   
            break;
        
		 case 'edit': // edit the driver
		    $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name );
		                    
            $metadata = [];
            $ret = $mydb->getTableMetadata($settings->tables_requests);
            if ($ret['ok']){
                $metadata = $ret['data'];
            } 
            
            // show a data editing form on the screen with data for driver with request_id = $actionid
		    $ret = $mydb->generateForm( $settings->tables_requests,'request_id', $actionid);
		    
		    if ($ret['ok']){ 
		        $body     = [];
		        $ignored  = ['request_id', 'user_id', 'entrydate'];        
		        $required = ['name', 'cellphone']; 
		        
		        foreach($ret['data'] as $id=>$v){
		                $c = @$comments[$id] ? $comments[$id] : ucwords($id);
		                $req = in_array($id, $required) ? "required='required'" : "";
		                $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
		                if (in_array($id, $ignored)) continue;
		
                        $type = @$metadata[$id]['type'];
                        $size = $mydb->numbersFromString($type);                        
                        $extra = $size ? "maxlength=$size" : '';

                        $data = "<input type='text' $extra $req name='$id' id='$id' value=\"$v\">";
                        
                        /* handle some columns */
                        switch ($id){
                            case 'photo':
                            case 'picture':
                                $data = "<input $req type='file' accept='.jpg,.jpeg,.png' name='$id' id='$id'>";
                                break;
                                
                            case 'filename':
                                $data = "<input $req type='file' accept='.txt,.pdf,.doc' name='$id' id='$id'>";
                                break;
                                
                            case 'active':
                                $values = '';
                                $opts = [1=>'Active', 0=>'Disabled'];
                                foreach($opts as $id0=>$val0){
                                	$selected = $id0 == $v ? 'selected' : '';
                                    $values .= "<option value='$id0' $selected>$val0</option>";
                                }
                                
                                $data = "<select $req name='$id' id='$id'>
                                          $values
                                         </select>";
                                break;
                                
                            case 'category_id*':
                                $ret = $mydb->getTableColumnData('categories', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id=>$val){
                                        $values .= "<option value='$id'>$val</option>";
                                    }
                                    
                                    $data = "<select $req id='$id'>$values</select>";
                                }           			
                                break;
                                
                            case 'email':
                            case 'email_address': 
                                $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                                break;
                                
                            default:
                                break;
                        } /* handle some columns */  

		                $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
		        }		
		        
		        $body = implode('', $body);
		
		        echo "<form method='post' enctype='multipart/form-data'>
		               <fieldset>
		                <legend>Edit Request</legend>
		                $body
                        <input type='submit' name='edit' value='Update Record'>               
		               </fieldset>
		              </form>
		              <p>&nbsp;</p>
		              ";
		
		    } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
		    }    
		    break; 

        case 'details': // get details of the driver
            $mydb = new CForm( $settings->database_host,
                               $settings->database_user,
                               $settings->database_pwd, 
                               $settings->database_name );
                            
            $ret = $mydb->generateForm( $settings->tables_requests,'request_id', $actionid);
        
            if ($ret['ok']){
                $body     = [];
                $ignored  = ['id'];        
                $required = []; // these values in the array will have the required property added to the input field
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $data = "<span>$v</span>";
                        $body[] = "<tr>
                                     <th><label for='$id'>$c $requiredstar</label></th><td>$data</td>
                                   </tr>";
                }    
                $body = implode('', $body);
        
                echo "<form method='post'>
                       <fieldset>
                        <legend>Request Details</legend>
                        <table>
                         <tbody>$body</tbody>
                        </table>
                       </fieldset>
                      </form>
                      <!--p>&nbsp;</p-->
                      ";

            } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
            }    
            break;     
        
        case 'delete': // delete the driver
            $ret = $app->deleteRequest($actionid);
            if (!$ret['ok']){
                if ($ret['error']) echo 'Error: ' . $ret['error'];
                
            } else {
                die("<script>window.location.href='?view=requests';</script>"); // show all requests
            }
            break;
             
             
     default: // show all the requests
		echo $settings->html_requests_title;

		$result = $app->getRequests();
		if (!$result['ok']){
		   echo $settings->error_norequests;
		   
		} else {
	
			$ignore = ['request_id', 'user_id'];
			$cols = $result['data']['cols'];
			$rows = $result['data']['rows'];
			$th = ''; $td = '';

			foreach($cols as $col){
			    if (in_array($col, $ignore)) continue; // don't show this field on the html form
				$th .= "<th>$col</th>";
			}
			// extra th
			$th .= "<th>Actions</th>";

			$idx = 0;
			
			foreach($rows as $row){
				$td .= "<tr>";
 				
				foreach($cols as $col){
				    if (in_array($col, $ignore)) continue; // don't show this field on the html form
  
				    $val = $row[$col];
				    //if ($col == 'Title') $val = "<a href='#' class='youtube-driver' onclick=\"queuedriver($idx);\" data-youtube-id=\"$id\">$val</a>";
				    $td .= "<td>$val</td>";
				}       
				// actions 
				$requestid = $row['request_id'];
                $actions = $settings->html_actions_requests;
                $actions = str_replace('{requestid}', $requestid, $actions);
                				
				$td .= "<td>$actions</td>";
				$td .= "</tr>";
				$idx++;
			}    
			echo "<table>";
			echo "<thead><tr>$th</tr></thead>";
			echo "<tbody>$td</tbody>";
			echo "</table>";   
			
			// use javascript to confirm if we want to delete record
            echo "<script>
                    function confirmDelete(id){
                        if (confirm('Delete the selected record?')){
                            window.location.href = '?view=requests&action=delete&id='+id;
                        }
                        return false;
                    }		
                    window.setTimeout(function(){
                        window.location.reload();
                    },60000)
                </script>";			
		}   
 }
