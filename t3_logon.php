<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  t3logon.php
#	Version:  July 25, 2009
#	Purpose:  Logon and Logout functions for the T3 Application

session_start();  //Start the session
ob_start();

if (isset($_POST['logon-submit']))
{
	validateLogon($_POST['login-name'], $_POST['login-password']);
}
elseif ($_GET['logout'])
{
	$url = 	$_SESSION['requesting-page'];
	header("Location: $url"); 
	logout();
	ob_end_flush();
	exit();
}
else
{
	topContent();
	loginForm($_SESSION['requesting-page']);
	bottomContent();
}
###################################################################
#                      HTML Functions                             #
###################################################################

function loginForm ()
{
	echo "<div id='app-login'>";
	//Output login message function	depending on requesting URL
	echo "<form action='t3_logon.php' method='post' id='login-form' >";
	echo "<fieldset>";
	echo "<legend>&nbsp;T3 Application Login&nbsp;</legend>";
	echo "<p><label for='login-name' class='left'>User Name</label>";
	echo "<input type='text' name='login-name' id='login-name' class='field value='' tabindex='1'/>";
 	echo "</p>";
	echo "<p><label for='login-password' class='left'>Password</label>";
	echo "<input type='password' name='login-password' id='login-name' class='field' tabindex='2' value=''/>";
	echo "</p>";
	echo "<p class='forgot'><a href='ese_contact.php?type=5'>Forgot Password</a></p>";
	echo "<p><input type='submit' name='logon-submit' id='logon-submit' class='button' value='Login' tabindex='3' />";
	echo "</p>";
	echo "</fieldset>";
   echo "</form>";
	echo "</div>";	
}

function topContent()
{
	//include('./includes/functions.php');
	include ('./includes/header.php');

	
	//Application Start USER ADMIN
	echo "<h1>Authorization Required</h1>";
	echo "<h2>Please login to access this page</h2>";
	echo "<p>&nbsp;</p>";	
}

function bottomContent()
{
	echo "</div>";
	echo "</div>";
	include ('./includes/footer.html');
}

###################################################################
#                      DATA Functions                             #
###################################################################

function validateLogon($user, $password)
{
	include('./includes/functions.php');
	//open database
	$database = db_connect();
	
	if (empty($user) or empty($password))
	{
		topContent();	
		echo "<h3 class='error'>There was an error with your request</h3>";
		loginForm();
		bottomContent();
	}	
	elseif ($database)
	{
		//set-up update query
		$findUser_query = "SELECT user_id, email, name, privileges, password FROM usersdata WHERE email='$user'";
		//Run Query
		$run = @mysqli_query ($database, $findUser_query);
		$num_rows = mysqli_num_rows($run);
		
		if ($num_rows > 0)
		{
			while ($user_row = mysqli_fetch_array($run))
			{
				$password = sha1($password);
				if ($password == $user_row[4])
				{
					$_SESSION['user_id'] = $user_row[0];
					$_SESSION['name'] = $user_row[2];
					$url = $_SESSION['requesting-page'];
					header("Location: $url"); 
	 				ob_end_flush();
					exit();
				}
				else
				{
					topContent();
					echo "<h3 class='error'>Either your username or password is incorrect.  Please try again.</h3>";
					loginForm();
					bottomContent();
				}
			}
		}
		else
		{
			topContent();
			echo "<h3 class='error'>Either your username or password is incorrect.  Please try again.</h3>";
			loginForm();
			bottomContent();
		}
	}
	else
	{
		topContent();	
		logonForm();
		bottomContent();			
	}		
}

function logout()
{
	session_unset();
	session_destroy();
}
?>  

