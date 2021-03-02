<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  t3_dataAdmin.php
#	Version:  July 25, 2009
#	Purpose:  Data Administration Interface:  Add/Modify/Delete Production Models, Change Zone of Protection Parameters, and Change Class of Protection Values

session_start();  //Start the session
ob_start();
if (!isset($_SESSION['user_id']))
{
	$_SESSION['requesting-page'] = $_SERVER["REQUEST_URI"];
	//echo $_SESSION['requesting-page'];
	header("Location: t3_logon.php"); 
	exit();
}
else
{
	$_SESSION['requesting-page'] = $_SERVER["REQUEST_URI"];
	//Include global functions and set-up content area
	//set entry type to Production Model
	$entry_type = 'PRO';
	
	include('./includes/functions.php');
	include ('./includes/header.php');

	//Open database
	$database = db_connect();

	//See if User is granted access
	$grantedAccess = grantAccess ($access_level=1, $database);
	
	if ($grantedAccess == 1)
	{
		//get Help tool tip content
		echo "<script type=\"text/javascript\" src=\"./site/tooltip.js\"></script>";
		
		//Application Functions
		adminLinks($database);
		
		echo "<h1 class='app-label'>Model Administration</h1>";
		echo "<h2>T3 Application Production Model Administration</h2>";
		
		//If the edit hyperlink is clicked
		if (isset($_GET['edit']))
		{
			echo "<h3>Edit Model Requested</h3>";
			
			//Set variables to pass to function
			$type = 1;
			$id = $_GET['edit'];
			$part_number = getModelNumber ($id, $database);
			$triggering_time = getTriggeringTime ($id, $database);
			//Display form that displays modification
			modelModification($type, $id, $part_number, $triggering_time, $entry_type);
			modelTable($entry_type, $database);
			zonesareadiv($database);
		}
		elseif (isset($_GET['delete']))
		{
			echo "<h3>Delete Requested</h3>";
			$id = $_GET['delete'];
			deleteConfirmForm($id, $database, $entry_type);
			//Output model table
			modelTable($entry_type, $database);
			//Zones of Protection data forms
			zonesareadiv($database);
		}
		//If user clicks the "Add Model" button from the Production Model Administration Form
		elseif (isset($_POST['add-model']))
		{
			echo "<h3>Add Model Request</h3>";
			$type = 0;
			$id = 0;
			modelModification($type, $id, '', '', $entry_type);
			zonesareadiv($database);
		}
		//If user submits the Model Modification Form with Add
		elseif (isset($_POST['add-submit']))
		{
			//if either field is empty return the form with the mising data
			if ((empty($_POST['part-number'])) or (empty($_POST['triggering-time'])))
			{
				echo "<h3 class='error'>Both fields must contain data</h3>";
				$type =0;
				$id = 0;
				modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type='PRO');
				modelTable($entry_type, $database);
				zonesareadiv($database);
			}
			//if both fields contain data
			else
			{
				$triggering_time = $_POST['triggering-time'] + 0;
				//if the value is numeric
				if ((is_numeric($triggering_time)) && ($triggering_time > 0))
				{
					//See if the part number is in the database
					$is_found = checkPartNum($_POST['part-number'], $id, $entry_type='PRO', $database);
					if ($is_found > 0)
					{
						echo "<h3 class='error'>Cannot add part.  This part number is already in the database.</h3>";
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
					elseif ($is_found == 0)
					{
						$entry_type = 'PRO';
						addModel($_POST['part-number'], $triggering_time, $entry_type, $database);
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
					else
					{
						echo "<h3 class='error'>There was an error with your request</h3>";
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
				}
				//else display form with error
				else
				{
					echo "<h3 class='error'>Triggering time must contain a numeric value greater than zero</h3>";
					$type =0;
					$id = 0;
					modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type);
					zonesareadiv($database);
				}
			}
		}
		//If the user sumbits the Model Modification Form with Edit
		elseif (isset($_POST['edit-submit']))
		{
			//if either field is empty return the form with the mising data
			if ((empty($_POST['part-number'])) or (empty($_POST['triggering-time'])))
			{
				echo "<h3 class='error'>Both fields must contain data</h3>";
				$type =1;
				$id = $_POST['model-id'];
				modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type);
				zonesareadiv($database);
			}
			//if both fields contain data
			else
			{
				//set the model's id
				$id = $_POST['model-id'];
				$triggering_time = $_POST['triggering-time'] + 0;
				//if the value is numeric and greater than zero
				if ((is_numeric($triggering_time)) && ($triggering_time > 0))
				{
					//See if the part number is in the database
					$is_found = checkPartNum($_POST['part-number'], $id, $entry_type='PRO', $database);
					if ($is_found > 0)
					{
						echo "<h3 class='error'>Cannot Modify the part.  You tried to change the part number to a part number that is already in the database.</h3>";
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
					elseif ($is_found == 0)
					{
						//Update model data
						updatePartNumber($id, $_POST['part-number'], $triggering_time, $database);
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
					else
					{
						echo "<h3 class='error'>There was an error with your request</h3>";
						modelTable($entry_type, $database);
						zonesareadiv($database);
					}
				}
				//else display form with error
				else
				{
					echo "<h3 class='error'>Triggering time must contain a numeric value greater than zero</h3>";
					$type =1;
					$id = $_POST['model-id'];
					modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type);
					modelTable($entry_type, $database);
					zonesareadiv($database);
				}
			}
		}
		elseif (isset($_POST['confirm-delete']))
		{
			deleteModel($_POST['model-id'], $database);
			//Output model table
			modelTable($entry_type, $database);
			//Zones of Protection data forms
			zonesareadiv($database);
		}
		elseif (isset($_POST['cancel-delete']))
		{
			echo "<h3 class='error'>Deletion has been CANCELLED</h3>";
			modelTable($entry_type, $database);
			zonesareadiv($database);
		}
		elseif (isset($_POST['editzone-submit']))
		{
			if ((is_numeric($_POST['min_height'])) and (is_numeric($_POST['max_height'])) and (is_numeric($_POST['increment'])) and (is_numeric($_POST['increment_start']))) 
			{
				updateZones($_POST['min_height'], $_POST['max_height'], $_POST['increment'], $_POST['increment_start'], $database);
				//Output model table
				modelTable($entry_type, $database);
				//Zones of Protection data forms
				zonesareadiv($database);
			}
			else
			{
				echo "<p class='error'>Your request did not process because there was non-numeric values submitted.</p>";
				//Output model table
				modelTable($entry_type, $database);
				//Zones of Protection data forms
				zonesareadiv($database);
			}
			
		}
		elseif (isset($_POST['classupdate-submit']))
		{
			if($_POST)
			{
				//Moves through all fields on the submitted form and passes to the update query
				$vars=$_POST;
				foreach($vars as $key => $value)
				{
					//Exclude the submit button
					if ($key != 'classupdate-submit')
					{
						if (is_numeric($value))
						{
							//Find record id by stripping the starting field name
							$id = trim($key, 'class-');
							//Run update query
							updateClass($id, $value, $database);
						}
						else
						{
							$id = trim($key, 'class-');
							$className = getClassName($id);
							echo "<h3 class='error'>".$className." was not updated because the value submitted was not a numeric value.  Please enter a numeric value and try again.</h3>";
						}
					}
				}
			}
			else
			{
				echo "<h3 class='error'>There was an error getting the form data</h3>";
			}
			//Output model table
			modelTable($entry_type, $database);
			//Zones of Protection data forms
			zonesareadiv($database);
		}
		else
		{
			//Output model table
			modelTable($entry_type, $database);
			//Zones of Protection data forms
			zonesareadiv($database);
			
		}
	}
	else
	{	
		echo "<h3 class='error'>You do not have permission to access this page</h3>";
		echo "<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
	}
	//Output end divs and footer
	bottomContent();
	//Close Database
	mysqli_close($database);
} // End Else from Login Check

###################################################################
#                      DATA Functions                             #
###################################################################
function updateZones($min_height, $max_height, $increment, $increment_start, $database)
{	
	if ($database)
	{
		//look up current record
		$lookupClass_query = "SELECT min_height, max_height, increment, increment_start FROM zonesdata WHERE zone_data_id=1";
		//echo "<p>".$lookupClass_query."</p>";
		//Send Query
		$lookup_run = @mysqli_query ($database, $lookupClass_query);
		
		//if the query returns a result
		if ($lookup_run)
		{
			while ($zone_row = mysqli_fetch_array($lookup_run))
			{
				//set-update to current values
				$update_min = $zone_row[0];
				$update_max = $zone_row[1];
				$update_increment = $zone_row[2];
				$update_start = $zone_row[3];
				
				//if values are different set the update value to the POST data
				if ($min_height != $zone_row[0])
				{
					$update_min = $min_height;
					echo "<h3>Minimum Height was updated to ".$update_min."</h3>";
				}
				if ($max_height != $zone_row[1])
				{
					$update_max = $max_height;
					echo "<h3>Maximum Height was updated to ".$update_max."</h3>";
				}
				if ($increment != $zone_row[2])
				{
					$update_increment = $increment;
					echo "<h3>Increment Start was updated to ".$update_increment."</h3>";
				}
				if ($increment_start != $zone_row[3])
				{
					$update_start = $increment_start;
					echo "<h3>Increment Start was updated to ".$update_start."</h3>";
				}				
			}
				$updateZone_query = "UPDATE zonesdata SET min_height='$min_height', max_height='$max_height', increment='$increment', increment_start='$increment_start' WHERE zone_data_id=1";
				//echo "<p>$updateZone_query</p>";
				//Run Query
				$run = @mysqli_query ($database, $updateZone_query);
		}
		else
		{
			echo "<h3 class='error'>There was a problem modifying this data.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request</h3>";
	}
}

function updateClass($id, $value, $database)
{	
	if ($database)
	{
		//look up current record
		$lookupClass_query = "SELECT class, value FROM classdata WHERE class_id=$id";
		//Send Query
		$lookup_run = @mysqli_query ($database, $lookupClass_query);
		
		//if the query returns a result
		if ($lookup_run)
		{
			while ($class_row = mysqli_fetch_array($lookup_run))
			{
				//if values are different run the update query
				if ($value != $class_row[1])
				{
					//set-up update query
					$updateClass_query = "UPDATE classdata SET value='$value' WHERE class_id=$id LIMIT 1";
					//Run Query
					$run = @mysqli_query ($database, $updateClass_query);
		
					//If the query ran 
					if ($run) 
					{ 
						echo "<h3>".$class_row[0]."'s value was updated to ".$value."</h3>";
					}
					else
					{
						echo "<p>There was a problem modifing the data</p>";
					}
				}
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem modifying this data.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request</h3>";
	}
}
	
###################################################################
#                      HTML Functions                             #
###################################################################

//Outputs the zone and class of protection data forms wrapped in the zones-data div tag
function zonesAreaDiv($database)
{
	echo "<div id='zones-data'>";
			
	echo "<h1>Zone of Protection & Class of Protection Data</h1>";
		zoneProtectionForm($database);
		classDataForm($database);
	echo "</div>";
}

//Display the Zone of Protection form and its data for update only
function zoneProtectionForm ($database) 
{	
	if ($database)
	{
		//set-up update query
		$zonesData_query = "SELECT min_height, max_height, increment, increment_start FROM zonesdata WHERE zone_data_id =1";
		//Run Query
		$run = @mysqli_query ($database, $zonesData_query);
		
		//If the query ran
		if ($run) 
		{
			//set-up the zone form
			echo "<form action='t3_dataAdmin.php' method='post'id='zones-form' name ='zones-form'>";
			echo "<fieldset>";
			echo "<legend id='label-zop'>&nbsp;Zone of Protection&nbsp;</legend>";		
			while ($zone_row = mysqli_fetch_array($run))
			{
				echo "<p><label id='label-min_height' for='min_height' class='left'><strong>Minimum Height: </strong></label>";
				echo "<input type='text' name='min_height' id='min_height' class='field' value='".$zone_row[0]."' tabindex='1'/></p>";
			
				echo "<p><label id='label-max_height' for='max_height' class='left'><strong>Maximum Height: </strong></label>";
				echo "<input type='text' name='max_height' id='max_height' class='field' value='".$zone_row[1]."' tabindex='2'/></p>";
			
				echo "<p><label id='label-increment'  for='increment' class='left'><strong>Increment By: </strong></label>";
				echo "<input type='text' name='increment' id='increment' class='field' value='".$zone_row[2]."' tabindex='3'/></p>";
			
				echo "<p><label id='label-increment_start' for='increment_start' class='left'><strong>Increment Start: </strong></label>";
				echo "<input type='text' name='increment_start' id='increment_start' class='field' value='".$zone_row[3]."' tabindex='4'/></p>";
			}
			echo "<p><input type='submit' name='editzone-submit' id='editzone-submit' class='button' value='Update Data' tabindex='5' /></p>";
			echo "</fieldset>";
			echo "</form>";
		}
		else
		{
			echo "<h3 class='error'>There was an issue getting the zone of protection data</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was an issue with the database</h3>";
	}
}

//Display Class' and its data for update only.
function classDataForm($database)
{	
	if ($database)
	{
		//set-up update query
		$class_query = "SELECT class_id, class, value FROM classdata";
		//Run Query
		$run = @mysqli_query ($database, $class_query);
		
		//If the query ran
		if ($run) 
		{
			//Set up the form
			echo "<form  action='t3_dataAdmin.php' method='post' id='class-form' name='class-form'>";
			echo "<fieldset>";
			echo "<legend id='label-cop'>&nbsp;Class of Protection&nbsp;</legend>";
			$count = 1;
			while ($class_row = mysqli_fetch_array($run))
			{
				$class_id = $class_row[0];
				$class_name = $class_row[1];
				$class_value = $class_row[2];
			
				echo "<p><label id='label-cop_warning' for='class-".$class_id."' class='left'><strong>".$class_name.": </strong></label>";
				echo "<input type='text' name='class-".$class_id."' id='class-".$class_id."' class='field' value='".$class_value."' tabindex='".$count."'/></p>";
				$count = $count+1;
			}
			//Close out form elements
			echo "<h1>&nbsp;</h1>";
			echo "<p><input type='submit' name='classupdate-submit' id='classupdate-submit' class='button' value='Update Data' tabindex='".($count+1)."' /></p>";
			echo "</fieldset>";
			echo "</form>";
		}
		else
		{
			echo "<h3 class='error'>There was a problem getting Class information</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
}

function bottomContent()
{
	echo "</div>";
	echo "</div>";
	echo "</div>";
	include ('./includes/footer.html');}
?>
