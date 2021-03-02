<?php
#	Programmer Name: Jerri Lynn Reeves
#	File Name:  index.php
#	Version:  July 25, 2009
#	Purpose:  TerraStreamer Product Introduction Interface
session_start();  //Start the session
ob_start();
$_SESSION['requesting-page'] = $_SERVER["REQUEST_URI"];
include('./includes/functions.php');
include ('./includes/header.php');
?> <img src="images/TSP_Group.png" alt="Early Streamer Emitter Terminals" width="300" height="419" class="right" />
		<h1>Early Streamer Emitter Terminals</h1>
		<h2>for structural lightning protection</h2>
		<p>Extensive research and development has allowed Alltec Corporation to create a lightweight and low wind loading ESE system to provide a safe and efficient manner of controlling dangerous lightning energy before it damages structures or its important contents, including human occupants. By utilizing advanced technology, TerraStreamer<sup>&reg;</sup> ESEs provide lightning protection to facilities that would otherwise be difficult or cost prohibitive to protect by conventional means.</p> 
       	<p>The TerraStreamer<sup>&reg;</sup>  ESE Terminals are externally mounted, proactive, structural lightning protection devices and are designed to activate in the moments directly preceding an imminent direct strike. The installation of a TerraStreamer<sup>&reg;</sup>  ESE Terminal combines the best advantages of two systems: the direct path to ground of a conventional lightning protection system, and state-of-the-art ESE technology employed in the TerraStreamer’s internal design. These combined advantages ensure that the TerraStreamer<sup>&reg;</sup>  ESE System provides a secure zone of protection.</p>
        <p>TerraStreamer<sup>&reg;</sup> ESEs are made of non-corrosive materials, utilize advance and sustainable technologies, maintain a 5-year replacement warranty, and are independently tested certified to NF C 17-102 and UNE 21 186 standards. TerraStreamer<sup>&reg;</sup> products complete the Alltec Protection Pyramid by capturing dangerous lightning discharges and safely channeling it to earth.</p>
        
	 <div class="right-column">
	 <h1>ESE Features</h1>
   	   	<h2>Read About Early Streamer Emitter Features</h2>
      	<div class="dynamic-text">
		<ul>
			<li>NF C 17-102 and UNE 21 186 tested and certified</li>
			<li>Lightweight and low wind loading</li>
			<li>Reliable performance in all weather conditions</li>
			<li>Suitable for corrosive environments</li>
			<li>Available in five models for multiple applications</li>
			<li>Economical and easy to install</li>
			<li>Five-year replacement warranty</li>	
		</ul>
		</div>
		<h1>ESE Standards Compliance</h1>
   	   	<h2>Read About Early Streamer Emitter Standards Compliance</h2>
      	<div class="dynamic-text"><p>A Certificate of Protection Radius and Fulfillment of standards UNE 21186 and NFC 17102 for each model and level</p><ul><li>Certificate of Withstood Current</li><li>Certificate of Gain in Triggering Time</li> </ul></div>
	     <h1>ESE Principles</h1>
	     <h2>Read About Early Streamer Emitter Protection Theory</h2>
   	  		 <div class="dynamic-text">
             <p>The principle of operation for ESE terminals is to create an upward propagating streamer earlier than conventional air terminals or other objects on the earth. TerraStreamer<sup>&reg;</sup>  does this by collecting and storing ground charge during the initial phase of a thunderstorm development.</p>
             <p>Once a thunderstorm begins creating downward step leaders, the ambient electric field intensity in the area of the ESE terminal increases.  When this electric field intensifies, it triggers the terminal to release the stored ground charge, forming an upward streamer microseconds earlier than other objects in the immediate area.</p>
             <p>This development of an upward streamer earlier in time and space ensures that the TerraStreamer® ESE terminal will be the target of the developing lightning strike.  The selection of the TerraStreamer® model, placement, and mounting height above the protected area all factor into formulas calculating the dimensions of the protection area.</p>
             </div>
   	   	 <h1>ESE Protection Radius</h1>
   	    <h2>Read About Early Streamer Emitter Protection Radius</h2>
    	    <div class="dynamic-text"><p>Any charts, drawings, or data pertaining to the radius of protection are provides provided as per the NF C 17-102 standard.  According to NF C 17-102, the standard protection radius Rp of the TerraStreamer<sup>&reg;</sup> is linked to &916;T,  the protection levels I, II, or III (as calculated in Annex B of NFC17-102), and to the height of the TerraStreamer® above the protected structure (H, defined by NF C 17-102 as a minimum of 2m).</p></div>
        </div><!--End right-column-->   
	<div class="left-column">
		<div id="carousel">
		<ul>
			<li><img alt="Railway Application" src="images/railway.jpg" width="400" height="268" /></li>
			<li><img alt="Airport Application" src="images/airport.jpg" width="400" height="268" /></li>
			<li><img alt="Mining Applications" src="images/mining.jpg" width="400" height="268" /></li>
			<li><img alt="Highrise Applications" src="images/highrise.jpg" width="400" height="268" /></li>
			<li><img alt="Golf Course Application" src="images/GolfCourse.png" width="400" height="268" /></li>
		</ul>
		</div>
	</div>
    </div><!--End content-->
  </div>
<?php 
ob_end_flush();
include ('./includes/footer.html');?>