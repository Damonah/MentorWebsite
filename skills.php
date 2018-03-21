<?php
include_once 'sql.php';
session_start();

?>
<html lang = "eng">
    <head>
        <title>Welcome to MTSU CS Mentoring</title>
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
    </head>
    <body>

        <!-- grab navbar from external file -->
		<?php include "navbar.php" ?>

        <h2>Popular Skills</h2>
        <hr>
		<table id="skillslist">
			<tr>
    			<?php
    			$count = 0;
    			if($result = $conn->query("SELECT SubjectName FROM Subject")){  //selects all the subjects from the list
    				while($row = $result->fetch_row()) {						//grabs all the subjects by row
    					if ($count % 4 == 0) {echo "</tr><tr>";}
    					echo "<td>$row[0]</td>";								//echos out the skill and updates count
    					$count++;
    				}
    			}
    			?>
			</tr>
		</table>
        <br>

        <div id = "bottomPage" style="text-align:center;">
			<br>
			<br>
			<br>
            <hr>
            <footer>
                <h6>Copyright &copy; 2017<br>MTSU CS Department</h6>
                <a href="mailto:joshua.phillips@mtsu.edu"><input class ="button1" type="button" value="Contact an Admin"/></a> <!--Has button to send email to Dr. Phillips as the admin -->
                <br>
                <br>
            </footer>
        </div>

    </body>
</html>
