//	Programmer:  Jerri Lynn Reeves
//	File Name:  tooltip.js
//	Purpose:  Provides content for mouse over tool tips
//	Version:  June 29, 2009

$(document).ready(function()
{				
//admin.php
	//User Modification form
	 $("#helpicon").wTooltip({
		content: "This page has tool tips like the one displayed here.  Roll over a form's legend  (title) or a field's label and instructions will appear.",
		style: {width: "200px"} 			
		}); 

	//user table
	 $("#label-userAdmin").wTooltip({
		content: "This table will display all currently registered users, his or her email (logon) name, and his or her permissions.  You may click edit in the chart below to edit that user's data. You may also click delete to delete that user, thus, revoking that user's access. There are no other tool tips for this form.",
		style: {width: "300px"} 			
		}); 

///Add/Modify User Form
	 //User Add/Modify Form:  Email address
	 $("#label-userForm ").wTooltip({
		content: "This form is used to add or modify user information.",
		style: {width: "300px"} 			
		}); 
		
	 //User Add/Modify Form:  Email address
	 $("#label-email ").wTooltip({
		content: "Enter a valid email address, username@domainname.com.  This email address is user's logon name.",
		style: {width: "300px"} 			
		}); 
		
	//User Add/Modify Form:  User's Name
	 $("#label-name ").wTooltip({
		content: "Enter the user's name.",
		style: {width: "300px"} 			
		}); 
		
	//User Add/Modify Form:  Privileges
	 $("#label-privileges").wTooltip({
		content: "Select the user's permissions: <ul><li>Standard:  Only has access to the T3 Application </li><li>Data Administration:  May add/edit/delete data associated with production models, zone of protection parameters such as minimum height, maximum hieght, increment start, and increment value, as well as values associated with Class I, Class II, Class III</li><li>Test Data User: May add/edit/delete test data models and compare models in a zone of protection chart</li><li>Administrator: Has all the permissions of Standard, Data Administration, and Test Data User as well as has the ability to add/edit/delete users and add or delete documents</li></ul>",
		style: {width: "500px"} 			
		}); 
	//User Add/Modify Form:  Password
	 $("#label-password ").wTooltip({
		content: "Enter the user's password.",
		style: {width: "300px"} 			
		}); 
		
//dataAdmin.php
	//User Model Table:  Test
	 $("#label-testMA").wTooltip({
		content: "This table displays all models in the database that have been entered in as a TEST model.  You may edit the model by click the edit link or delete a model by clicking the delete link.  To add a new test model click the  Add Model button.  There are no additional tool tips for this table.",
		style: {width: "300px"} 			
		}); 
	//User Model Table:  Pro
	 $("#label-proMA").wTooltip({
		content: "This table displays all models in the database that have been entered in as a PRO, Production, model.  A production model is a model currently being sold.  You may edit the model by click the edit link or delete a model by clicking the delete link.  To add a new production model click the  Add Model button.  There are no additional tool tips for this table.",
		style: {width: "300px"} 			
		}); 
		//Zone of Protection Form Legend
	 $("#label-zop").wTooltip({
		content: "This form allows you to edit parameters that set-up the zone of protection chart, such as, minimum height, maximum height, an increment value between the minimum and maxiumn height, and the value at which you would like to start that increment.  Each table in the form below contains tool tips.",
		style: {width: "300px"} 			
		}); 
	//Zone of Protection Min Height
	 $("#label-min_height").wTooltip({
		content: "The Zone of Protection Chart begins with the lowest number and will increment to the highest number.  Please enter here the lowest value, or start value, with which you would like the Zone of Protection Chart to begin.",
		style: {width: "300px"} 			
		}); 
	//Zone of Protection max Height
	 $("#label-max_height").wTooltip({
		content: "The Zone of Protection Chart ends with the highest number.  Please enter here the highest value, or end value, with which you would like the Zone of Protection Chart to end.",
		style: {width: "300px"} 			
		}); 
		//Zone of Protection increment
	 $("#label-increment").wTooltip({
		content: "The Zone of Protection Chart will increment from the lowest value to the highest value (pausing its increment during the optimal height zone, -10 to 10).  Please enter a value by which you would like your chart to increment by until reaching the optimal height zone.",
		style: {width: "300px"} 			
		}); 
	//Zone of Protection increment
	 $("#label-increment_start").wTooltip({
		content: "You may want to see another increment value until reaching the optimal height area.  If so, you may change the value here.  This number is the postive and negative number at which your increment will stop.",
		style: {width: "300px"} 			
		}); 
	//Class of Protection
	 $("#label-cop").wTooltip({
		content: "You may change these values; however, these values are set by the NF C and UNE standards.  Only change these values if the values in the NF C and UNE change.",
		style: {width: "300px"} 			
		});
	//Class Values
	 $("#label-cop_warning").wTooltip({
		content: "Only change these values if the values in the NF C and UNE change.",
		style: {width: "300px"} 			
		});
//Shared forms on test and production models
		//Delete Confirm
	 $("#label-delete_confirm").wTooltip({
		content: "Please click delete to delete this model from the database, or you may click cancel to keep the model.",
		style: {width: "300px"} 			
		});
	//Modification Form
	 $("#label-modify").wTooltip({
		content: "This form you may add or modify a models data by entering in the part number and triggering time.  Please note:  only unique part numbers are allowed.  No part number may be used twice as it's type, production or test.",
		style: {width: "300px"} 			
		});
	//Part number
	 $("#label-model-number").wTooltip({
		content: "Please enter a model number.  Generally the format is XXX-##.",
		style: {width: "300px"} 			
		});
	//Part number
	 $("#label-triggering-time").wTooltip({
		content: "Please enter the triggering time for the model.",
		style: {width: "300px"} 			
		})
//Test Data testData.php
	//Comparison Chart
	 $("#label-testData").wTooltip({
		content: "This form is used to compare models in the database of either the production or testing types.",
		style: {width: "300px"} 			
		})
	//Model Selection
	 $("#model-selection").wTooltip({
		content: "The models listed here consist of Test models, prefixed with TEST, and Production models, prefixed  by PRO.  You may choose up to five models to compare, but you must at lease select two.  After your selection has been submitted, a Zone of Protection Chart will be displayed for those models.",
		style: {width: "300px"} 			
		})
//T3 Application
	//Protection Radius Calculator
	 $("#label-prc").wTooltip({
		content: "This calculator determines the protection raidus of a structure given the class of protection requested, the Delta T, or model, used to protect the structure, and the height of the ESE in meters.",
		style: {width: "300px"} 			
		})
	//Protection Radius Calculator Class
	 $("#label-class").wTooltip({
		content: "You must select a Class of Protection",
		style: {width: "300px"} 			
		})
	//Protection Radius Calculator Model
	 $("#label-deltaT").wTooltip({
		content: "Please select a model number.  If you have a particular Deleta T in mind, please use the Gain in Lead Distance Table to see which model most closely matches your Delta T value.",
		style: {width: "300px"} 			
		})
	//Protection Radius Calculator Model
	 $("#label-height").wTooltip({
		content: "Please enter the height of the ESE mast in meters.  This calculation will be incorrect unless you enter your values in meters.",
		style: {width: "300px"} 			
		})
//Safety Distance Calculator
	//Legend label
	 $("#label-sdc").wTooltip({
		content: "This form calculates the minimum distance at which no dangerous spark is produced between a down-conductor draining current from a lightning strike and an earthed (grounded) conductive mass",
		style: {width: "300px"} 			
		})
	//Downconductors
	 $("#label-downconductors").wTooltip({
		content: "Select the number of down-conductors",
		style: {width: "300px"} 			
		})
	//Downconductors
	 $("#label-sdc-class").wTooltip({
		content: "Select the Class of Protection",
		style: {width: "300px"} 			
		})
	//Downconductors
	 $("#label-sdc-material").wTooltip({
		content: "Select the material used between two looped ends",
		style: {width: "300px"} 			
		})
	//Downconductors
	 $("#label-sdc-length").wTooltip({
		content: "Select the length along the down-conductors in meters",
		style: {width: "300px"} 			
		})
});
