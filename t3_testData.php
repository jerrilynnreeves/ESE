<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  t3_testData.php
#	Version:  July 25, 2009
#	Purpose:  Test Data Interface for comparing various models inside a zone of protection chart, as well as add, delete, modify interface for Test data models.

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
	//set variables
	$zone_status=0;
	$selected_models='';
	$entry_type = 'TEST';
	//Include global functions and set-up content area
	include('./includes/functions.php');
	include ('./includes/header.php');

	//Open database
	$database = db_connect();
	
	//See if User is granted access
	$grantedAccess = grantAccess ($access_level=2, $database);
	
	if ($grantedAccess == 1)
	{
	
		//get Help tool tip content
		echo "<script type=\"text/javascript\" src=\"./site/tooltip.js\"></script>";
		
		//Application Functions
		adminLinks($database);
	
		echo "<h1 class='app-label'>Test Model Data  Panel</h1>";
		echo "<h2>T3 Application Test Model Data Access &amp; Adminisration</h2>";

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
			testDataForm($zone_status, $selected_models, $database);
			modelTable($entry_type, $database);
		}
		elseif (isset($_GET['delete']))
		{
			echo "<h3>Delete Requested</h3>";
			$id = $_GET['delete'];
			deleteConfirmForm($id, $database, $entry_type);
			//Output Zone from
			testDataForm($zone_status, $selected_models, $database);
			//Output model table
			modelTable($entry_type, $database);
		}
		//If user clicks the "Add Model" button from the Production Model Administration Form
		elseif (isset($_POST['add-model']))
		{
			echo "<h3>Add Model Request</h3>";
			$type = 0;
			$id = 0;
			modelModification($type, $id, '', '', $entry_type);
			//Output Zone from
			testDataForm($zone_status, $selected_models, $database);
			//Output model table
			modelTable($entry_type, $database);
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
				modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type);
				//Output Zone from
				testDataForm($zone_status, $selected_models, $database);
				//Output model table
				modelTable($entry_type, $database);
			}
			//if both fields contain data
			else
			{
				$triggering_time = $_POST['triggering-time'] + 0;
				//if the value is numeric
				if ((is_numeric($triggering_time)) && ($triggering_time > 0))
				{
					//See if the part number is in the database
					$is_found = checkPartNum($_POST['part-number'], $id, $entry_type='TEST', $database);
					if ($is_found > 0)
					{
						echo "<h3 class='error'>Cannot add part.  This part number is already in the database.</h3>";
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
					elseif ($is_found == 0)
					{
						$entry_type = 'TEST';
						addModel($_POST['part-number'], $triggering_time, $entry_type, $database);
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
					else
					{
						echo "<h3 class='error'>There was an error with your request</h3>";
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
				}
				//else display form with error
				else
				{
					echo "<h3 class='error'>Triggering time must contain a numeric value greater than zero</h3>";
					$type =0;
					$id = 0;
					//Output Zone from
					testDataForm($zone_status, $selected_models, $database);
					//Output model table
					modelTable($entry_type, $database);
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
				//Output Zone from
				testDataForm($zone_status, $selected_models, $database);
				//Output model table
				modelTable($entry_type, $database);
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
					$is_found = checkPartNum($_POST['part-number'], $id, $entry_type='TEST', $database);
					if ($is_found > 0)
					{
						echo "<h3 class='error'>Cannot Modify the part.  You tried to change the part number to a part number that is already in the database.</h3>";
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
					elseif ($is_found == 0)
					{
						//Update model data
						updatePartNumber($id, $_POST['part-number'], $triggering_time, $database);
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
					else
					{
						echo "<h3 class='error'>There was an error with your request</h3>";
						//Output Zone from
						testDataForm($zone_status, $selected_models, $database);
						//Output model table
						modelTable($entry_type, $database);
					}
				}
				//else display form with error
				else
				{
					echo "<h3 class='error'>Triggering time must contain a numeric value greater than zero</h3>";
					$type =1;
					$id = $_POST['model-id'];
					modelModification($type, $id, $_POST['part-number'], $_POST['triggering-time'], $entry_type);
					//Output Zone from
					testDataForm($zone_status, $selected_models, $database);
					//Output model table
					modelTable($entry_type, $database);
				}
			}
		}
		elseif (isset($_POST['confirm-delete']))
		{
			deleteModel($_POST['model-id']);
			//Output Zone Form
			//Output Zone from
			testDataForm($zone_status, $selected_models, $database);
			//Output model table
			modelTable($entry_type, $database);
		}
		elseif (isset($_POST['cancel-delete']))
		{
			echo "<h3>Deletion has been CANCELLED</h3>";
		}
		elseif (isset($_POST['selection-submit']))
		{	
			if (isset($_POST['model-selection']))
			{
				$models_selected = $_POST['model-selection'];
				$count_selected = count($models_selected);
				if ($count_selected > 5)
				{
					echo "<h3 class='error'>Your test model comparison was not processed because you have selected more than 5 models</h3>";
					//Output Zone from
					testDataForm($zone_status, $selected_models, $database);
					//Output model table
					modelTable($entry_type, $database);
				}
				elseif ($count_selected < 2)
				{
					echo "<h3 class='error'>Your test model comparison was not processed because you selected less than 2 models</h3>";
					//Output Zone from
					testDataForm($zone_status, $selected_models, $database);
					//Output model table
					modelTable($entry_type, $database);			
				}
				else
				{
					$zone_status=1;
					$selected_models = $_POST['model-selection'];
					//Output Zone from
					testDataForm($zone_status, $selected_models, $database);
					//Output model table
					modelTable($entry_type, $database);
				}
			}
			else
			{
				//Output Zone from
				testDataForm($zone_status, $selected_models, $database);
				//Output model table
				modelTable($entry_type, $database);
			}
		}
		#######################################################
		else
		{
			//Output Zone from
			testDataForm($zone_status, $selected_models, $database);
			//Output model table
			modelTable($entry_type, $database);
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
#                      HTML Functions                             #
###################################################################

function testDataForm($zone_status, $selected_models, $database)
{	
	//the the database opens begin creating the form
	if ($database)
	{
		echo "<form action='t3_testData.php' method='post' id ='testdata-form' name'testdata-form'>";
		echo "<fieldset>";
		echo "<legend id='label-testData'>&nbsp;Please Select at Least Two Models for Comparision&nbsp;</legend>";
		echo "<p><label id='model-selection' for='model-selection' class='left'><strong>Model Selection: </strong></label>";
		//Find records for the select name
		$findModels_query = "SELECT model_id, part_number, entry_type FROM modelsdata ORDER BY entry_type DESC, part_number ASC";
		//Run query
		$findModels_run = @mysqli_query ($database, $findModels_query); 
		
		if ($findModels_run)
		{
			echo "<select name='model-selection[]' class='field' size='5' multiple>";
			
			while ($model_row = mysqli_fetch_array($findModels_run))
			{
				//id = $model_row[0] part_number = $model_row[1] entry type $model_row[2]
				//set up the name to be displayed
				$model_name = $model_row[2].": ".$model_row[1];
				echo "<option value='".$model_row[0]."'>".$model_name."</option>";
			}
			
			echo "</select>";
			echo "<input type='submit' name='selection-submit' id='selection-submit' class='button' value='Submit'  />";
			echo "</p>";
			echo "<p class='bound'>Do not select more than 5 models.  To select multiple models, hold down the control key while clicking on the desired model numbers.</p>";
			echo "<hr />";
		}
		else
		{
			echo "<h3 class='error'>The was a problem getting models from which to select</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
	
	//Output zone chart if models have been selected
	if ($zone_status == 1)
	{
		$form_status = 1;
		zoneChart($selected_models, $form_status, $database);
	}
               
    echo "</fieldset>";
    echo "</form>";
}	
function bottomContent()
{
	echo "</div>";
	echo "</div>";
	include ('./includes/footer.html');
}
?>
