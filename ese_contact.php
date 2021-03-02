<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  ese_contact.php
#	Version:  July 25, 2009
#	Purpose:  Email Contact Form Interface

session_start();  //Start the session
ob_start();
$_SESSION['requesting-page'] = $_SERVER["REQUEST_URI"];
//set variables
include('./includes/functions.php');
include ('./includes/header.php');

echo "<h1 class='app-label'>Contact Alltec Corporation</h1>";
echo "<h2>TerraStreamer Early Streamer Emission Terminals</h2>";


//if the contact form has been submitted
if (isset($_POST['submit']))
{
	check_before ($request_type=0);
}
elseif (isset($_GET['type']))
{
	$request_type = $_GET['type'];
	displayEmailForm('','','','','','','', '', '','','','',$contact_type=$request_type, '', $submit_status=0);
}
//display the form
else
{
echo "<p>Thank you for your interest in TerraStreamer Early Emission Terminals.  Please fill out the contact form below and click on submit.  An Alltec representative will contact you shortly.</p>";
	//do something else
	displayEmailForm('','','','','','','', '', '','','','','', '', $submit_status=0);
}
bottomContent();

####################################
#       EMAIL FORM FUNCTIONS       #
####################################

function check_before ($request_type)
{
	$recipient = "online-info@allteccorp.com";
	 //referers domains/ips that you will allow forms to reside on.
	$referers = array ('localhost', 'ese-terminals.com','www.ese-terminals.com', 'www.allteccorp.net');
	
	// field / value seperator
	define("SEPARATOR", ($separator)?$separator:": ");

	// content newline
	define("NEWLINE", ($newline)?$newline:"\n");

	
	//Check to make sure it is from a valid referrer
	//$checkRef = check_referer($referers); 
	
	//Check the validity of email address
	$recipient_to_test = $_POST['email'];
   	
   	//If the required fields are blank set error to 1
	$is_error = 0;
	if ((empty($_POST['contact_firstname'])) or (empty($_POST['contact_familyname'])) or (empty($_POST['subject'])) or (empty($_POST['contact-type'])))
	{
		echo "<h3 class='error'>Your form contained missing data.  Please correct the items labeled in red.</h3>";
		$is_error = $is_error + 1;
	}	
	//If the email is not correct
	if ((!eregi("^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,6}$", $recipient_to_test)) )
	{
		echo "<h3 class='error'>Please enter a valid email address</h3>";
		$is_error =  $is_error + 1;
	}
    //if there is an error post the form back	
	if ($is_error > 0)
	{
		displayEmailForm($_POST['contact_company'], $_POST['contact_firstname'], $_POST['contact_familyname'], $_POST['contact_street'], $_POST['contact_postalcode'], $_POST['contact_city'], $_POST['contact_state'], $_POST['contact_country'], $_POST['contact_phone'], ' ', $_POST['contact_url'], $_POST['subject'], $request_type, $_POST['message'], $submit_status=1);
   	}

	else
	{
		//prepare and send the content
		if ($sort == "alphabetic") 
		{
   			uksort($HTTP_POST_VARS, "strnatcasecmp");
		} 
		elseif ((ereg('^order:.*,.*', $sort)) && ($list = explode(',', ereg_replace('^order:', '', $sort)))) 
		{
   			$sort = $list;
		}
		
		$content = parse_form($_POST, $sort);
		$email = $_POST['email'];
		mail_it($content, $email, $recipient);
		echo "<h1>Thank You! Your request was sent.</h1>";
		//echo "<p>The Content Is:  ".$content."</p>";
	}
}

//prepares email content 
function mail_it($content, $email, $recipient) 
{  
   $headers = 'From: online-info@allteccorp.com' . "\r\n" .
    'Reply-To: online-infor@allteccorp.com.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
   $message .= $content."\n\n";
   
   mail($recipient, "TerraStreamer Inquiry", $message, $headers);
}

//parses the form for content
function parse_form($array, $sort = "") 
{
   if (count($array)) 
   {
      if (is_array($sort)) 
	  {
         foreach ($sort as $field) 
		 {
         	if (is_array($array[$field])) 
			{
				for ($z=0;$z<count($array[$field]);$z++)
                     $content .= $field.SEPARATOR.$array[$field][$z].NEWLINE;
            } 
			else
			{
                  $content .= $field.SEPARATOR.$array[$field].NEWLINE;
            }
         }
      }
      while (list($key, $val) = each($array)) 
	  {
	  	// prepare content
        if (is_array($val)) 
		{
			for ($z=0;$z<count($val);$z++)
				$content .= $key.SEPARATOR.$val[$z].NEWLINE;
        } 
		else
               $content .= $key.SEPARATOR.$val.NEWLINE;
      	}
   }
   return $content;
}

//checks domain from which the email was sent
function check_referer($referers) 
{
   if (count($referers)) 
   {
      $found = false;

      $temp = explode("/",getenv("HTTP_REFERER"));
      $referer = $temp[2];
      
      if ($referer=="") {$referer = $_SERVER['HTTP_REFERER'];
         list($remove,$stuff)=split('//',$referer,2);
         list($home,$stuff)=split('/',$stuff,2);
         $referer = $home;
     }
      
      for ($x=0; $x < count($referers); $x++) 
	  {
         if (eregi ($referers[$x], $referer)) 
		 {
            $found = true;
         }
      }
      
	  if ($referer =="")
         $found = false;
      if (!$found)
	  {
         echo "<p class='error'>You are coming from an unauthorized domain.</p>";
      }
         return $found;
      } 
	  else 
	  {
	  	return true; // not a good idea, if empty, it will allow it.
   		}
		return true;
}

//emai form
function displayEmailForm($company, $first_name, $last_name, $address, $zip, $city, $state, $country, $phone, $email, $website, $subject, $contact_type, $message, $submit_status)
{
	echo "<div class='contact'>";
	echo "<div class='contactform'>";
	echo "<form id='contact-form' name='contact-form' action='ese_contact.php' method='post'>";
	echo "<fieldset><legend>&nbsp;CONTACT DETAILS&nbsp;</legend>";
	echo "<p><label for='contact_company' class='left'>Company:</label>";
    echo "<input type='text' name='contact_company' id='contact_company' class='field' value='".$company."' tabindex='1' /></p>";
	echo "<p ".formatMissing($submit_status, $first_name)."><label for='contact_firstname' class='left-req'>First name:</label>";
	echo "<input type='text' name='contact_firstname' id='contact_firstname' class='field' value='".$first_name."' tabindex='2' /></p>";	
	echo "<p ".formatMissing($submit_status, $last_name)."><label for='contact_name' class='left-req'>Last name:</label>";
	echo "<input type='text' name='contact_familyname' id='contact_familyname' class='field' value='".$last_name."' tabindex='3' /></p>";
	echo "<p><label for='contact_street' class='left'>Address:</label>";
	echo "<input type='text' name='contact_street' id='contact_street' class='field' value='".$address."' tabindex='4' /></p>";
	echo "<p><label for='contact_postalcode' class='left'>Postal code:</label>";
	echo "<input type='text' name='contact_postalcode' id='contact_postalcode' class='field' value='".$zip."' tabindex='5' /></p>";
	echo "<p><label for='contact_city' class='left'>City:</label>";
	echo "<input type='text' name='contact_city' id='contact_city' class='field' value='".$city."' tabindex='6' /></p>";
	echo "<p><label for='contact_country' class='left'>State:</label>";
	echo "<label><input name='contact_state' type='text' class='field' id='contact_state' value ='".$state."'tabindex='7' /></label></p>";
	echo "<p><label for='contact_country' class='left'>Country:</label>";
	echo "<label><input name='contact_country' type='text' class='field' id='contact_country' value ='".$country."'tabindex='7' /></label></p>";
	echo "<p><label for='contact_phone' class='left'>Phone:</label>";
	echo "<input type='text' name='contact_phone' id='contact_phone' class='field' value='".$phone."' tabindex='8' /></p>";
	echo "<p ".formatMissing($submit_status, $user_name)."><label for='contact_email' class='left-req'>Email:</label>";
	echo "<input type='text' name='email' id='email' class='field' value='".$email."' tabindex='9' /></p>";
	echo "<p><label for='contact_url' class='left'>Website:</label>";
	echo "<input type='text' name='contact_url' id='contact_url' class='field' value='".$website."' tabindex='10' /></p>";
	echo "<p class='notice-req'>Underlined labels denote required fields</p>";
	echo "</fieldset>";
	//Message
	echo "<fieldset><legend>&nbsp;MESSAGE DETAILS&nbsp;</legend>";
	echo "<p ".formatMissing($submit_status, $subject)."><label for='subject' class='left-req'>Subject:</label>";
    echo "<input type='text' name='subject' id='subject' class='field' value='".$subject."' tabindex='11' /></p>";
	//request type
	echo "<p ".formatMissing($submit_status, $conact_type)."><label for='contact-type' class='left-req'>Request Type:</label></p>";
	echo "<select name='contact-type' id='contact-type'>";
	echo "<option ".checkSelection($contact_type, 0)." value='0'></option>";
	echo "<option ".checkSelection($contact_type, 'Sales')." value='Sales'>Sales/Pricing</option>";
	echo "<option ".checkSelection($contact_type, 'Technical')." value='Technical'>Techincal Data</option>";
	echo "<option ".checkSelection($contact_type, 'Installation')." value='Installation'>Installation</option>";
	echo "<option ".checkSelection($contact_type, 'Access')." value='Access'>T3 App Authorization</option>";
	echo "<option ".checkSelection($contact_type, 'Password')." value='Password'>Forgotten Password</option>";
	echo "</select>";
	
	echo "<p ".formatMissing($submit_status, $message)."><label for='message' class='left-req'>Message:</label>";
	echo "<textarea name='message' id='message' cols='45' rows='10' tabindex='12' wrap='virtual'>".trim($message)."</textarea></p>";
	echo "<p class='center'><input type='submit' name='submit' id='submit' class='button' value='Send message' tabindex='12' /></p>";
	echo "</fieldset>";
	echo "</form>";
	echo "</div>";
}
function bottomContent()
{
	echo "</div>";
	echo "</div>";
	echo "</div>";
	include ('./includes/footer.html');
}
?>