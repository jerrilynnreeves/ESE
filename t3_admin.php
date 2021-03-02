<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  t3_admin.php
#	Version:  July 25, 2009
#	Purpose:  Administrator Interface for user addition, modification, and deletion

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
	include('./includes/functions.php');
	include ('./includes/header.php');

	//Open Database
	$database = db_connect();
	
	//See if User is granted access
	$grantedAccess = grantAccess ($access_level=3, $database);

	if ($grantedAccess == 1)
	{
		//get Help tool tip content
		echo "<script type=\"text/javascript\" src=\"./site/tooltip.js\"></script>";
		
		//Application Functions
		//After login Determine which links should be shown
		adminLinks($database);
		
		//Application Start USER ADMIN
		echo "<h1 class='app-label'>User Administration</h1>";
		echo "<h2>T3 Application User Administration</h2>";
		//If no session has been started
		//If the user clicks the Edit Link beside the User Information
		if (isset($_GET['edit']))
		{
			$type = 2;
			$id = $_GET['edit'];
			
			getEditForm($type, $id, $database);
			userTable($database);
		}
		//If the user clicks the Add User button from the User Administration Panel
		elseif (isset($_POST['add-user']))
		{
			$type = 1;
			userForm($type, "", "", "", "", $submit_status=0, $database);
			userTable($database);
		}
		//If the user clicks has click the Update (edit-user) button
		elseif (isset($_POST['edit-submit']))
		{
			//Run Update Query
				$errorStatus = 0;
				//Check email address
				if (validateEmail($_POST['email']) == 0)
				{
					echo "<h3 class='error'>Incorrect email address was supplied</h3>";
					$errorStatus = 1;
					userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "", $submit_status=1, $database);
					userTable($database);
				}
				//If the email address is correct
				if ($errorStatus == 0)
				{
						updateUserData($_POST['user_id'], $_POST['email'], $_POST['name'], $_POST['privileges'], $database);
						userTable($database);
				}
		}
		//Reset Password Request
		//Show user form with the password field
		elseif(isset($_POST['reset-request']))
		{
			$type = 3;
			$id = $_POST['user_id'];
			getEditForm($type, $id, $database);
			userTable($database);
		}
		//If the user has reset (can update information here as well)
		elseif(isset($_POST['change-password']))
		{
			$errorStatus =0;
			//if the password is less than 5 characters
			if (strlen($_POST['password']) < 5)
			{
				echo "<h3 class='error'>Your password must be five characters or greater </h3>";
				$errorStatus = 1;
				userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "", $submit_status=1, $database);
				userTable($database);
			}
			if ($errorStatus ==0)
			{
				//Run update query
				updatePassword($_POST['user_id'], $_POST['email'], $_POST['name'], $_POST['privileges'], $_POST['password'], $database);
				userTable($database);
			}
		}
		//If the user clicks Add Button from the User Modification Form
		elseif (isset($_POST['add-submit']))
		{
			//See if the email address exists in the database (>0)
			$same_user = isSameUser($_POST['email'], $database);
			//Set the error status to 0 Change to 1 if error condition occurs
			$errorStatus = 0;
			//if email, name, password, or privs have been set
			if ((!empty($_POST['email'])) and (!empty($_POST['name'])) and (!empty($_POST['privileges'])) and (!empty($_POST['password'])))
			{
				//Check email address to see it is a valide email
				if (validateEmail($_POST['email']) == 0)
				{
					echo "<h3 class='error'>Incorrect email address was supplied</h3>";
					$errorStatus = 1;
					userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "", $submit_status=1, $database);
					userTable($database);
				}
				//if the password is too short
				if (strlen($_POST['password']) < 5)
				{
					echo "<h3 class='error'>Your password must be five characters or greater </h3>";
					$errorStatus = 1;
					userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "", $submit_status=1, $database);
					userTable($database);
				}
				//If the user was found in the database
				if ($same_user > 0)
				{
					echo "<h3 class='error'>This user email address is already in use</h3>";
					$errorStatus = 1;
					userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "",$submit_status=1, $database);
					userTable($database);				
				}
				//Add the user if the error status is 0 
				if ($errorStatus == 0)
				{
					addUser($_POST['email'], $_POST['name'], $_POST['privileges'], $_POST['password'], $database);	
					userTable($database);
				}
			}
			else //Something is missing from the form submitted
			{
				echo "<h3 class='error'>There was missing data</h3>";
				userForm($_POST['type'], $_POST['email'], $_POST['name'], $_POST['privileges'], "", $submit_status=1, $database);
				userTable($database);
			}
		}
		
		//If the user clicks the delete link from the User Table
		elseif (isset($_GET['delete']))
		{
			$user_id = $_GET['delete'];
			confirmDeleteForm($user_id, $database);
			userTable($database);
		}
		//If the user clicks the Delete button from the confirmDelete Form
		elseif (isset($_POST['confirm-delete']))
		{
			//Run Delete Query
			deleteUser($_POST['user_id'], $database);
			userTable($database);
		}
		//If the user clicks the Cancel button from the confirmDelete Form
		elseif (isset($_POST['cancel-delete']))
		{
			echo "<h3 class='error'>You terminated the deletion process. User was not deleted.</h3";
			userTable($database);
		}
		//If nothing has been clicked -- last option
		else
		{
			userTable($database);
		}
	}
	else
	{	
		echo "<h3 class='error'>You do not have permission to access this page</h3>";
		echo "<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
	}


} // end else session
mysqli_close($database); //Close database
echo "</div></div></div>"; //end content containers
include ('./includes/footer.html');
	
###################################################################
#                      Data Functions                             #
###################################################################
	
//Updates user information in the database
function updateUserData($user_id, $email, $name, $privileges, $database)
{
	//Create a single string for privileges
	$privs = '';
	foreach ($privileges as $p)
	{
		$privs .= $p;
	}
	
	if ($database)
	{
		//set-up update query
		$updateUser_query = "UPDATE usersdata SET email='$email', name='$name', privileges='$privs' WHERE user_id=$user_id LIMIT 1";
		//Run Query
		$run = @mysqli_query ($database, $updateUser_query);
			
		//If the query ran 
		if ($run) 
		{ 
			echo "<h3>The user's data is now: </h3>";
			echo "<p>User: ".$name."</p>";
			echo "<p>Email: ".$email."</p>";
			echo "<p>Privileges: ".$privs."</p>";
			echo "<hr />";	
		}
		else
		{
			echo "<h3 class='error'>There was a problem adding your user to the database.</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request</h3>";
	}
}
	
//Update password
function updatePassword($user_id, $email, $name, $privileges, $password, $database)
{
	//Create a single string for privileges
	$privs = '';
	foreach ($privileges as $p)
	{
		$privs .= $p;
	}
		
	if ($database)
	{
		//set-up update query
		$updatePassword_query = "UPDATE usersdata SET email='$email', name='$name', privileges='$privs', password = SHA1('$password') WHERE user_id=$user_id LIMIT 1";
		//Run Query
		$run = @mysqli_query ($database, $updatePassword_query);
		
		//If the query ran 
		if ($run) 
		{ 
			echo "<h3>The user's data is now: </h3>";
			echo "<p>User: ".$name."</p>";
			echo "<p>Email: ".$email."</p>";
			echo "<p>Privileges: ".$privs."</p>";
			echo "<p>Password: CONFIDENTIAL</p>";
			echo "<hr />";	
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

//Adds a user to the database
function addUser($email, $name, $privileges, $password, $database)
{
	//Create a single string for privileges
	$privs = '';
	foreach ($privileges as $p)
	{
		$privs .= $p;
	}
		
	if ($database)
	{
		//set-up query
		$addUser_query = "INSERT INTO usersdata (email, name, password, privileges) VALUES ('$email', '$name', SHA1('$password'), $privs)";		
	
		//echo $addUser_query;
		
		//Run Query
		$run = @mysqli_query ($database, $addUser_query); // Run the query.
		
		if ($run) 
		{ 
			echo "<h3>The following user information was added</h3>";
			echo "<p>User: ".$name."</p>";
			echo "<p>Email: ".$email."</p>";
			echo "<p>Privileges: ".$privs."</p>";
			echo "<hr />";	
		}
		else
		{
			echo "<h3 class='error'>There was a problem adding your user to the database.</h3>";
		}
	}
	else
	{
		echo "<h3 sclass='error'>There was a problem with your request</h3>";
	}

}

//Deletes the user	
function deleteUser($user_id, $database)
{
	//Get the name and email address of the user	
	//If the database is opened
	if($database)
	{
		//set-up query
		$findUser_query = "SELECT user_id, name FROM usersdata WHERE user_id=".$user_id;
		//Run Query
		$run = @mysqli_query ($database, $findUser_query); // Run the query.
		
		//if the query ran
		if ($run) 
		{
			while ($user_row = mysqli_fetch_array($run))
			{
				//set-up query
				$deleteUser_query = "DELETE FROM usersdata WHERE user_id=".$user_id;
				//Run Query
				$runDelete = @mysqli_query ($database, $deleteUser_query); // Run the query.
				
				if($runDelete)
				{
					echo "<p>The user ".$user_row[1]." has been deleted</p>";
				}
				else
				{
					echo "<h3 class='error'>There was an error deleting the user.  Please try again</h3>";
				}
			}
		}
		else
		{
			echo "<h3 class='error'>There was an error with your request</h3>";
		}
	}
	else
	{
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
}	

###################################################################
#                    HTML/TABLE Functions                         #
###################################################################
//Returns the items in a selection menu that were picked in a prior form
function checkPrivSelection($picked, $totest)
{
	//Look for the charter in the string
	//if the character is found then the selection was picked
	foreach ($picked as $p)
	{
		if ($totest == $p)
		{
			return "selected=' '";		
		}
	}
}

//turns numeric privileges to words	
function privilegesToWords($privileges)
{
	//convert privileges to an array
	$privs_array =  str_split($privileges);
	$privs ='';
	$count = 0;
	$array_count = count($privs_array);
	foreach ($privs_array as $p)
	{	
		$privs = $privs.getPrivString($p);
		$count = $count + 1;
		//do we need to add a comma
		if (($count > 0) and ($count < $array_count))
		{
			$privs = $privs.", ";
		}
	}
	return $privs;
}

function getPrivString($value)
{
	if ($value == 0)
	{
		return "Standard";
	}
	elseif ($value == 1)
	{
		return "Data Manager";
	}
	elseif ($value == 2)
	{
		return "Test Data Manager";
	}
	elseif ($value == 3)
	{
		return "Administrator";
	}
}

//Populates userForm with the user information requested for modification
function getEditForm($type, $id, $database)
{		
	if($database)
	{
		//set-up query
		$findUser_query = "SELECT user_id, email, name, privileges FROM usersdata WHERE user_id=".$id;
		
		//Run Query
		$run = @mysqli_query ($database, $findUser_query); // Run the query.
	
		if ($run) 
		{
			while ($user_row = mysqli_fetch_array($run))
			{
				$user_id = $user_row[0];
				$email = $user_row[1];
				$name= $user_row[2];
				$privileges = str_split($user_row[3]);
			}
			 userForm($type, $email, $name, $privileges, $user_id, $submit_status=0, $database);	
		}
		else
		{
			echo "<h3 class='error'>There was an error with your request</h3>";
		} 
	}
	else
	{
		echo "<h3 class='error'>There was a problem with your request</h3>";
	}
}

//Displays a form for User edits, addditions, and Password Changes
function userForm($type, $user_email, $user_name, $user_privileges, $user_id, $submit_status, $database)
{	
	//always have password unset when form is submitted
	$password='';
	
	//If the form has been submitted
	//check email for wrong address so we can throw color error to user
	if ($submit_status > 0)
	{
		if(validateEmail($user_email) == 0)
		{
			$SpecialError_status = -1;
		}
	}
	
	//Form Data
	echo "<form  action='t3_admin.php' method='post' id='user-form' name='user-form'>";
	echo "<fieldset>";
	echo "<legend id='label-userForm'>&nbsp;Add/Modify User&nbsp;</legend>";
	echo "<input type='hidden' name='type' id = 'type' value='".$type."'>";
	echo "<input type='hidden' name='user_id' id = 'user_id' value='".$user_id."'>";
	echo "<p><label id='label-email' for='email' class='left'><strong ".formatMissing($SpecialError_status, $user_email).">Email Address: </strong></label>";
	echo "<input type='text' name='email' id='email' class='field' value='".$user_email."'/></p>";
	echo "<p><label id='label-name' for='name' class='left'><strong ".formatMissing($submit_status, $user_name).">Name: </strong></label>";
	echo "<input type='text' name='name' id='name' class='field' value='".$user_name."'/></p>";
	echo "<p><label id='label-privileges' for='privileges' class='left'><strong ".formatMissing($submit_status, $user_privileges).">Privileges: </strong></label>";
	echo "<select name='privileges[]' id='privileges[]' size='4' multiple>";
	echo "<option ".checkPrivSelection($user_privileges, 0)." value='0'>Standard</option>";
	echo "<option ".checkPrivSelection($user_privileges, 1). " value='1'>Data Administration</option>";
	echo "<option ".checkPrivSelection($user_privileges, 2). " value='2'>Test Data User</option>";
	echo "<option ".checkPrivSelection($user_privileges, 3). "value='3'>Administrator</option>";
	echo "</select></p>";
	//If Add user or Change Password has been requested show the password field
	if (($type == 1) or ($type == 3))
	{    	
		//Password Field
		echo "<p><label id='label-password' for='password' class='left'><strong ".formatMissing($submit_status, $password)." >Password: </strong></label>";
		echo "<input type='password' name='password' id='password' class='field' value=''/></p>";
		//Action Buttons
		echo "<p>";
		//If Add User -- Show Add Button
		if ($type == 1)
		{
			echo "<input type='submit' name='add-submit' id='add-submit' class='button' value='Add' style='margin-left: 119px'/>";
		}
		//Password Reset has been requested
		elseif ($type==3)
		{
			echo "<input type='submit' name='change-password' id='change-password' class='button' value='Commit Changes' style='margin-left: 119px'/>";
		}
	 }
	 //If Edit User has been requested
	 if ($type == 2)
	 {
		echo "<input type='submit' name='edit-submit' id='edit-submit' class='button' value='Commit Changes' style='margin-left: 119px'/>";       
		echo "<input type='submit' name='reset-request' id='reset-request' class='button' value='Password Reset'/>";
	 }
	 echo "</p>";
	 echo "</fieldset> </form>";
}
	
//Confirms or Cancels the Delete of a User
function confirmDeleteForm($user_id, $database)
{	
	//If the database is opened
	if($database)
	{
		//set-up query
		$findUser_query = "SELECT user_id, email, name FROM usersdata WHERE user_id=".$user_id;
		//Run Query
		$run = @mysqli_query ($database, $findUser_query); // Run the query.
		
		//if the query ran
		if ($run) 
		{
			while ($user_row = mysqli_fetch_array($run))
			{
				echo "<form action='t3_admin.php' method='post' id='user-form' name='user-form'>";
				echo "<fieldset>";
				echo "<legend>&nbsp;Delete User Confirmation&nbsp;</legend>";
				echo "<p>Please Confirm the Deletion of the following user:</p>";
				echo "<input type='hidden' name='user_id' id = 'user_id' value='".$user_row[0]."'>";
				echo "<p>".$user_row[1]."</p>";
				echo "<p>".$user_row[2]."</p>";
				echo "<input type='submit' name='confirm-delete' id='confirm-delete' class='button' value='Confirm'/>";           
				echo "<input type='submit' name='cancel-delete' id='cancel-delete' class='button' value='Cancel'/>";
				echo "</fieldset> </form>";
				}
			}
			else
			{
				echo "<h3 class='error'>There was a problem with your Delete Request</h3>";
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem with the Database</h3>";
		}
	} 

//Displays a list of registered users
function userTable($database)
{
	//If the database is opened
	if ($database)
	{
		//Setup query
		$displayUsers_query = "SELECT user_id, email, name, privileges FROM usersdata ORDER BY email";
		//Send query to database
		$run = @mysqli_query ($database, $displayUsers_query);	
						
		//Set-up table and form
		echo "<form action='t3_admin.php' method='post' id='user-admin' name='user-admin'>";
		echo "<fieldset>";
		echo "<legend id='label-userAdmin'>&nbsp;User Administration&nbsp;</legend>";
		echo "<input name='add-user' type='submit' value='Add User' class='button' />";
		echo "<table id='user-table' border='0' cellspacing='0' cellpadding='0'>";
		echo "<tr>";
		echo "<td class='table-name' colspan='5'>Modify Existing User</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<th colspan='2' scope='col'>Actions</th>";
		echo "<th scope='col'>Email</th>";
		echo "<th scope='col'>Name</th>";
		echo "<th scope='col'>Privileges</th>";	
		echo "</tr>";
		
		//user_row[0] = user_id     //user_row[1] = email     //user_row[2] = name     //user_row[3] = privileges	
		
		while ($user_row = mysqli_fetch_array($run))
		{
			$privs = privilegesToWords($user_row[3]);	
			echo "<tr class='user-record'>";
			echo "<td><a href='t3_admin.php?edit=".$user_row[0]."'>Edit</a></td>";
			echo "<td><a href='t3_admin.php?delete=".$user_row[0]."'>Delete</a></td>";
			echo "<td>".$user_row[1]."</td>";
			echo "<td>".$user_row[2]."</td>";
			echo "<td>".$privs."</td>";
			echo "</tr>";
		}
		//Close table and Form
		echo "</table></fieldset></form>";
	}
}
?>  

