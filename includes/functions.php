<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  functions.php
#	Version:  July 25, 2009
#	Purpose:  Contains functions shared between multiple scripts, such as, database connection, calculations, html styles, and html forms

########################################
#          DATABASE FUNCTIONS          #
########################################

function db_connect()
{
	require ('db_functions.php');
	return $dbc;
}

###################################
#         CALCULATIONS            #
###################################

//Returns Radius of Protection Calculation
function getRp($class_value, $model_value, $height)
{
	//SQRT(height * (2 * Class Value - height) +  triggering time * (2 * Class Value + Triggering Time
	$rp = sqrt($height * (2 * $class_value-$height) + $model_value * (2 * $class_value + $model_value));

	//20-21% Decrease < 5m @ 1M
	if ($height == 1)
	{
		$rp = $rp *0.205;
	}
	//40-42% Decrease < 5m @ 2m
	elseif ($height == 2)
	{
		$rp = $rp * 0.42;
	}
	//60-61% Decrease < 5m @ 3m
	elseif ($height == 3)
	{
		$rp = $rp * 0.61;
	}
	//80-81% Decrease < 5m @ 4m
	elseif($height == 4)
	{
		$rp = $rp * 0.81;
	}

	//Format the number with no decimals
	$rp=number_format($rp,0);
	return $rp;
}

//Returns values associated with number of downconductors: Safety Distance
function getDownConductor($value)
{
	if ($value == 1)
	{
		return 1;
	}
	elseif ($value == 2)
	{
		return 0.6;
	}
	elseif ($value ==3)
	{
		return 0.05;
	}
	else
	{
		return 0;
	}
}

//Returns values associated with the type of material: Safety Distance
function getMaterial($value)
{
	if ($value == 1)
	{
		return 1;
	}
	elseif ($value == 2)
	{
		return 0.5;
	}
	else
	{
		return 0;
	}
}

//Returns values associated with Class:  Safety Distance
function getSafetyClass($value, $database)
{
	$class = getClassName($value, $database);

	if ($class == "Class I")
	{
		return 0.1;
	}
	elseif ($class = "Class II")
	{
		return 0.075;
	}
	elseif ($class == "Class III")
	{
		return .05;
	}
	else
	{
		return 0;
	}
}

//Returns the safety distance in Meters
function getSafetyDistance($downconductors, $class, $material, $length)
{
	$safety_distance = $downconductors * ($class/$material) * $length;
	$safety_distance=number_format($safety_distance,2);
	return $safety_distance;
}

//Returns the Gain time of a model
function getGainInTime($triggering_time)
{
	$gain = $triggering_time * 1.23;
	$gain=number_format($gain,1);
	return $gain;
}
################################
#       DATA FUNCTIONS         #
################################
//Returns the value stored in the database for the parameter submitted
function getZoneParameter ($parameter, $database)
{
	if($database)
	{
		//Set up search query
		$findParameter_query = "SELECT $parameter FROM zonesdata where zone_data_id=1";
		//Run query
		$run = @mysqli_query ($database, $findParameter_query);

		if ($run)
		{
			while ($findParameter_row = mysqli_fetch_array($run))
			{
				return $findParameter_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested Value.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (01)</h3>";
	}
}

//Returns the triggering time given the records' id
function getTriggeringTime ($id, $database)
{
	if($database)
	{
		//Set up search query
		$findTriggeringTime_query = "SELECT triggering_time FROM modelsdata where model_id=$id";
		//Run query
		$run = @mysqli_query ($database, $findTriggeringTime_query);

		if ($run)
		{
			while ($triggeringTime_row = mysqli_fetch_array($run))
			{
				return $triggeringTime_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested Triggering Time.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (02)</h3>";
	}
}

//Returns the part number given the record's id
function getModelNumber ($id, $database)
{
	if($database)
	{
		//Set up search query
		$findPartNumber_query = "SELECT part_number FROM modelsdata WHERE model_id=$id";
		//Run query
		$run = @mysqli_query ($database, $findPartNumber_query);

		if ($run)
		{
			while ($partNumber_row = mysqli_fetch_array($run))
			{
				return $partNumber_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested part number.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (03)</h3>";
	}
}

//Returns the class given the record's id
function getClassName ($id, $database)
{
	if($database)
	{
		//Set up search query
		$findClassName_query = "SELECT class FROM classdata WHERE class_id=$id";
		//Run query
		$run = @mysqli_query ($database, $findClassName_query);

		if ($run)
		{
			while ($className_row = mysqli_fetch_array($run))
			{
				return $className_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested Class Name.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (04)</h3>";
	}
}

//Get the class value from the record id
function getClassValue ($id, $database)
{
	if($database)
	{
		//Set up search query
		$findClassValue_query = "SELECT value FROM classdata WHERE class_id=$id";

		//Run query
		$run = @mysqli_query ($database, $findClassValue_query);

		if ($run)
		{
			while ($classValue_row = mysqli_fetch_array($run))
			{
				return $classValue_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested Class Value.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (05)</h3>";
	}
}

//Checks to see if a part number already exists in the database
function checkPartNum($part_number, $id, $entry_type, $database)
{
	if($database)
	{
		//Set up search query
		$checkPart_query = "SELECT part_number FROM modelsdata where part_number='$part_number' AND entry_type='$entry_type' AND model_id != '$id'";

		//Run query
		$run = @mysqli_query ($database, $checkPart_query);

			$count = mysqli_num_rows($run);

			return $count;
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (06)</h3>";
	}
}

//Adds Model
function addModel($part_number, $triggering_time, $entry_type, $database)
{
	//the the database opens
	if ($database)
	{
		//set-up query
		$addModel_query = "INSERT INTO modelsdata (part_number, triggering_time, entry_type) VALUES ('$part_number', '$triggering_time', '$entry_type')";

		//Run Query
		$run = @mysqli_query ($database, $addModel_query); // Run the query.

		if ($run)
		{
			echo "<h3><strong>The following model information was added</strong></h3>";
			echo "<p><strong>Part Number: </strong>".$part_number."</p>";
			echo "<p><strong>Triggering Time: </strong>".$triggering_time."</p>";
			echo "<hr />";
		}
		else
		{
			echo "<h3 class='error'>There was a problem adding your Model to the database.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (07)</h3>";
	}
}

//Updates Model Information
function updatePartNumber($model_id, $part_number, $triggering_time, $database)
{
	if ($database)
	{
		//set-up update query
		$updatePartNumber_query = "UPDATE modelsdata SET part_number='$part_number', triggering_time='$triggering_time' WHERE model_id=$model_id LIMIT 1";
		//Run Query
		//echo "<p>Query: ".$updatePartNumber_query."</p>";
		$run = @mysqli_query ($database, $updatePartNumber_query);

		//If the query ran
		if ($run)
		{
			echo "<h3>The model information now is: </h3>";
			echo "<p>Part Number: ".$part_number."</p>";
			echo "<p>Triggering Time: ".$triggering_time."</p>";
			echo "<hr />";
		}
		else
		{
			echo "<h3 class='error'>There was a problem updating the model requested.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request (08)</h3>";
	}
}

//Deletes the model from the database
function deleteModel($id, $database)
{
	//If the database is opened
	if($database)
	{
		//get part number
		$model_number = getModelNumber ($id, $database);

		//set-up query
		$deleteModel_query = "DELETE FROM modelsdata WHERE model_id=".$id;
		//echo "<p>".$deleteModel_query."</p>";
		//Run Query
		$runDelete = @mysqli_query ($database, $deleteModel_query); // Run the query.

		if($runDelete)
		{
			echo "<h3>The model, ".$model_number.", has been deleted</h3>";
		}
		else
		{
			echo "<h3 class='error'>There was an error deleting the model.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
}

###############################
#         HTML STYLES         #
###############################
//Combo box selected
function checkSelection($picked, $totest)
{
	if ($picked == $totest)
	{
		return "selected=' '";
	}
}

function classStyle($class)
{
	//set-up style to be used
	if ($class == "Class I")
	{
		$style = 'model-c1';
		return $style;
	}
	elseif ($class == "Class II")
	{
		$style = 'model-c2';
		return $style;
	}
	if ($class == "Class III")
	{
		$style = 'model-c3';
		return $style;
	}
}

function modelStyle($class)
{
	//set-up style to be used
	if ($class == "Class I")
	{
		$style = 'Class-I';
		return $style;
	}
	elseif ($class == "Class II")
	{
		$style = 'model-c2';
		return $style;
	}
	if ($class == "Class III")
	{
		$style = 'model-c3';
		return $style;
	}
}

function formatMissing($submit, $value)
{
	//if the form has never been submitted display regular text
	if ($submit == 0)
	{
		return "";
	}
	//if the form submit has been thrown a negative 1, it indicates an additional validation requirement failed
	elseif($submit == -1)
	{
		return "style='color: #CC0000'";
	}
	else
	{
		if (empty($value))
		{
			return "style='color: #CC0000'";
		}
		else
		{
			return "";
		}
	}
}

###############################
#      SHARED HTML FORMS      #
###############################

function deleteConfirmForm($id, $database, $entry_type)
{
	//Get Model Number
	$part_number = getModelNumber ($id, $database);
	//Get Triggering Time
	$triggering_time =getTriggeringTime($id, $database);
	if ($type == 'TEST')
	{
		$url = 't3_testData.php';
	}
	else
	{
		$url = 't3_dataAdmin.php';
	}

	echo "<form action='".$url."' method='post' id='model-form' name='model-form'>";
    echo "<fieldset>";
    echo "<legend id='label-delete_confirm'>&nbsp;Delete Model Confirmation&nbsp;</legend>";
    echo "<p>Please Confirm the Deletion of the following model:</p>";
    echo "<p class='data'><strong> Model Number: </strong>". $part_number."<br />";
	echo "<strong>Triggering Time: </strong> ".$triggering_time."</p>";
	echo "<input type='hidden' name='model-id' id='model-id' value='".$id."' />";
    echo "<p><input type='submit' name='confirm-delete' id='confirm-delete' class='button' value='Confirm'/>";
	echo "<input type='submit' name='cancel-delete' id='cancel-delete' class='button' value='Cancel'/></p>";
    echo "</fieldset>";
    echo "</form>";
}
function modelModification($type, $id, $part_number, $triggering_time, $entry_type)
{
	if ($entry_type == 'TEST')
	{
		$url = 't3_testData.php';
	}
	else
	{
		$url = 't3_dataAdmin.php';
	}
	echo "<form action='".$url."' method='post' id='model-form' name='model-form'>";
    echo "<fieldset>";
		if ($entry_type == 'TEST')
		{
			echo "<legend id='label-modify'>&nbsp;Test Model Modification&nbsp;</legend>";
		}
		elseif ($entry_type == 'PRO')
		{
			echo "<legend id='label-modify'>&nbsp;Production Model Modification&nbsp;</legend>";
		}
    echo "<p><label id='label-model-number' for='model-number' class='left'><strong>Model Number: </strong></label>";
	echo "<input type='text' name='part-number' id='part-number' class='field' value='".$part_number."' tabindex='1'/></p>";
    echo "<p><label id='label-triggering-time' for='triggering-time' class='left'><strong>Triggering Time: </strong></label>";
	echo "<input type='text' name='triggering-time' id='triggering-time' class='field' value='".$triggering_time."' tabindex='2'/></p>";
	//if Edit was selected
    if ($type==1)
	{
		echo "<input type='hidden' name='model-id' id ='model-id' value='".$id."'>";
	    echo "<p><input type='submit' name='edit-submit' id='edit-submit' class='button' value='Edit' tabindex='3' /></p>";
    }
	//if Add was selected
	else
	{
        echo "<p><input type='submit' name='add-submit' id='add-submit' class='button' value='Add' tabindex='3' /></p>";
    }
    echo "</fieldset>";
    echo "</form>";
}
function modelTable($entry_type, $database)
{
 //$database = db_connect();
	if ($database)
	{
		//set URL for edit and delete
		if ($entry_type == 'TEST')
		{
			$url = 't3_testData.php';
		}
		else
		{
			$url = 't3_dataAdmin.php';
		}
		//set-up update query
		$model_query = "SELECT model_id, part_number, triggering_time FROM modelsdata WHERE entry_type='$entry_type'";
		//Run Query
		$run = @mysqli_query ($database, $model_query);
		//echo "<p>".$model_query."</p>";

		if($run)
		{
			echo "<form action='".$url."' method='post' id='model-admin' name='model-admin'>";
			echo "<fieldset>";
			//Echo the legend showing which type of table data is being displayed
			if ($entry_type == 'TEST')
			{
				echo "<legend id='label-testMA'>&nbsp;Test Model Administration&nbsp;</legend>";
			}
			elseif ($entry_type == 'PRO')
			{
				echo "<legend id='label-proMA'>&nbsp;Production Model Administration&nbsp;</legend>";
			}
			echo "<input name='add-model' type='submit' value='Add Model' class='button' />";
			echo "<table id='model-table' border='0' cellspacing='0' cellpadding='0'>";
			echo "<tr>";
			echo "<td class='table-name' colspan='5'>Modify Existing An Existing Model</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th colspan='2' scope='col'>Actions</th>";
			echo "<th scope='col'>Model Number</th>";
			echo "<th scope='col'>Triggering Time</th>";
			echo "</tr>";

			//Begin While records
			while ($model_row = mysqli_fetch_array($run))
			{
				echo "<tr class='model-record'>";
				echo "<td><a href='".$url."?edit=".$model_row[0]."'>Edit</a></td>";
				echo "<td><a href='".$url."?delete=".$model_row[0]."'>Delete</a></td>";
				echo "<td>".$model_row[1]."</td>";
				echo "<td>".$model_row[2]."</td>";
				echo "</tr>";
			}
			//End while material

			echo "</table></fieldset></form>";
		}
		else
		{
			echo "<p>There was a problem with your request (09)</p>";
		}
	}
	else
	{
		echo "<p>There was a problem with the database</p>";
	}
}

###############################
#       USER FUNCTIONS        #
###############################

//Find Privilege Level and grant access to the page
function grantAccess ($access_level, $database)
{
	$user_id = $_SESSION['user_id'];
	//Find user's privileges
	$privs = getPrivileges($user_id, $database);
	//Create an array
	$privs_array =  str_split($privs);

	//Move through the array
	$granted = 0;
	//If the user is admin they get access to everything
	if((array_search(3, $privs_array) > 0) or (array_search(3, $privs_array) === 0))
	{
		$granted = 1;
	}
	//if the access level is 0 then everyone gets access to it
	elseif($access_level == 0)
	{
		$granted = 1; //everyone has access to the page
	}
	//if the acccess level is found in the $privs_array then allow access
	//allows array key or 0 to validate, but doesnot allow empty return
	elseif ((array_search($access_level, $privs_array) > 0) or (array_search($access_level, $privs_array) === 0))
	{
		$granted = 1;
	}
	else
	{
		$granted = 0;
	}
	return $granted;
}
//gets the users privilege level based on his or her userid
function getPrivileges($user_id, $database)
{
	if($database)
	{
		//Setup user query
		$user2_query = "SELECT privileges FROM usersdata WHERE user_id=$user_id";
		//run query
		$run = @mysqli_query ($database, $user2_query);

		//If the query ran set and return the $privileges variable
		if($run)
		{
			while ($user_row = mysqli_fetch_array($run))
			{
				$privileges = $user_row[0];
			}
			return $privileges;
		}
		else
		{
			echo "<h3 class='error'>There was a problem retreiving the privileges: ".$user_id."</h3>";
		}
	}
	else
	{
		echo "<h3 error='class'>There was a problem with the database</h3>";
	}
}

//adds Admin links to Admin pages
function adminLinks($database)
{
	$user_id = $_SESSION['user_id'];
	$privileges = getPrivileges($user_id, $database);

	$privs_array =  str_split($privileges);
	//Set up div / navigation
	echo "<div id='app-functions'>";
	echo "<ul>";


	//move through the array and find the highest level or permissions
	$highest_privs = -1;
	foreach ($privs_array as $p)
	{
		if ($p > $highest_privs)
		{
			$highest_privs = $p;
		}
	}
	//if the hightest priv is greater than 0 (standard user does not get admin links)
	if ($highest_privs >0)
	{
		//if admin privileges
		if ($highest_privs == 3)
		{
			adminURLS($highest_privs);
		}
		//Run through loop to see what privileges have been granted
		else
		{
			foreach ($privs_array as $p)
			{
				adminURLS($p);
			}
		}
	}
	//Close out div
	echo "<li><img id='helpicon' src='./site/HelpIcon.gif' width='20' height='19'/></li>";
	echo "</ul>";
	echo "</div>";
}

//Assigns urls to administration pages based on permissions
function adminURLS($value)
{
	$level_one = "<li><a href='t3_dataAdmin.php'>&raquo; Data Management</a></li>";
	$level_two = "<li><a href='t3_testData.php'>&raquo; Test Data Interface</a></li>";
	$level_three = "<li><a href='t3_admin.php'>&raquo; User Management</a></li>";
	if ($value == 1)
	{
		echo $level_one;
	}
	elseif ($value == 2)
	{
		echo $level_two;
	}
	elseif ($value == 3)
	{
		echo $level_one.$level_two.$level_three;
	}
}

function validateEmail($email)
{
	if (!eregi("^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,6}$", $email))
		{
    		return 0; //Incorrect email format
   		}
		else
		{
			return 1; //correct email format
		}
}

function isSameUser($email, $database)
{
	if ($database)
	{
		//Set-up update query
		$find_query = "SELECT email FROM usersData WHERE email='".$email."'";
		//Send Query
		$find_result = @mysqli_query($database, $find_query);

		$num_rows =  mysqli_num_rows($find_result);

		return $num_rows;
	}
	else
	{
		echo "<h3 class='error'>There was an error with your request</h3>";
	}
}
#####################################
# 		    SHARED CHARTS   		#
#####################################

function zoneChart($selected_models, $form_status, $database)
{
	if ($form_status >0)
	{
		echo "<div id='testApp-container'>";
    }
	echo "<h1 class='app-label'>&raquo; Zone of Protection Chart</h1>";
    //if the form has been submitted open the container
	if ($form_status >0)
	{
		echo "<div id='selected-form' class='calculator'>";
	}
	else
	{
		echo "<div class='calculator'>";
	}
	echo "<table border='0' cellspacing='0' cellpadding='0' id='zop'>";
    echo "<tr>";
	echo "<th scope='col' class='height'>H(m)</th>";
	//Function to output class names
	//Count number of models the column label will span
	$num_models = count($selected_models);
	//Create and run query to display column names

	//If the database is opened -- begin creating the form
	if ($database)
	{
		$classInfo_query = "SELECT class, value FROM classdata ORDER BY class ASC";
		$classInfo_run = @mysqli_query ($database, $classInfo_query);
		//Get Row Headings for Class
		if ($classInfo_run)
		{
			while ($class_row = mysqli_fetch_array($classInfo_run))
			{
				echo "<th colspan='".$num_models."' scope='col' class='".classStyle($class_row[0])."'>".$class_row[0]."</th>";
			}
			echo "</tr>";
		}
    }
	//Start Model Selected Row heading
	echo "<tr>";
    echo "<th scope='row' class='height'>&nbsp;</th>";
		//Use class query again to set-up style
		$classInfo_run = @mysqli_query ($database, $classInfo_query);
		if ($classInfo_run)
		{
			while ($class_row = mysqli_fetch_array($classInfo_run))
			{
				//set-up query to move through models selected
				foreach($selected_models as $key => $value)
				{
					//find out model number and format for table
					//out put with correct class
					$model_number=getModelNumber ($value, $database);
					$new_model_number = str_replace("-", "<br />", $model_number);
					echo "<td class='".classStyle($class_row[0])."'>".$new_model_number."</td>";
				}
			}
		}
		//end model headings
		echo "</tr>";
    //Begin Calculations
	echo"<tr>";
	//Set the height with the minimum value, which will begin the table
	$height = getZoneParameter('min_height', $database);
	//get the maximum value that will be used in the table
	$max_height = getZoneParameter ('max_height', $database);
	//get increment value
	$increment = getZoneParameter('increment', $database);
	//while the height display (and used for calucluation is less than the max height
	//display the table and perform calculations
	while ($height <= $max_height)
	{
		echo "<th scope='row' class='height'>".$height."</th>";
			//Use class query again to set-up style
			$classInfo_run = @mysqli_query ($database, $classInfo_query);
			if ($classInfo_run)
			{
				while ($class_row = mysqli_fetch_array($classInfo_run))
				{
					//set-up query to move through models selected
					foreach($selected_models as $key => $value)
					{
						//find out model number and format for table
						//out put with correct class
						$model_number=getModelNumber ($value, $database);
						$class_value = $class_row[1];
						$model_value = getTriggeringTime($value, $database);
						$rp = getRp($class_value, $model_value, $height);
						//Set style if the rp returns a valid value
						if ($rp >0)
						{
							$style_name = str_replace(" ", "-", $class_row[0]);
							$style = "class='".$style_name."'";
						}
						//if the value is greater than zero show the value
						if ($rp >0)
						{
							echo "<td ".$style.">".$rp."</td>";
						}
						//else display with no value
						else
						{
							echo "<td>&nbsp;</td>";
						}
					}
				}
			}
			//Increment Function
			//By the increment value until the stop value/ then by 2 until 6 / by 1
			$stop_increment = getZoneParameter ('increment_start', $database);
			if ($height >= -$stop_increment && $height < $stop_increment)
			{
				if ($height >= -6 && $height <6)
				{
					$height = $height +1;
				}
				else
				{
					$height = $height + 2;
				}
			}
			else
			{
				$height = $height + $increment;
			}
			echo "</tr>";
		}
        //Finsh out the table
    echo "</table>";
    echo "</div>";
    echo "</div>";
}
?>
