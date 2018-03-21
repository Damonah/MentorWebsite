<?php
	session_start();
	include 'sql.php';

	//send to account page if profile button is clicked
	if(isset($_GET['profile'])) {
		$user_email = $_GET['profile'];
		header("Location: account.php?email=$user_email");
		unset($_GET['profile']);
	}


	//default values for buttons/tables
	$radiosel1 = $radiosel4 = "checked";
	$radiosel2 = $radiosel3 = $radiosel5 = "";
	$tableformat = 1;

	if(isset($_POST['searchQuery'])){
		if ($_POST['searchQuery'] == "")
			unset($_POST['searchQuery']);
	}

	//a default placeholder for the person_info array (decides what gets displayed in the table)
	$person_info = "default_placeholder";

	//if the roleSearch radio is selected, decide which table to
	//send into future funtions
	if(isset($_POST['roleSearch'])) {
		$rolebox = $_POST['roleSearch'];

		if ($rolebox == "Mentor") {
			$roletable = "`mentorsubjects`";
			$role = "Mentor ";
			$radiosel1 = "checked";
		}
		elseif ($rolebox == "Mentee") {
			$roletable = "`menteesubjects`";
			$role = "Mentee ";
			$radiosel2 = "checked";
		}
		else {
			$roletable = "both";
			$role = "";
			$radiosel3 = "checked";
		}
	}

	//if the search type radio is clicked, send a string into the search function
	//at the same time, keep the button clicked when we display the results
	if(isset($_POST['searchType'])) {
		$searchType = $_POST['searchType'];
		if ($searchType == "skill") {
			$radiosel5 = "checked";
			$radiosel4 = "";
		}
	}

	//if there is a search query, capture it and unset the post var
	//then perform the search with the external function, finally
	//set the tableformat
	if(isset($_POST['searchQuery']) && isset($_POST['submit'])) {
		$search_str = $_POST['searchQuery'];
		unset($_POST['searchQuery']);
		$person_info = search($search_str, $roletable, $searchType);
		$tableformat = 1;
	}

	//if the user clicks any of the quick search buttons, capture
	//which one, perform the query with the external function, then
	//set the role variable and table format
	if(isset($_POST['qspanel'])){
		$qsp = $_POST['qspanel'];
		if($qsp == "List All Users"){
			$person_info = listAll();
			$role = "";
			$tableformat = 1;
		}
		elseif ($qsp == "Show Inactive Users") {
			$person_info = showInactive();
			$role = "";
			$tableformat = 1;
		}
		//for the next two, we need to set a string for inside the button for
		//matching/unmatching
		elseif ($qsp == "Best Potential Matches") {
			$person_info = bestMatches();
			$role = "";
			$tableformat = 2;
			$match_button = "Create Match";
		}
		elseif ($qsp == "Show Matched Users") {
			$person_info = showMatches();
			$role = "";
			$tableformat = 2;
			$match_button = "Unmatch Pair";
		}elseif ($qsp == "Unmatched Mentors") {
			$person_info = unmatched(1);
			$role = "";
			$tableformat = 1;
		}elseif ($qsp == "Unmatched Mentees") {
			$person_info = unmatched(2);
			$role = "";
			$tableformat = 1;
		}
		unset($_POST['qspanel']);
	}

	//if the unmatch button is selected, perform the query with the external
	//function, get the bestmatches, and show the new matches. Reset the table
	// and the match button string as well
	if(isset($_GET['Unmatch_Pair'])) {
		deleteMatch($_GET['Unmatch_Pair']);
		$person_info = bestMatches();
		$role = "";
		$tableformat = 2;
		$match_button = "Create Match";

	}

	//if the match button is selected, perform the query with the external function,
	//get the matched users, and reset the role, table, and button string
	if(isset($_GET['Create_Match'])) {
		addMatch($_GET['Create_Match']);
		$person_info = showMatches();
		$role = "";
		$tableformat = 2;
		$match_button = "Unmatch Pair";
	}

?>
<!-- build the form for searching -->
<!DOCTYPE html>
<html lang = "eng">
    <head>
        <title>Admin</title>
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
    </head>
    <body>

	<!-- grab navbar from external file -->
        <?php include "navbar.php" ?>

        <h2>Admin Search</h2>
        <hr><br>
		<form action="admin.php#table" method="post">
			<table class="quickSearch">
				<caption>Quick Search Panel</caption>
				<tr>
					<td><input class = "button2" type="submit" name="qspanel" value = "Show Matched Users"/></a></td>
					<td><input class = "button2" type="submit" name="qspanel" value = "Unmatched Mentors"/></a></td>
					<td><input class = "button2" type="submit" name="qspanel" value = "Unmatched Mentees"/></a></td>
				</tr>
				<tr>
					<td><input class = "button2" type="submit" name="qspanel" value = "Best Potential Matches"/></a></td>
					<td><input class = "button2" type="submit" name="qspanel" value = "Show Inactive Users"/></a></td>
					<td><input class = "button2" type="submit" name="qspanel" value = "List All Users"/></a></td>
				</tr>
			</table>
		</form>
		<br /><hr><br />
        <form action='admin.php#tables' style="text-align:center;" method="post" >
			<label><input type="radio" id="searchType" name="searchType" value="person"
				<?php echo "$radiosel4" ?> />By Person</label>
            <label><input type="radio" id="searchType" name="searchType" value="skill"
				<?php echo "$radiosel5" ?> />By Skill</label>
			<br />
            <label><input type="radio" id="roleSearch" name="roleSearch" value="Mentor"
				<?php echo "$radiosel1" ?> />Mentors</label>
            <label><input type="radio" id="roleSearch" name="roleSearch" value="Mentee"
				<?php echo "$radiosel2" ?> />Mentees</label>
			<label><input type="radio" id="roleSearch" name="roleSearch" value="both"
				<?php echo "$radiosel3" ?> />Combined</label>
            <br><br>
            <label>Search: </label><input type="text" id="searchQuery" name="searchQuery" size="50"/>
			<input type="submit" class="button3" name="submit" />
        </form>
        <br>
        <hr>
        <br>
		<?php
		//this is the table format for most searches (single return values instead of pairs)
		if($tableformat == 1){
			if($person_info == "default_placeholder")
				echo "<h2></h2>";
			elseif (count($person_info) < 1)
				echo "<h2>Search Returned No Results</h2>";
			else {
				//after a search is performed and the page reloads with the results,
				//anchor here
				echo "<div id=\"tables\">";
				echo "<form action=\"admin.php#tables\" method=\"post\" >";
				echo "<table id=\"adminSearch\" >";
				echo "<th>Name</th><th>Username</th><th>Email</th><th>Class</th>";
				echo "<th>Role</th><th>$role Skills</th><th></th>";
				foreach($person_info as $person){
					echo "<tr>";
					foreach($person as $info) {
						if($info != "Inactive" && $info != "Admin" && $info != "Pending")
							echo "<td>$info</td>";
						//pending shows grey, admins and inactives will be red
						elseif($info == "Pending")
							echo "<td style=\"color: #c4c4c4; font-weight: bold;\">$info</td>";
						else
							echo "<td style=\"color: red; font-weight: bold;\">$info</td>";
					}
					echo "<td><button class=\"button1\" type=\"submit\" name=\"profile\"";
					echo "formmethod=\"get\" formaction=\"admin.php#tables\" value=\"$person[2]\">Profile</button></td></tr>";
				}
				echo "</table></form></div>";
			}
		}
		//this is the table format for matched pairs
		elseif($tableformat == 2) {
			if($person_info == NULL) {
				echo "<h2>Search Returned No Results<h2>";
			}
			else {
				$row_count = 0;
				echo "<div id=\"tables\">";
				echo "<form action=\"admin.php#tables\" method=\"post\">";
				echo "<table id=\"adminSearch2\" >";
				echo "<th></th><th>Name</th><th>Username</th><th>Email</th><th>Class</th>";
				echo "<th>Role</th><th>$role Skills</th><th style=\"font-size: 8pt;\"># Matched Skills</th>";
				echo "<th></th>";
				for ($i = 0; $i < count($person_info); $i++) {
					for ($j = 0; $j < count($person_info[$i]); $j++) {
						echo "<tr>";
						$count = 0;
						if ($row_count % 2 == 0) {
							$e = $person_info[$i][$j][1];
							$o = $person_info[$i][$j+1][1];
							$em = $person_info[$i][$j][2];
							$send = "$e, $o";
							echo "<td rowspan=\"2\" class=\"mskills\"><button class=\"button4\" type=\"submit\" name=\"$match_button\"";
							echo "formmethod=\"get\" formaction=\"admin.php#tables\" value=\"$send\" >$match_button</button></td>";
						}
						for ($k = 0; $k < count($person_info[$i][$j]); $k++) {
							$info = $person_info[$i][$j][$k];
							if($count == 6)
								echo "<td class=\"mskills\" rowspan=\"2\">$info</td>";
							else {
								if($info != "Inactive" && $info != "Admin" && $info != "Pending")
									echo "<td>$info</td>";
								elseif($info == "Pending")
									echo "<td style=\"color: #c4c4c4; font-weight: bold;\">$info</td>";
								else
									echo "<td style=\"color: red; font-weight: bold;\">$info</td>";
							}
							$count++;
						}
						echo "<td><button class=\"button1\" type=\"submit\" name=\"profile\"";
						echo "formmethod=\"get\" formaction=\"admin.php#tables\" value=\"$em\">Profile</button></td></tr>";

						$row_count++;
					}
				}
				echo "</table></form></div>";
			}
		}


		?>
        <div id = "bottomPage" style="text-align:center;">
            <br>
			<br>
			<br>
            <hr>
            <footer>
                <h6>Copyright &copy; 2017<br>MTSU CS Department</h6>
				<a href="mailto:joshua.phillips@mtsu.edu"><input class ="button1" type="button" value="Contact an Admin"/></a>
                <br>
                <br>

            </footer>
        </div>

    </body>
</html>
