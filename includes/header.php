<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  header.php
#	Version:  July 25, 2009
#	Purpose:  HTML header, beginning page containers, and logon status indicator for all site pages

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
echo "<title>TerraStreamer Early Streamer Emission Terminals (ESE)</title>";
echo "<link href=\"site/t3.css\" rel=\"stylesheet\" type=\"text/css\" />";
echo "<script type=\"text/javascript\" src=\"./site/jquery.js\"></script>";
echo "<script type=\"text/javascript\" src=\"./site/infinitecarousel.js\"></script>";
echo "<script type=\"text/javascript\" src=\"./site/wtooltip.js\"></script>";
echo "<script type=\"text/javascript\" src=\"./site/terrastreamer.js\"></script>";

echo "<!--[if gt IE 6]><link href=\"site/t3-7.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->";
echo "<!--[if IE 8]><link href=\"site/t3.css\" rel=\"stylesheet\" type=\"text/css\" /><![endif]-->";
echo "</head>";
echo "<body>";
echo "<div id=\"wrapper\">";
echo "<div id=\"page-container\">";
echo "<!--Start Header Container-->";
echo "<div id=\"top-header\">";
echo "<!--Logo Area-->";
echo "<div id=\"logo\">";
echo "<h1>TerraStreamer</h1>";
echo "<h2>Early Streamer Emitter Terminals (ESE)</h2>";
echo "<span><img src=\"site/logo.png\" alt=\"TerraStreamer Early Streamer Emitter Terminals\" /> </span>";
echo "</div>";

logonStatus();

echo "<!--Navigation Div-->";
echo "<div id=\"navigation\">";
echo "<ul>";
echo "<li><a href=\"index.php\">Home</a></li>";
echo "<li><a href=\"ese_documentation.php\">Documentation</a></li>";
echo "<li><a href=\"t3_app.php\">T3 Application</a></li>";
echo "<li><a href=\"ese_contact.php\">Contact</a></li>";
echo "</ul>";
echo "</div>";
echo "</div>";

function logonStatus()
{
	echo "<!--Logon Status-->";
	echo "<div id=\"status\">";
	echo "<ul>";
	echo "<li>Welcome <strong>";
	//If username is not set show guest and login 
	if (!isset($_SESSION['name']))
	{
		echo "Guest</strong></li>";
        echo "<li> <a href=\"t3_logon.php\">&raquo; Login </a></li>";
	}
	else
	{
		echo $_SESSION['name']."</strong></li>";
        echo "<li> <a href=\"t3_logon.php?logout=1\">&raquo; Logout </a></li>";
	}
    echo "</ul> </div>";
}
//begin content containers
	echo "<div id='content-container'>";
	echo "<div id='content'>";
?>
