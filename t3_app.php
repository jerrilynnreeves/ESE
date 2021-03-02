<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  t3_app.php
#	Version:  July 25, 2009
#	Purpose:  TerraStreamer Techincal Calculators and Charts

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
	
		//Open Database
		$database = db_connect();
	
	//check to see if user has been granted permission
	$grantedAccess = grantAccess ($access_level=0, $database);

		//get Help tool tip content
		echo "<script type=\"text/javascript\" src=\"./site/tooltip.js\"></script>";	
	
	if ($grantedAccess == 1)
	{
	
		//get Help tool tip content
		echo "<script type=\"text/javascript\" src=\"./site/tooltip.js\"></script>";
	
		//Application Functions
		adminLinks($database);
		
		echo "<h1 class='app-label'>T3 Application</h1>";
		echo "<h2>TerraStreamer Technical Terminal</h2>";
		echo "<p>The T3 Application (TerraStreamer Technical Terminal) is an online resource that displays technical calculators and data for Alltec Corporation's early streamer emitter terminals (ESE).  When you click on the application headings below, the calculator or techincal data will be revealed.  Data and user administration links as well as test model comparison links will be displayed above right if you have permission to access those modulales</p>";
		echo "<div class='left-app'></div>";
		//Start application container
		echo "<div id='app-container'>";
		 
		if (isset($_POST['rp-submit']))
		{
			if((empty($_POST['class'])) or (empty($_POST['deltaT'])) or (empty($_POST['height'])))
			{
				$message =  "<p class='error'>There was missing data in your form.  Please correct the items labeled in red.</p>";
				rpCalculator($form_status=1, $_POST['class'], $_POST['deltaT'], $_POST['height'], $_POST['rp'], $message);
				safetyDistanceCalculator($form_status=0, '', '', '', '', '', $message='', $database);
				gainInLeadTimeTable($database);
				protectionRadiusTable($database);
			}
			else
			{
				rpCalculator($form_status=1, $_POST['class'], $_POST['deltaT'], $_POST['height'], '', $message ='');
				safetyDistanceCalculator($form_status=0, '', '', '', '', '', $message='', $database);
				gainInLeadTimeTable($database);
				protectionRadiusTable($database);
			}
		}
		elseif (isset($_POST['safety-submit']))
		{
			if((empty($_POST['downconductor'])) or (empty($_POST['class'])) or (empty($_POST['material'])) or (empty($_POST['length'])))
			{
				rpCalculator($form_status=0, '', '', '', '',$message='');
				$message =  "<p class='error'>There was missing data in your form.  Please correct the items labeled in red.</p>";
				safetyDistanceCalculator($form_status=1, $_POST['downconductor'], $_POST['class'], $_POST['material'], $_POST['length'], $_POST['safety-distance'], $message, $database);
				gainInLeadTimeTable($database);
				protectionRadiusTable($database);
			}
			else
			{
				rpCalculator($form_status=0, '', '', '', '',$message='');
				safetyDistanceCalculator($form_status=1, $_POST['downconductor'], $_POST['class'], $_POST['material'], $_POST['length'], $_POST['safety-distance'], $message='', $database);
				gainInLeadTimeTable($database);
				protectionRadiusTable($database);		
			}
		}
		else
		{
			rpCalculator($form_status=0, '', '', '', '', $message='');
			safetyDistanceCalculator($form_status=0, '', '', '', '', '', $message='', $database);
			gainInLeadTimeTable($database);
			protectionRadiusTable($database);
		}
			echo "</div>"; //end application container
	}
	else
	{	
		echo "<h3 class='error'>You do not have permission to access this page</h3>";
		echo "<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
	}

	//Output end divs and footer
	bottomContent();
	//close database
	mysqli_close($database);
} // End Else from Login Check
###############################################################
#                HTML FORMS / CALCULATORS                     #      
###############################################################
 
function rpCalculator($form_status, $class, $model, $height, $rp, $message)
{
	//Open database
	$database = db_connect();

	//if the form has been submitted get the protection raidus
	if ($form_status == 1 and empty($message))
	{
		//get and set variables
		$class_value = getClassValue ($class, $database);
		$model_value = getTriggeringTime ($model, $database);
		$rp = getRp($class_value, $model_value, $height);
	}

	//the the database opens begin creating the form
	if ($database)
	{
		echo "<h1 class='app-label'>&raquo; Protection Radius Calculator</h1>";

		//If the form has been submitted open the form
		if ($form_status >0)
		{
			echo "<div id='selected-form' class='calculator'>";
		}
		else
		{
			echo "<div class='calculator'>";
		}
		echo "<form action='t3_app.php' method='post' id='protection-radius' name ='protection-radius'>";
		echo "<fieldset>";
		//echo error message if  there is one
		if(!empty($message))
		{
			echo "<p class'error'>".$message."</p>";
		}
		echo "<legend id='label-prc'>&nbsp;Protection Radius Calculator&nbsp;</legend>";
		//Query Available Classes for the select
			//Find records for the class 
			$findClass_query = "SELECT class_id, class FROM classdata ORDER BY class ASC";
			//Run query
			$findClass_run = @mysqli_query ($database, $findClass_query); 
			//echo "<p>$findClass_query</p>";
			if ($findClass_run)
			{
			echo "<p ".formatMissing($form_status, $class)."><label id='label-class' for='class' class='left'>Class of Protection</label>";
				echo "<select name='class' id='class' class='field' tabindex='1'>";
				echo "<option value='0' selected=' '></option>";
					while ($class_row = mysqli_fetch_array($findClass_run))
					{
						$class_id = $class_row[0];
						$class_name = $class_row[1];
						echo "<option ".checkSelection($class, $class_id)." value='".$class_id."'>".$class_name."</option>";
					}
				echo "</select>";
			echo "</p>";
			}		
			
		//Query Available Production Models
			//Set-up query
			$findModels_query = "SELECT model_id, part_number FROM modelsdata WHERE entry_type='PRO'";
			//Run Query
			$findModels_run = @mysqli_query ($database, $findModels_query);
			
			if ($findModels_run)
			{
			echo "<p ".formatMissing($form_status, $model)."><label id='label-deltaT' for='deltaT' class='left'>Delta T</label>";
				echo "<select name='deltaT' class='field' tabindex='2'>";	
				echo "<option value='0' selected=' '></option>";
					while ($findModels_row = mysqli_fetch_array($findModels_run))
					{
						$model_id = $findModels_row[0];
						$model_name = $findModels_row[1];
						echo "<option  ".checkSelection($model, $model_id)."value='".$model_id."'>".$model_name."</option>";
					}
				echo "</select></p>";
			echo "</p>";	
			}
		echo "<p ".formatMissing($form_status, $height)."><label id='label-height' for='height' class='left'>Height of ESE in meters</label>";
		echo "<input type='text' name='height' id='height' class='field' value='".$height."' tabindex='3'/></p>";
		echo "<p><label for='rp' class='left'><strong>Radius of Protection</strong></label>";
		echo "<input type='text' name='rp' id='rp' class='calculated_field' value='".$rp."'/></p>";  
		echo "<p><input type='submit' name='rp-submit' id='rp-submit' class='button' value='Calculate' tabindex='4' /></p>";
		echo "</fieldset>";
		echo "</form>";
		echo "</div>";
	}
	else 
	{
		echo "<h3 class='error'>There was a problem connecting to the database</h3>";
	}
}

function safetyDistanceCalculator($form_status, $downconductors, $class, $material, $length, $safety_distance, $message, $database)
{   
	if ($database)
	{
		//If the form has been submitted get and calculate values
		if ($form_status == 1 and empty($message))
		{
			//get values for fields
			$downconductor_value = getDownConductor($downconductors);
			$class_value = getSafetyClass($class, $database);
			$material_value = getMaterial($material);
			//get safety distance
			$safety_distance = getSafetyDistance($downconductor_value, $class_value, $material, $length);
		}
		echo "<h1 class='app-label'>&raquo; Safety Distance Calculator</h1>";

		//If the form has been submitted open the form
		if ($form_status >0)
		{
			echo "<div id='selected-form' class='calculator'>";
		}
		else
		{
			echo "<div class='calculator'>";
		}
		echo "<form action='t3_app.php' method='post' id='safety-distance' name='safety-distance'>";
		echo "<fieldset>";
		echo "<legend id='label-sdc'>&nbsp;Safety Distance Calculator&nbsp;</legend>";
		if(!empty($message))
		{
			echo $message;
		}
		echo "<p ".formatMissing($form_status, $downconductors)."><label id='label-downconductors' for='downconductor' class='left'>Number of Down Conductors</label>";
		echo "<select name='downconductor' class='field' tabindex='1'>";
			echo "<option ".checkSelection($downconductors, 0)." value='0' selected=' '> </option>";
			echo "<option ".checkSelection($downconductors, 1)."value='1'>One</option>";
			echo "<option ".checkSelection($downconductors, 2)."value='2'>Two</option>";
			echo "<option ".checkSelection($downconductors, 3)."value='3'>Three or More</option>";
		echo "</select></p>";
		//Query Available Classes for the select
			//Find records for the class 
			$findClass_query = "SELECT class_id, class FROM classdata ORDER BY class ASC";
			//Run query
			$findClass_run = @mysqli_query ($database, $findClass_query); 
			//echo "<p>$findClass_query</p>";
			if ($findClass_run)
			{
			echo "<p ".formatMissing($form_status, $class)."><label id='label-sdc-class' for='class' class='left'>Class of Protection</label>";
				echo "<select name='class' id='class' class='field' tabindex='2'>";
				echo "<option value='0' selected=' '></option>";
					while ($class_row = mysqli_fetch_array($findClass_run))
					{
						$class_id = $class_row[0];
						$class_name = $class_row[1];
						echo "<option ".checkSelection($class, $class_id)." value='".$class_id."'>".$class_name."</option>";
					}
				echo "</select>";
			echo "</p>";
			}		
		echo "<p ".formatMissing($form_status, $material)."><label id='label-sdc-material' for='material' class='left'>Material between two looped ends</label>";
		echo "<select name='material' class='field' tabindex='3'>";
			echo "<option ".checkSelection($material, 0)." value='0' selected=' '> </option>";
			echo "<option ".checkSelection($material, 1)." value='1'>Air</option>";
			echo "<option ".checkSelection($material, 2)." value='2'>Non-metallic Solid Material</option>";
		echo "</select></p>";
		echo "<p ".formatMissing($form_status, $length)."><label id ='label-sdc-length' for='length' class='left'>Length along down conductors (m)</label>";
		echo "<input type='text' name='length' id='length' class='field' value='".$length."' tabindex='4'/></p>";
		echo "<p><label for='safety-distance' class='left'><strong>Safety Distance in Meters</strong></label>";
		echo "<input type='text' name='safety-distance' id='saftey-distance' class='calculated_field' value='".$safety_distance."'/></p>";
		echo "<p><input type='submit' name='safety-submit' id='safety-submit' class='button' value='Calculate' tabindex='5' /></p>";
		echo "</fieldset>";
		echo "</form>";
		echo "</div>";
	}
	else
	{
		echo "<h3 class='error'>There was a problem generating this form</h3>";
	}
}

function gainInLeadTimeTable($database)
{
	if ($database)
	{
		//set up table and column headings
		echo "<h1 class='app-label'>&raquo; Gain in Lead Distance Table</h1>";
		echo "<div class='calculator'>";
		echo "<table border='0' cellpadding='0' cellspacing='0' id='gain-distance'>";
		echo "<tr>";
		echo "<th colspan='3' scope='col'>Triggering Time Test Result</th>";
		echo "</tr>";
		echo "<tr class='row-heading'>";
		echo "<td width='89'>Model</td>";
		echo "<td width='117'>Advance<br />Time (µs)</td>";
		echo "<td width='178'>Gain in Lead<br />Distance (m)</td>";
		echo "</tr>";
		//Begin Model Rows
		//set up query
		$model_query = "SELECT part_number, triggering_time FROM modelsdata WHERE entry_type='PRO'";
		//Run Query
		$run = @mysqli_query ($database, $model_query);
		
		//if the query ran with success
		if($run)
		{
			//set row format to even to start
			$row_format = 0; //zero even // 1 odd
			//while there are records
			while ($model_row = mysqli_fetch_array($run))
			{
				if (row_format < 1)
				{
					echo "<tr class='even'>";
				}
				else
				{
					echo "<tr class='odd'>";
				}
				echo "<td>".$model_row[0]."</td>";
				echo "<td>".$model_row[1]."</td>";
				//get gain in lead time
				$gain = getGainInTime($model_row[1]);
				echo "<td>".$gain."</td>";
				echo "</tr>";
			
				if ($row_format == 1)
				{
					$row_format = 0;
				}
				else
				{
					$row_format = 1;
				}
			}
			//end table
			echo "</table>";
			echo "</div>";
		}
		else
		{
			echo "<h3 class='error'>There was an error with your request</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was an error with the database</h3>";
	}
}

function protectionRadiusTable($database)
{
	if ($database)
	{
		//set-up query
		$model_query = "SELECT model_id FROM modelsdata WHERE entry_type='PRO'";
		//Run Query
		$run = @mysqli_query ($database, $model_query);
		
		
		//if the query ran with success
		if($run)
		{
			while ($model_row = mysqli_fetch_array($run))
			{
					$selected_models[] = $model_row[0];
			}
			//set form status to zero to hide div
			$form_status = -1;
			zoneChart($selected_models, $form_status, $database);
		}
		else
		{
			echo "<h3 class='error'>There was a problem with your request</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem connecting to the database</h3>";
	}

}

function bottomContent()
{
	echo "</div>";
	include ('./includes/footer.html');
}
?>
