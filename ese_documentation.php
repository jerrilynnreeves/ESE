<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  ese_documentation.php
#	Version:  July 25, 2009
#	Purpose:  Documentation Interface.  Includes add/delete interface for logged in administrative users

session_start();  //Start the session
ob_start();
$_SESSION['requesting-page'] = $_SERVER["REQUEST_URI"];
include('./includes/functions.php');
include ('./includes/header.php');

	//Open Database
	$database = db_connect();

echo "<h1>Documentation Center</h1>";
echo "<h2>Marketing &amp; Technical Documentation for Download</h2>";

//Show the document Table
documentTable($database);
mysqli_close($database); //Close the public connection to the database

###################################################################
#    PRIVATE / DOCUMENT ADMINISTRATION Function DISPLAY           #
###################################################################

//If the user is logged in
if (isset($_SESSION['user_id']))
{
	$admin_database = db_connect();
	//See if User is granted access
	$grantedAccess = grantAccess ($access_level=3, $admin_database);
	
	//If they are granted access
	if ($grantedAccess == 1)
	{
		//see if upload has been requested
		if (isset($_POST['upload-submit']))
		{
			if (empty($_POST['doc-name']) or empty($_POST['doc-description']))
			{
				echo "<h3 class='error'>You must supply both a name and description for the file you wish to upload.</h3>";
				documentManagement($_POST['doc-name'], $_POST['doc-description'], $admin_database);
			}
			else
			{
				echo "<h3>Upload Requested</h3>";
				//if a file has been submitted
				if (isset($_FILES['upload'])) 
				{
					//allowed file type uploads
					$allowed = array ('image/pjpeg', 'image/jpeg', 'image/JPG', 'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png', 'application/x-zip-compressed', 'application/zip', 'text/plain', 'application/octet-stream', 'application/msword', 'application/pdf', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'audio/mpeg', 'audio/x-aiff', 'image/tiff', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'application/acad', 'application/x-acad', 'application/autocad_dwg', 'image/x-dwg', 'application/dwg', 'application/x-dwg', 'application/x-autocad', 'image/vnd.dwg', 'drawing/dwg', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation','image/photoshop', 'image/x-photoshop', 'image/psd', 'application/photoshop', 'application/psd');
					
					//check to see if document type is in the allowed list
					if (in_array($_FILES['upload']['type'], $allowed)) 
					{
						//Move the file & add information to the database
						if (move_uploaded_file ($_FILES['upload']['tmp_name'], "./files/{$_FILES['upload']['name']}")) 
						{
							//if there was an error print it out
							if ($_FILES['upload']['error'] > 0)
							{
								uploadError($_FILES['upload']['error']);
								documentManagement($name='', $description='', $admin_database);
							}
							else
							{
								$file_name = $_FILES['upload']['name'];
								addFile($_POST['doc-name'], $_POST['doc-description'], $file_name, $admin_database);
								documentManagement($name='', $description='', $admin_database);
							}
						}
						else
						{
								echo "<h3 class='error'>There was an error uploading the file</h3>";
								uploadError($_FILES['upload']['error']);
								documentManagement($name='', $description='', $admin_database);
						}
						
					} 
					else 
					{
						echo "<h3 class='error'>Unsupport File Type Upload</h3>";
						documentManagement($name='', $description='', $admin_database);
					}
					// Delete the file if it still exists:
					if (file_exists ($_FILES['upload']['tmp_name']) && is_file($_FILES['upload']['tmp_name']) ) 
					{
						unlink ($_FILES['upload']['tmp_name']);
					}
				}
			}
		}
		elseif (isset($_POST['delete-submit']))
		{
			$id = $_POST['delete-name'];
			docDeleteConfirmForm($id, $admin_database);
			documentManagement($name='', $description='', $admin_database);
		}
		elseif (isset($_POST['confirm-delete']))
		{
			$id = $_POST['document-id'];
			deleteFile($id, $admin_database);
		}
		elseif (isset($_POST['cancel-delete']))
		{
			echo "<h3 class='error'>The Delete process has been cancelled by the user</h3>";
			documentManagement($name='', $description='', $admin_database);
		}
		else
		{
			documentManagement($name='', $description='', $admin_database);
		}
	}
}
//do not display any error message. We do not want someone to know there is an adminstrative panel here
		
		
echo "</div>";
echo "</div>";
include ('./includes/footer.html');

###################################################################
#                      DATA Functions                             #
###################################################################

//adds a file
function addFile($name, $description, $file_name, $admin_database)
{
	//the the database opens
	if ($admin_database)
	{
		//set-up query
		$addDocument_query = "INSERT INTO documents (document_name, description, file_name) VALUES ('$name', '$description', '$file_name')";	
		//echo "<p>".$addDocument_query."</p>";		
		//Run Query
		$run = @mysqli_query ($admin_database, $addDocument_query); // Run the query
		
		//if the query ran
		if($run)
		{
			echo "<h3>The document ".$document_name." (".$file_name.") has been uploaded</h3>";
			echo "<p>Please refresh your browser window to view file in the table above</p>";
		}
		else
		{
			echo "<h3 class='error'>There was an error with the document</h3>";
		}
	}
	else
	{
		echo "<h3 class'error'>There was a problem with the database</h3>";
	}
}

//deletes a file
function deleteFile($id, $admin_database)
{
	//Delete file from the database
	if($admin_database)
	{		
		//set-up query
		$deleteDocument_query = "DELETE FROM documents WHERE document_id=".$id;
		//echo "<p>".$deleteDocument_query."</p>";
		//Run Query
		$runDelete = @mysqli_query ($admin_database, $deleteDocument_query);

		//If the query ran
		if ($runDelete)
		{
			echo "<h3>The document was delete successfully</h3>";
			
			// Delete the file from the server
			$file_name = $setup["./files/"] . $fileName;
			if (file_exists ($file_name) && is_file($file_name) ) 
			{
				unlink ($file_name);
			}
			documentManagement($name='', $description='', $admin_database);
		}
		else
		{
			echo "<h3 class='error'>There was a problem deleting the document</h3>";
		}
	}
	else
	{	
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
}

function uploadError($error)
{
	//Print out error messages
	switch ($error) 
	{
		case 1:
			echo "<h3 class='error'>The file exceeds the upload_max_filesize setting in php.ini.</h3>";
			break;
		case 2:
			echo "<h3 class='error'>The file exceeds the max file size</h3>";
			break;
		case 3:
			echo  "<h3 class='error'>The file was only partially uploaded.</h3>";
			break;
		case 4:
			echo "<h3 class='error'>No file was uploaded.</h3>";
			break;
		case 6:
			echo "No temporary folder was available.</h3";
			break;
		case 7:
			echo "<h3 class='error'>Unable to write to the disk.</h3>";
			break;
		case 8:
			echo "<h3 class='error'>File upload stopped.</h3>";
			break;
		default:
			echo "<h3 class='error'>A system error occurred.</h3>";
			break;
	} // End of switch.
}

//returns an icon for the document type to download
function getDocumentIcon($file_name)
{
	//get the documents extenstion
	//$char_count = strlen($file)-4;
	//return the extension
	$position = (strripos($file_name, "."))+1;
	$ext =  strtolower(substr($file_name, $position));
	$image_name = $ext.".png";
	return $image_name;
}
//Returns the document name given the record's id
function getDocumentName ($id, $admin_database)
{	
	if($admin_database)
	{
		//Set up search query
		$findDocumentName_query = "SELECT document_name FROM documents WHERE document_id=$id";
		//Run query
		$run = @mysqli_query ($admin_database, $findDocumentName_query); 
		
		//echo "<p>".$findDocumentName_query."</p>";
		
		if ($run)
		{
			while ($documentName_row = mysqli_fetch_array($run))
			{
				return $documentName_row[0];
			}
		}
		else
		{
			echo "<h3 class='error'>There was a problem retrieving the requested Document Name.</h3>";
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

//Document Delete Confirm or Cancel Form
function docDeleteConfirmForm($id, $admin_database)
{	
	//get the documents name
	$doc_name = getDocumentName($id, $admin_database);
	
	echo "<form action='ese_documentation.php' method='post' id='docDelete-form' name='docDelete-form'>";
    echo "<fieldset>";
    echo "<legend>&nbsp;Delete Document Confirmation&nbsp;</legend>";
    echo "<p>Please Confirm the Deletion of the following document:</p>";
    echo "<p class='data'><strong> Document Name: </strong>". $doc_name."<br />";
	echo "<input type='hidden' name='document-id' id='document-id' value='".$id."' />";
    echo "<p><input type='submit' name='confirm-delete' id='confirm-delete' class='button' value='Confirm'/>";
	echo "<input type='submit' name='cancel-delete' id='cancel-delete' class='button' value='Cancel'/></p>";
    echo "</fieldset>";
    echo "</form>";
}

//Displays the documentation management form
function documentManagement($name, $description, $admin_database)
{
	echo "<h1>Document Administration</h1>";
    echo "<form enctype='multipart/form-data' id='doc-mgmt' name='doc-mgmt' method='post' action='ese_documentation.php'>";
    echo "<fieldset>";
    echo "<legend>&nbsp;DOCUMENT MANAGEMENT&nbsp;</legend>";
    echo "<fieldset>";
    echo "<legend>&nbsp;ADD DOCUMENT&nbsp;</legend>";
    echo "<p><label for='doc-name' class='left'>Document Name:</label>";
    echo "<input type='text' name='doc-name' id='doc-name' class='field' value='".$name."' tabindex='1' /></p>";
    echo "<p><label for='doc-description' class='left'>Description:</label>";
    echo "<textarea name='doc-description' id='doc-description' cols='45' rows='10' tabindex='12' wrap='virtual'>".trim($description)."</textarea></p>";
    echo "<p><label for='file' class='left'>File:</label><input name='upload' type='file' class='field' />";
	echo "<input type='submit' name='upload-submit' id='upload-submit' class='button' value='Upload' tabindex='12' /></p>";
	echo "<p>For files that exceed 2 MB, please contact your webmaster.</p>";
    echo "</fieldset>";
    echo "<fieldset>";
	echo "<legend>&nbsp;DELETE DOCUMENT&nbsp;</legend>";
		//Query Documents
		$findDocuments_query = "SELECT document_id, document_name FROM documents";
		$run = @mysqli_query ($admin_database, $findDocuments_query);
		if ($run)
		{
			echo "<p><label for='delete-name' class='left'>Document Name:</label>";
			echo "<select name='delete-name' id='delete-name'>";	
			echo "<option value='0' selected=' '></option>";
			while ($findDocuments_row = mysqli_fetch_array($run))
			{
				$document_id = $findDocuments_row[0];
				$document_name = trim($findDocuments_row[1]);
				echo "<option  value='".$document_id."'>".$document_name."</option>";
			}
		}
    echo "<input type='submit' name='delete-submit' id='delete-submit' class='button' value='Delete' tabindex='12' /></p>";
	echo "</fieldset>";
    echo "</fieldset>";
    echo "</form>";
}

//Displays a table with a list of documents to download
function documentTable($database)
{
	echo "<table border='0' cellspacing='0' cellpadding='0' id='documentation'>";
    echo "<tr class='row-heading'>";
    echo "<th width='10%' scope='col'> Type</th>";
    echo "<th width='85%' scope='col'>Document Name: Description</th>";
    echo "<th width='5%' scope='col'>&nbsp;</th>";
    echo "</tr>";
	
	//Begin outputing documents on the server
	if ($database)
	{
		//set-up document query
		$query = "SELECT document_name, description, file_name FROM documents ORDER BY document_name";
		//run query
		$run = @mysqli_query ($database, $query);
		//echo $query;
		
		if($run)
		{
			//while there are records output the information into the table
			while ($document_row = mysqli_fetch_array($run))
			{
				$document_icon = getDocumentIcon($document_row[2]);
				echo "<tr>";
				echo "<td><div align='center'>";
				echo "<img src='./site/doc-icons/".$document_icon."' width='50' height='50' />";
				echo "</div></td>";
				echo "<td valign='top'><strong>".$document_row[0].": </strong>".$document_row[1]."</td>";
				echo "<td>";
				echo "<a href='./files/".$document_row[2]."' target='blank'>";
				echo "<img src='./site/download.png' width='25' height='25' alt='Click to Download' />";
				echo "</a>";
				echo "</td>";
				echo "</tr>";
			}
		
		}
		//end the table
		echo "</table>";
	}
	else
	{
		echo "<h3 class='error'>There was a problem with the database</h3>";
	}
}
?>