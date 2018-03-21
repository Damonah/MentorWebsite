<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mentor_sys";
// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: <br>" . $conn->connect_error);
} 
//Connected successfully

?>
<?php
//this function gets a list of mentors
function GetMentors(){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Role.roleName LIKE 'mentor'";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		return $result;
	}
	return false;
}

//this function gets a list of mentees
function GetMentees(){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Role.roleName LIKE 'mentee'";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		return $result;
	}
	return false;
}

//this function returns a list of Admins
function GetAdmins(){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Role.roleName LIKE 'admin'";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		return $result;
	}
	return false;
}

//this function gets a user id from an email
function getIdByEmail($email) {
	$sql = "SELECT `id` from `person` where `email` = \"$email\"";
	$result = $GLOBALS['conn']->query($sql);
	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		return $row['id'];
	}
	else
		return null;
}

//this function will delete the pending role for a user
function deletePending($p_id) {
	$sql = "DELETE FROM `personroles` where `person_id` = $p_id AND `role_id` = 4";
	$result = $GLOBALS['conn']->query($sql);
	return $result;
}
	
//this function gets all of the information from the persons table, indexed by email
function GetUser($email){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName, Person.classification,Person.uname, Role.roleName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Person.email='$email'";
	if ($result = $GLOBALS['conn']->query($sql)){
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	return false;
}

//this function gets basic information from the persons table, indexed by email
function GetUserProfile($email){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName, Person.classification,Person.uname FROM Person
    WHERE Person.email='$email'";
	if ($result = $GLOBALS['conn']->query($sql)){
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	return false;
}
//this function gets user by username
function GetUserById($uname){
	
	$sql = "SELECT Person.id,Person.email, Person.fName, Person.lName, Person.classification,Person.uname, Role.roleName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Person.uname='$uname'";
	if ($result = $GLOBALS['conn']->query($sql)){
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	return false;
}

//this function gets user roles as role numbers indexed by email
function GetUserRoles($email){
	$sql = "SELECT Person.id,Person.email, Role.roleName FROM Person
    JOIN PersonRoles
        ON PersonRoles.person_id = Person.id
    JOIN Role
        ON Role.id = PersonRoles.role_id
    WHERE Person.email='$email''";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		return $result;
	}
	return false;

}

//this function inserts a mentor's subjects
function AddUserSubjectMentorsubjects($person_id, $skill){

	$sql = "SELECT  id FROM `subject` WHERE subjectName LIKE '$skill'";
			  $result = $GLOBALS['conn']->query($sql);
		if( $result-> num_rows > 0 ){
				while($row = $result ->fetch_assoc()){
					$subj_id  = $row['id'];
				}
			}else{
				echo "Error: " . $result . "<br>" . $GLOBALS['conn']->error;
			}
		$sql = "INSERT INTO mentorsubjects (person_id, subject_id)
			VALUES ($person_id, $subj_id)";
		if ($GLOBALS['conn']->query($sql) !== true ){
			echo "Error: " . $sql . "<br>" . $GLOBALS['conn']->error;
		}
}

//this function inserts a Mentee's subjects
function AddUserSubjectMenteesubjects( $person_id, $skill){

	$sql = "SELECT  id FROM `subject` WHERE subjectName LIKE '$skill'";
			$result = $GLOBALS['conn']->query($sql);
		if( $result-> num_rows > 0 ){
				while($row = $result ->fetch_assoc()){
					$subj_id  = $row['id'];
				}
			}else{
				echo "Error: " . $result . "<br>" . $GLOBALS['conn']->error;
			}
		$sql = "INSERT INTO menteesubjects (person_id, subject_id)
			  VALUES ($person_id, $subj_id)";
		if ($GLOBALS['conn']->query($sql) !== true ){
			echo "Error: " . $sql . "<br>" . $GLOBALS['conn']->error;
		}

}

//this function adds a role into the personroles table
function AddUserRole( $person_id, $role){
	
	if (strtolower($role) == "admin")
		$role_id = 1;
	elseif (strtolower($role) == "mentor")
		$role_id = 2;
	elseif (strtolower($role) == "mentee")
		$role_id = 3;
	elseif (strtolower($role) == "pending")
		$role_id = 4;
	
	//check if the user already has the role
	$sql = "SELECT * FROM `personroles` WHERE `person_id` = $person_id AND `role_id` = $role_id";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0)
		return null;
	else{
		$sql = "INSERT INTO PersonRoles (person_id, role_id)
				VALUES ($person_id, $role_id)";
		if ($GLOBALS['conn']->query($sql) !== true){
			echo "Error: " . $sql . "<br>" . $GLOBALS['conn']->error;
		}
	}

}

	# this function returns a username given an email address
	function GetUsername($email){
		$username_query = "select uname from Person where email = '$email'";
		$username_results = $GLOBALS['conn']->query($username_query);
		if ($username_results){
			while($username_array[]=mysqli_fetch_array($username_results));

			if(is_array($username_array)){
				if(isset($username_array[0])){
					$resulting_username=$username_array[0][0];
				}
			}
			return  $resulting_username;
		}
		return null;
	}

	# this function returns an email address given a username
	function GetEmail($username){
		$email_query = "select email from Person where uname = '$username'";
		$email_results = $GLOBALS['conn']->query($email_query);
		while($email_array[]=mysqli_fetch_array($email_results));

		if(is_array($email_array)){
			if(isset($email_array[0])){
				$resulting_email=$email_array[0][0];
		   }
		}
		return  $resulting_email;
	}

	# this function returns an array of a mentors mentees given the mentors email address
	function GetMentorsMentees($email){
		$search_username = GetUsername($email);
		$resulting_ids = GetCurrentRelationships($email);
		$mentors_mentees_query = "select person.uname from Person, PersonMatches where PersonMatches.id in ($resulting_ids) and (person.id = PersonMatches.mentee_id)";
		$results = $GLOBALS['conn']->query($mentors_mentees_query);
		$result = array();
		if ($results){
			while($mentors_mentees_array[]=mysqli_fetch_array($results));
			$results_array[]=array();
			foreach ($mentors_mentees_array as $mentee){
				if ($mentee["uname"] != null){
					if ($mentee["uname"] != $search_username){
						array_push($results_array, $mentee["uname"]);
					}
				}
			}
			$result = array_filter($results_array);
		}
		return $result;
	}

	# this function returns an array of a mentees mentors given the mentees email address
	function GetMenteesMentors($email){
		$search_username = GetUsername($email);
		$resulting_ids = GetCurrentRelationships($email);
		$mentees_mentors_query = "select person.uname from Person, PersonMatches where PersonMatches.id in ($resulting_ids) and (person.id = PersonMatches.mentor_id)";
		$results = $GLOBALS['conn']->query($mentees_mentors_query);
		if ($results){
			while($mentees_mentors_array[]=mysqli_fetch_array($results));
			$results_array[]=array();
			foreach ($mentees_mentors_array as $mentor){
				if ($mentor["uname"] != null){
					if ($mentor["uname"] != $search_username){
						array_push($results_array, $mentor["uname"]);
					}
				}
			}
			$result = array_filter($results_array);
			return $result;
		}
		return array();		
	}

	# this function returns a comma seperated list of match ids given a users email address
	function GetCurrentRelationships($email){
		$match_id_query = "select PersonMatches.id from PersonMatches, Person where person.email = '$email' and (person.id = PersonMatches.mentor_id OR person.id = PersonMatches.mentee_id)";
		$match_id_results = $GLOBALS['conn']->query($match_id_query);
		$comma_separated= "";
		while($resulting_id_array[]=mysqli_fetch_array($match_id_results)){
			foreach($resulting_id_array as $row){
				if ($comma_separated !== ""){
					$comma_separated=$comma_separated.",";
				}
				$comma_separated = $comma_separated.$row['id'];
			}
		}
		$resulting_ids = "";
		foreach($resulting_id_array as $key=>$value){
		    if(is_array($value)){
		    	if(isset($value[0])){
					$resulting_ids.= "\"".$value[0]."\",";
		    		}
		       }
		}
		$resulting_ids = substr($resulting_ids, 0, -1);
		return $resulting_ids;
	}

	# this function builds a table of the current relationships for a given email address
	function Get_Relationships_Table($search_email){
		$mentors_array = GetMenteesMentors($search_email);
		$mentees_array = GetMentorsMentees($search_email);
		echo "<br/>";
		echo "<table id=\"relationshipstable\"><tr><th>Username</th><th>Relationship</th><th>Email</th></tr>";
		foreach ($mentors_array as $mentor) {
			$email = GetEmail($mentor);
			echo "<tr><td>$mentor</td><td>Mentor</td><td>$email</td></tr>";
		}
		foreach ($mentees_array as $mentee) {
			$email = GetEmail($mentee);
			echo "<tr><td>$mentee</td><td>Mentee</td><td>$email</td></tr>";
		}
		echo "</table>";
	}

?>
<?php
//search the users 
//$str = search string
//$table = mentorsubjects, menteesubjects, or both
//$type = person (by person) or skill (by skill)
function search($str, $table, $type) {
	if($type == "skill") {
		//get the id of the search term from subject table
		$sql = "SELECT `id` FROM `subject` WHERE `subjectName` LIKE \"$str\"";
		$s_id = $GLOBALS['conn']->query($sql);
		if($s_id->num_rows > 0) {
			while($row = $s_id->fetch_assoc())
				$subj_id = $row['id'];
		}
		//if there are no matches for the search string, return null
		else {
			return null;
		}
		//there are special tricks we need to do if we are searching both tables
		if ($table == "both") {
			//iterate over both mentee and mentor subjects, getting the person_ids 
			for ($i = 0; $i < 2; $i++) {
				if ($i == 0) {
					$table = "`mentorsubjects`";
					$role =  1;
				}
				else {
					$table = "`menteesubjects`";
					$role = 2;
				}
				//get the ids from the person table for those persons who have the skill 
				$sql = "SELECT `person_id` FROM $table WHERE `subject_id` = $subj_id";
				$p_id = $GLOBALS['conn']->query($sql);
				if($p_id->num_rows > 0) {
					while($row = $p_id->fetch_assoc()) 
						$pers_id[] = array((int)$row['person_id'], $role);
				}
			}
			//reset the table string for future processing
			$table = "both";
			//if no one has the skills, return null
			if (count($pers_id) == 0)
				return null;
		}
		//if we are searching only one table, we can get sraight to obtaining the person_ids
		else {
			//get the ids from the person table for those persons who have the skill 
			$sql = "SELECT `person_id` FROM $table WHERE `subject_id` = \"$subj_id\"";
			$p_id = $GLOBALS['conn']->query($sql);
			if($table = "`mentorsubjects`")
				$role = "Mentor";
			else
				$role = "Mentee";
			if($p_id->num_rows > 0) {
				while($row = $p_id->fetch_assoc())
					$pers_id[] = $row['person_id'];
			}
			else {
				return null;
			}
		}
		
		//get the info for each person that has the skill
		foreach($pers_id as $value) {
			
			$sql = "SELECT `fname`, `lname`, `classification`, `uname`, 
					`email` FROM `person` WHERE `id` = $value[0]";
			$person = $GLOBALS['conn']->query($sql); {
				while($row = $person->fetch_assoc())
					$person_info[] = array($row['fname'], $row['lname'], 
						$row['uname'], $row['email'], $row['classification']);
			}
			//again, if we are searching both tables, we need to do some extra work
			if ($table == "both") {
				if ($value[1] == 1) {
					$table = "`mentorsubjects`";
					$role = "Mentor";
				}
				else {
					$table = "`menteesubjects`";
					$role = "Mentee";
				}
				//get a list of the subject id's for each person that has the skill
				$sql = "SELECT `subject_id` FROM $table WHERE `person_id` = $value[0]";
				$personsubjects = $GLOBALS['conn']->query($sql);

				while($row = $personsubjects->fetch_assoc())
					$pers_subj[] = $row['subject_id'];
				$pers_subj_ids[] = $pers_subj;
				unset($pers_subj);
				$table = "both";
			}
			else {			
				//get a list of the subject id's for each person that has the skill
				$sql = "SELECT `subject_id` FROM $table WHERE `person_id` = $value[0]";
				$personsubjects = $GLOBALS['conn']->query($sql);
				while($row = $personsubjects->fetch_assoc())
					$pers_subj[] = $row['subject_id'];
				$pers_subj_ids[] = $pers_subj;
				unset($pers_subj);
			}
			
			//get the roles of each person
			$sql = "SELECT `role_id` FROM `personroles` WHERE `person_id` = $value[0]";
			$roleid = $GLOBALS['conn']->query($sql);
			$i = count($person_info) - 1;
			if ($roleid->num_rows > 0) {
				while($row = $roleid->fetch_assoc())
					$roles[] = $row['role_id'];
				if(count($roles > 0))
					array_push($person_info[$i], $role);
			}
			else
				array_push($person_info[$i], "Inactive");
		}
		//get the names of the subjects for each persons subject list
		$count = 0;
		foreach($pers_subj_ids as $ids) {
			foreach($ids as $id){
				$sql = "SELECT `subjectName` FROM `subject` WHERE `id` = $id";
				$names = $GLOBALS['conn']->query($sql);
				while($row = $names->fetch_assoc())
					$subj_names[] = $row['subjectName'];
			}
			//implode the list of subject names to one string
			$pers_subj_names = implode(", ", $subj_names);
			unset($subj_names);
			
			//add subject names to the end of the person info array
			array_push($person_info[$count], $pers_subj_names);
			$count++;
		}
		
		//concatenate the names, drop the extra field
		for($i = 0; $i < count($person_info); $i++) {
			$full_name = $person_info[$i][0] . " " . $person_info[$i][1]; 
			$person_info[$i][0] = $full_name;
			unset($person_info[$i][1]);
		}
	}
	//if we are searching by username/Name
	else {
		$person_info = array();
		//get the info of the people from the person table
		$sql = "SELECT `id`, `fName`, `lName`, `email`, `uname`, `classification` FROM `person` WHERE 
				`uname` LIKE \"%$str%\" OR fname LIKE \"%$str%\" OR lname LIKE \"%$str%\"";
		$results = $GLOBALS['conn']->query($sql);
		if($results->num_rows > 0) {
			while($row = $results->fetch_assoc())
				$person_info[] = array($row['id'], $row['fName'], $row['lName'], $row['uname'], $row['email'], $row['classification']);
		}
		//if there is no match of the search string
		else {
			return null;
		}
		
		//for each person, get the role and subjects string 
		for($i = 0; $i < count($person_info); $i++) {
			$p_id = (int)$person_info[$i][0];
			
			//get the roles for this user
			$roles = array();
			$sql = "SELECT `role_id` FROM `personroles` WHERE `person_id` = \"$p_id\"";
			$results = $GLOBALS['conn']->query($sql);
			if($results->num_rows == 1) {
				while($row = $results->fetch_assoc())
					$roles =  $row['role_id'];
			}
			elseif($results->num_rows > 1) {
				while($row = $results->fetch_assoc())
					array_push($roles, $row['role_id']);
			}
			
			//build the roles and subject id/name list
			$subj_ids = $s_ids = array();
			if(count($roles) == 0) {
				$role_names = "Inactive";
				$subj_names = getSubjNames($p_id, "both");
			}
			elseif (count($roles) == 1) {
				if($roles == "1"){
						$role_names = "Admin";
						$subj_names = "";
				}
				elseif($roles == "2") {
					$role_names = "Mentor";
					$subj_names = getSubjNames($p_id, "mentor");
				}
				elseif($roles == "3") {
					$role_names = "Mentee";
					$subj_names = getSubjNames($p_id, "mentee");
				}
				elseif($roles == "4") {
					$role_names = "Pending";
					$subj_names = getSubjNames($p_id, "both");
				}
			}
			else {
				$role_names = "";
				for($j = 0; $j < count($roles); $j++) {
					if($roles[$j] == "2")
						$role_names .= "Mentor";
					elseif($roles[$j] == "3")
						$role_names .= "Mentee";
					elseif($roles[$j] == "4") {
						$role_names = "Pending";
					}
					if(count($roles) != $j)
						$role_names .= ", ";				
				}
				//if "Pending" is part of the role string, we want to omit the other roles
				if(strpos($role_names, "Pending"))
				$role_names = "Pending";
				$subj_names = getSubjNames($p_id, "both");
			}
			
			//add the role and subject lists to person_info
			array_push($person_info[$i], $role_names);
			array_push($person_info[$i], $subj_names);	
		
			//get rid of the $p_id
			unset($person_info[$i][0]);
			$person_info[$i] = array_values($person_info[$i]);
			
			//concatenate the names, drop the extra field
			$full_name = $person_info[$i][0] . " " . $person_info[$i][1]; 
			$person_info[$i][0] = $full_name;
			unset($person_info[$i][1]);
			$person_info[$i] = array_values($person_info[$i]);
		}
		
	}
	return $person_info;
}

//get a list of all users (intentionally omits Pending users)
function listAll() {
	
	//get all of the users from the users table
	$sql = "SELECT * FROM `person`";
	$results = $GLOBALS['conn']->query($sql);
	while ($row = $results->fetch_assoc()){
		$person_info[] = array((int)$row['id'], $row['fName'], $row['lName'], 
			$row['uname'], $row['email'], $row['classification']);
		
	}

	//get the roles for each user
	for ($i = 0; $i < count($person_info); $i++){
		$id = $person_info[$i][0];
		$sql = "SELECT `role_id` FROM `personroles` WHERE
				`person_id` = $id AND `role_id` != 4";
		if($results = $GLOBALS['conn']->query($sql)) {
			if($results->num_rows > 1){
				while($row = $results->fetch_assoc())
					$roles[] = $row['role_id'];
			}
			else {
				$row = $results->fetch_assoc();
				$roles = $row['role_id'];
			}
		}
		
		//push a role value to the end of the person_info array
		if(!isset($roles)) {
			array_push($person_info[$i], "Inactive");
			$sql = "SELECT `subject_id` FROM `mentorsubjects` WHERE `person_id` = $id UNION ALL 
				SELECT `subject_id` FROM `menteesubjects` WHERE `person_id` = $id";
		}
		elseif(count($roles) > 1) {
			array_push($person_info[$i], "Mentor, Mentee");
			$sql = "both";
		}
		elseif ($roles == 1) {
			array_push($person_info[$i], "Admin");
			$sql = "";
		}
		elseif ($roles == 2) {
			array_push($person_info[$i], "Mentor");
			$sql = "SELECT `subject_id` FROM `mentorsubjects` WHERE 
				`person_id` = $id";	
		}
		elseif ($roles == 3) {
			array_push($person_info[$i], "Mentee");	
			$sql = "SELECT `subject_id` FROM `menteesubjects` WHERE 
				`person_id` = $id";	
		}
		
		//push the subject names to the end of the p_i array
		$pers_subj_names = "";
		if($sql == "") 
			array_push($person_info[$i], "");
		//if the person has multiple roles, split the queries up so we
		//can interlope some identifiers for each set of skills
		elseif ($sql =="both"){
			for($j = 0; $j < 2; $j++) {
				if($j == 0) {
					$sql = "SELECT `subject_id` FROM `mentorsubjects` WHERE 
						`person_id` = $id";
				}
				else {
					$sql = "SELECT `subject_id` FROM `menteesubjects` WHERE 
						`person_id` = $id";	
				}
				
				$personsubjects = $GLOBALS['conn']->query($sql);
			
				unset($pers_subj);
				while($row = $personsubjects->fetch_assoc())
					$pers_subj[] = $row['subject_id'];
				
				//get the names of the subjects for each persons subject list
				foreach($pers_subj as $ids) {
					$sql = "SELECT `subjectName` FROM `subject` WHERE `id` = $ids";
					$names = $GLOBALS['conn']->query($sql);
						while($row = $names->fetch_assoc())
							$subj_names[] = $row['subjectName'];
					}
					//implode the list of subject names to one string
					$pers_subj_names .= implode(", ", $subj_names);
					unset($subj_names);

					if($j == 0) {
						$pers_subj_names = "<b>Mentor: </b>" . $pers_subj_names
							. "<br/><b>Mentee: </b>";
					}
			}
			//add subject names to the end of the person info array
			array_push($person_info[$i], $pers_subj_names);
		}	
		else {		
			$personsubjects = $GLOBALS['conn']->query($sql);
			
			$pers_subj = array();
			if ($personsubjects->num_rows > 0) {
				while($row = $personsubjects->fetch_assoc())
					$pers_subj[] = $row['subject_id'];
			}
			
			//get the names of the subjects for each persons subject list
			foreach($pers_subj as $ids) {
				$sql = "SELECT `subjectName` FROM `subject` WHERE `id` = $ids";
				$names = $GLOBALS['conn']->query($sql);
				if($names->num_rows > 0){
					while($row = $names->fetch_assoc())
						$subj_names[] = $row['subjectName'];
				}
			}
				//implode the list of subject names to one string
				$pers_subj_names = implode(", ", $subj_names);
				$subj_names = array();
				
				//add subject names to the end of the person info array
				array_push($person_info[$i], $pers_subj_names);
			
		}
		//get rid of the person_id and concatenate the users name
		unset($person_info[$i][0]);
		$full_name = $person_info[$i][1] . " " . $person_info[$i][2]; 
		$person_info[$i][1] = $full_name;
		unset($person_info[$i][2]);
	}
	//reset the indices and return
	$person_info = array_map('array_values', $person_info);
	
	//sort the array by role
	$count = $inner_count = count($person_info);
	$temp = $person_info;
	unset($person_info);
	$person_info = array();
	
	//add all of the mentors to the array first
	for($j = 0; $j < $inner_count; $j++) {	
		if($temp[$j][4] == "Mentor") {
			array_push($person_info, $temp[$j]);
			unset($temp[$j]);
			$inner_count--;
		}
	}
	$temp = array_values($temp);
	for($j = 0; $j < $inner_count; $j++) {	
		if($temp[$j][4] == "Mentor, Mentee") {
			array_push($person_info, $temp[$j]);
			unset($temp[$j]);
			$inner_count--;
		}
	}
	$temp = array_values($temp);
	for($j = 0; $j < $inner_count; $j++) {	
		if($temp[$j][4] == "Mentee") {
			array_push($person_info, $temp[$j]);
			unset($temp[$j]);
			$inner_count--;
		}
	}
	$temp = array_values($temp);
	for($j = 0; $j < $inner_count; $j++) {	
		if($temp[$j][4] == "Inactive") {
			array_push($person_info, $temp[$j]);
			unset($temp[$j]);
			$inner_count--;
		}
	}
	$temp = array_values($temp);
	for($j = 0; $j < $inner_count; $j++) {	
		if($temp[$j][4] == "Admin") {
			array_push($person_info, $temp[$j]);
			unset($temp[$j]);
			$inner_count--;
		}
	}
	$temp = array_values($temp);
	return $person_info;
}

//get a list of inactive users (shows pending users)
function showInactive() {
	$inactives = listAll();
	$temp = $inactives;
	for ($i = 0; $i < count($temp); $i++) {
		if ($temp[$i][4] != "Inactive")
			unset($inactives[$i]);
	}
	return $inactives;
}

//get a list of the best match for each mentee
function bestMatches() {
	//get a list of only active, non-admin users
	//separate the lists into mentor/mentees
	$actives = listAll();
	$temp = $actives;
	$mentors = array();
	$mentees = array();
	for ($i = 0; $i < count($temp); $i++) {
		if ($temp[$i][4] == "Inactive" || $temp[$i][4] == "Admin")
			unset($actives[$i]);
		elseif ($temp[$i][4] == "Mentor") {
			//array_push($actives[$i], "");
			array_push($mentors, $actives[$i]);
		}
		elseif ($temp[$i][4] == "Mentee")
			array_push($mentees, $actives[$i]);
		else {
			//get rid of the mentor/mentee interpolates and push each
			//half into the appropriate mentee/mentee array
			$copy = $copy2 = $temp;
			$copy[$i][5] = str_replace("<b>Mentor: </b>", "", $copy[$i][5]);
			$pos = strpos($copy[$i][5], "<br");
			$copy[$i][5] = substr_replace($copy[$i][5], "", $pos, 9999);
			$copy[$i][4] = "Mentor";
			array_push($mentors, $copy[$i]);
			$pos = strpos($copy2[$i][5], "e: </b>");
			$copy2[$i][5] = substr_replace($copy2[$i][5], "", 0, $pos+7);
			$copy2[$i][4] = "Mentee";
			array_push($mentees, $copy2[$i]);
		}
	}
	
	//loop for each mentee and find the mentor that has the most matches
	for($i = 0; $i < count($mentees); $i++) {
		$e_skill = $mentees[$i][5];
		$e_skills = explode(", ", $e_skill);
		$e_uname = $mentees[$i][1];
		for($j = 0; $j < count($mentors); $j++) {
			$o_skill = $mentors[$j][5];
			$o_skills = explode(", ", $o_skill);
			$o_uname = $mentors[$j][1];
			$matches = 0;
			for($k = 0; $k < count($e_skills); $k++){
				for($l = 0; $l< count($o_skills); $l++) {
					if($e_skills[$k] == $o_skills[$l])
						$matches++;
				}
			}
			$e_matches[] = array($matches, $o_uname, $j);
		}
		// choose the best matches
		$greatest = array(-1, -1);
		for($m = 0; $m < count($e_matches); $m++) {
			if($e_matches[$m][0] > $greatest[0]) {
				$greatest = array($e_matches[$m][0], $e_matches[$m][2]);
			}			
		}
		//check for multiple mentors with the same number of matches
		for($m = 0; $m < count($e_matches); $m++) {
			if($e_matches[$m][0] == $greatest[0]) {
				array_push($greatest, $e_matches[$m][2]);				
			}
		}
		
		//get rid of the original value that is now a duplicate
		unset($greatest[1]);
		
		//reset array indices for $greatest array
		$greatest = array_values($greatest);
		
		//push the number of matches onto the end of each mentee array
		array_push($mentees[$i], $greatest[0]);
		
		//put all info into an array for sending back to admin.php
		for($n = 1; $n < count($greatest); $n++){
			$match_array[] = array($mentees[$i], $mentors[$greatest[$n]]);
		}
		unset($e_matches);
	}
	
	//get rid of already matched pairs
	$temp = $match_array;
	$matched_array = showMatches();
	for($i = 0; $i < count($matched_array); $i++) {
		$mo_uname = $matched_array[$i][0][1];
		$me_uname = $matched_array[$i][1][1];
		for($k = 0; $k < count($temp); $k++) {
			$uo_uname = $temp[$k][1][1];
			$ue_uname = $temp[$k][0][1];
			if($mo_uname == $uo_uname && $me_uname == $ue_uname)
				unset($match_array[$k]);
			
		}
	}
	$match_array = array_values($match_array);
	return $match_array;
}	

//show the matched users from the personmatches table
function showMatches() {
	
	//get a list of person info of matched mentors and mentees
	$sql = "SELECT Mentor.fName ofname, Mentor.lName olname, Mentor.uname ouname, 
			Mentor.email oemail, Mentor.classification oclass, Mentee.fName, Mentee.lName,
			Mentee.uname, Mentee.email, Mentee.classification FROM personmatches 
			INNER JOIN person AS `Mentor` ON Mentor.id = personmatches.mentor_id 
			INNER JOIN person AS `Mentee` ON Mentee.id = personmatches.mentee_id";
	$matched_list = $GLOBALS['conn']->query($sql);
	$num_rows = $matched_list->num_rows;
	if($matched_list->num_rows > 0) {
		while($row = $matched_list->fetch_assoc())
			$matched_arr[] = $row;
	}
	
	//grab the rest of the information from the db
	for ($i = 0; $i < count($matched_arr); $i++) {
		//reset the array keys
		$matched_arr[$i] = array_values($matched_arr[$i]);
		
		//concatenate the first and last names for mentor and mentee
		$full_name = $matched_arr[$i][0] . " " . $matched_arr[$i][1]; 
		$matched_arr[$i][0] = $full_name;
		unset($matched_arr[$i][1]);
		$full_name = $matched_arr[$i][5] . " " . $matched_arr[$i][6]; 
		$matched_arr[$i][5] = $full_name;
		unset($matched_arr[$i][6]);
		$matched_arr[$i] = array_values($matched_arr[$i]);
		
		//add the role for the user
		array_splice($matched_arr[$i], 4, 0, "Mentor");
		array_splice($matched_arr[$i], 9, 0, "Mentee");
		
		//get the skills
		$o_subjects = array();
		$e_subjects = array();
		
		for($j = 0; $j < 2; $j++) {
			if ($j == 0) {
				$uname = $matched_arr[$i][1];
				$table = "mentorsubjects";
			}
			else {
				$uname = $matched_arr[$i][6];
				$table = "menteesubjects";
			}
			$sql = "(SELECT subject_id FROM $table WHERE person_id = 
					(SELECT id FROM person WHERE uname = \"$uname\"))";
			$result = $GLOBALS['conn']->query($sql);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$subj_id = $row['subject_id'];
					$sql = "SELECT subjectName FROM subject WHERE 
						id = \"$subj_id\"";
					$subj_name = $GLOBALS['conn']->query($sql);
					if($subj_name->num_rows > 0) {
						$row2 = $subj_name->fetch_assoc();
						if ($j == 0)
							array_push($o_subjects, $row2['subjectName']);
						else 
							array_push($e_subjects, $row2['subjectName']);
					}
				}
			}
		}
		
		//get the number of matches for the pair
		$matches = 0;
		for($k = 0; $k < count($e_subjects); $k++){
			for($l = 0; $l< count($o_subjects); $l++) {
				if($e_subjects[$k] == $o_subjects[$l])
					$matches++;
			}
		}
		
		$mentor_subjects = implode(", ", $o_subjects);
		$mentee_subjects = implode(", ", $e_subjects);
		
		//add the skills for the user
		array_splice($matched_arr[$i], 5, 0, $mentor_subjects);
		array_splice($matched_arr[$i], 6, 0, $matches);
		array_push($matched_arr[$i], $mentee_subjects);
		unset($subj_ids, $o_subjects, $e_subjects);
		
		$mentor_arr = array_slice($matched_arr[$i], 0, 7);
		$mentee_arr = array_slice($matched_arr[$i], 7);
		$match_array[] = array($mentor_arr, $mentee_arr);
	}
	
	return $match_array;

}

//show unmatched users, argument is mentor/mentee
function unmatched($role){
	
	$actives = listAll();
	$temp = $actives;
	$mentors = array();
	$mentees = array();
	for ($i = 0; $i < count($temp); $i++) {
		if ($temp[$i][4] == "Inactive" || $temp[$i][4] == "Admin")
			unset($actives[$i]);
		elseif ($temp[$i][4] == "Mentor") {
			//array_push($actives[$i], "");
			array_push($mentors, $actives[$i]);
		}
		elseif ($temp[$i][4] == "Mentee")
			array_push($mentees, $actives[$i]);
		else {
			//get rid of the mentor/mentee interpolates and push each
			//half into the appropriate mentee/mentee array
			$copy = $copy2 = $temp;
			$copy[$i][5] = str_replace("<b>Mentor: </b>", "", $copy[$i][5]);
			$pos = strpos($copy[$i][5], "<br");
			$copy[$i][5] = substr_replace($copy[$i][5], "", $pos, 9999);
			$copy[$i][4] = "Mentor";
			array_push($mentors, $copy[$i]);
			$pos = strpos($copy2[$i][5], "e: </b>");
			$copy2[$i][5] = substr_replace($copy2[$i][5], "", 0, $pos+7);
			$copy2[$i][4] = "Mentee";
			array_push($mentees, $copy2[$i]);
		}
	}
	if($role == 1) {
		$temp = $mentors;
		$sql = "SELECT Mentor.uname FROM personmatches INNER JOIN person 
				AS `Mentor` ON Mentor.id = personmatches.mentor_id";
		$result = $GLOBALS['conn']->query($sql);
		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$uname = $row['uname'];
				for($i = 0; $i < count($temp); $i++) {
					if ($temp[$i][1] == $uname)
						unset($mentors[$i]);
				}
			}
		}
		return $mentors;
	}
	else {
		$temp = $mentees;
		$sql = "SELECT Mentee.uname FROM personmatches INNER JOIN person 
				AS `Mentee` ON Mentee.id = personmatches.mentee_id";
		$result = $GLOBALS['conn']->query($sql);
		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$uname = $row['uname'];
				for($i = 0; $i < count($temp); $i++) {
					if ($temp[$i][1] == $uname)
						unset($mentees[$i]);
				}
			}
		}
		return $mentees;
	}
}

//add a match to the personmatches table
//$match = "mentee_username, mentor_username"
function addMatch($match) {

//change the string to an array (format: array(mentee, mentor))
$match_array = explode(", ", $match);

//get the person id of both
$e = $match_array[0];
$o = $match_array[1];
$sql = "SELECT `id` FROM `person` WHERE `uname` = \"$e\" UNION ALL 
		SELECT `id` FROM `person` WHERE `uname` = \"$o\"";
$result = $GLOBALS['conn']->query($sql);
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc())
		$ids[] = $row['id'];
}
$e_id = $ids[0];
$o_id = $ids[1];

$sql = "INSERT INTO `PersonMatches` (`mentee_id`, `mentor_id`) VALUES ($e_id, $o_id)";
$GLOBALS['conn']->query($sql);
}

//drop a match from the personmatches table
//$match = "mentor_username, mentee_username"
function deleteMatch($match) {

//change the string to an array (format: array(mentor, mentee))
$match_array = explode(", ", $match);

//get the person id of both
$o = $match_array[0];
$e = $match_array[1];
$sql = "SELECT `id` FROM `person` WHERE `uname` = \"$e\" UNION ALL 
		SELECT `id` FROM `person` WHERE `uname` = \"$o\"";
$result = $GLOBALS['conn']->query($sql);
if($result->num_rows > 0) {
	while($row = $result->fetch_assoc())
		$ids[] = $row['id'];
}
$o_id = $ids[1];
$e_id = $ids[0];

$sql = "DELETE FROM `PersonMatches` WHERE `mentor_id` = $o_id AND `mentee_id` = $e_id";
$GLOBALS['conn']->query($sql);	
}

//get the cat-ted string version of the a person's skills
//$p_id = person id
//$role = "mentor", "mentee", "both"
function getSubjNames($p_id, $role) {
		
	//get the subj_ids
	$subj_ids = $s_ids = array();
	$table = array("`mentorsubjects`", "`menteesubjects`");
	for ($k = 0; $k < 2; $k++){
		$sql = "SELECT `subject_id` FROM $table[$k] WHERE `person_id` = $p_id";
		$results = $GLOBALS['conn']->query($sql);
		if($results->num_rows > 0) {
			while($row = $results->fetch_assoc())
				array_push($s_ids, $row['subject_id']);
		}
		$subj_ids[$k] = $s_ids;
		$s_ids = array();
	}
	$mentee_count = count($subj_ids[1]);
	$mentor_count = count($subj_ids[0]);

	if($role == "both"){
		if ($mentor_count > 0 && $mentee_count > 0) {
			$o_s_ids = $subj_ids[0];
			$e_s_ids = $subj_ids[1];
			$o_s_nms = $o_s_names = array();
			$e_s_nms = $e_s_names = array();
			for($k = 0; $k < count($o_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $o_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($o_s_nms, $row['subjectName']);
				}
			}
			for($k = 0; $k < count($e_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $e_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($e_s_nms, $row['subjectName']);
				}
			}
			$o_s_names = implode(", ", $o_s_nms);
			$e_s_names = implode(", ", $e_s_nms);
			$subj_names = "<b>Mentor: </b>" . "$o_s_names <br/> <b>Mentee: </b>" . "$e_s_names";
		}
		elseif ($mentor_count > 0 &&  $mentee_count < 1) {
			$o_s_ids = $subj_ids[0];
			$o_s_nms = array();
			for($k = 0; $k < count($o_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $o_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($o_s_nms, $row['subjectName']);
				}
			}
			$subj_names = implode(", ", $o_s_nms);
			$subj_names = "<b>Mentor: </b>" . "$subj_names";
		}
		elseif($mentor_count < 1 && $mentee_count > 0){
			$e_s_ids = $subj_ids[1];
			$e_s_nms = array();
			for($k = 0; $k < count($e_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $e_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($e_s_nms, $row['subjectName']);
				}
			}
			$subj_names = implode(", ", $e_s_nms);
			$subj_names = "<b>Mentee: </b>" . "$subj_names";
		}
	}
	elseif($role == "mentor") {
		$o_s_ids = $subj_ids[0];
			$o_s_nms = array();
			for($k = 0; $k < count($o_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $o_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($o_s_nms, $row['subjectName']);
				}
			}
			$subj_names = implode(", ", $o_s_nms);
	}	
	else {
		$e_s_ids = $subj_ids[1];
			$e_s_nms = array();
			for($k = 0; $k < count($e_s_ids); $k++) {
				$sql = "SELECT subjectName FROM subject WHERE id = $e_s_ids[$k]";
				$result = $GLOBALS['conn']->query($sql);
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc())
						array_push($e_s_nms, $row['subjectName']);
				}
			}
			$subj_names = implode(", ", $e_s_nms);
	}
	return $subj_names;
}

//get an array of the subjects in the subject table
function getSubjects() {
	
	$subj_names = array();
	//get all of the subjects
	$sql = "SELECT `subjectName` FROM `subject`";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0){
		while($row = $result->fetch_assoc())
			array_push($subj_names, $row['subjectName']);
	}
	return $subj_names;
}
	
//get subjects for the dual list box (returns an array with 
//the person's selected subjects prepended with a "!")
function getBoxSubjs($email, $role) {

	$subj_names = array();

	//assuming you have email here, get the person's person_id
	$sql = "SELECT `id` FROM `person` WHERE `email` = \"$email\"";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$p_id = $row['id'];
	}

	$subj_names = getSubjects();
	
	//get the string of the user's subjects
	$subj_str = getSubjNames($p_id, $role);
	
	//get rid of the commas
	$pers_subj = explode(", ", $subj_str);
	
	//prepend a "!" to the subjects that the user has already selected
	for($i = 0; $i < count($pers_subj); $i++) {
		for($k = 0; $k < count($subj_names); $k++) {
			if($pers_subj[$i] == $subj_names[$k]) {
				$subj_names[$k] = "!" . $subj_names[$k];
				continue;
			}
		}
	}
	return $subj_names;
}

//get the role for a person (return values will be "Admin", "Inactive", 
// "Pending", "Mentor", "Mentee", and "Mentor/Mentee")
function getRoles($p_id) {
	
	$roles = array();
	
	//get the person's role_ids
	$sql = "SELECT `role_id` FROM `personroles` WHERE `person_id` = $p_id";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc())
			array_push($roles, $row['role_id']);
	}
	
	if (count($roles) == 0)
		return "Inactive";
	elseif(count($roles) == 1) {
		if ($roles[0] == 1)
			return "Admin";
		elseif ($roles[0] == 2)
			return "Mentor";
		elseif ($roles[0] == 3)
			return "Mentee";
		elseif ($roles[0] == 4)
			return "Pending";
	}
	else {
		$rolestr = implode(", ", $roles);
		if(strpos($rolestr, "4"))
			return "Pending";
		else
			return "Mentor, Mentee";
		
	}
	
}

//when a person changes skills, send it to the table
function sendSkills($email, $role, $skills) {
	
	//first get the p_id
	$sql = "SELECT `id` FROM `person` WHERE `email` = \"$email\"";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$p_id = $row['id'];
	}
	
	//next, drop all skills in the table associated with the p_id
	if($role == "mentor")
		$table = "`mentorsubjects`";
	elseif($role == "mentee")
		$table = "`menteesubjects`";
		
	$sql = "DELETE FROM $table WHERE `person_id` = $p_id";
	$result = $GLOBALS['conn']->query($sql);

	//next, get the skill ids
	$skill_ids = array();
	for($i = 0; $i < count($skills); $i++) {
		$sql = "SELECT `id` FROM `subject` WHERE `subjectName` LIKE \"$skills[$i]\"";
		$result = $GLOBALS['conn']->query($sql);
		if($result->num_rows > 0) {
			while ($row = $result->fetch_assoc())
				array_push($skill_ids, $row['id']);
		}
	}
	
	//last, construct an insert string and send it to the db
	$sql = "INSERT INTO $table (`person_id`, `subject_id`) VALUES ";
	for($i = 0; $i < count($skill_ids); $i++){
		$sql .= "($p_id , $skill_ids[$i])";
		if($i < (count($skill_ids)-1))
			$sql .= ", ";
		else
			$sql .= ";";
	}
	$result = $GLOBALS['conn']->query($sql);
	
	return getBoxSubjs($email, $role);
}

# this function concats and returns a string of the usernames for all the current 
# relationships of a given mentor or mentee (table decides which relationships)
function logInsert($user_dropping, $table, $type){
	if($type == "drop"){
		$role = $table;
		$table .= "_id";
		# get all current match ids for matches that given user is a part of
		$match_id_query = "select PersonMatches.id from PersonMatches, Person where person.uname = '$user_dropping' and person.id = PersonMatches." . "$table";  //OR person.id = PersonMatches.mentee_id)
		$match_id_results = $GLOBALS['conn']->query($match_id_query);
		while($resulting_id_array[]=mysqli_fetch_array($match_id_results));

		# construct comma seperated list of returned match ids
		$resulting_ids="";
		foreach($resulting_id_array as $key=>$value){
			if(is_array($value)){
				if(isset($value[0])){
					$resulting_ids.="\"".$value[0]."\",";
					}
			   }
		}
		$resulting_ids = rtrim($resulting_ids,',');

		# get all usernames that are part of any affected matches
		$affected_users_query = "select person.uname from Person, PersonMatches where PersonMatches.id in ($resulting_ids) and person.id = PersonMatches." . "$table";
		$affected_user_results = $GLOBALS['conn']->query($affected_users_query);
		
		# get current date into sql date format
		date_default_timezone_set('America/Chicago');
		$getDate = date('Y-m-d H:i:s');
		$mysqldate = date( 'Y-m-d H:i:s', strtotime( $getDate ) );
			
		//don't push anything to the log table if the change doesn't affect
		//any relationships
		if($affected_user_results->num_rows == 0) {
			$affected_relationships = "User $user_dropping amended the $role roles table. No relationships were affected.";
			$users_change_role_qry = "insert into logs (time_stamp, event_description) values ('$mysqldate', '$affected_relationships')";
			$insert_users_change_role = $GLOBALS['conn']->query($users_change_role_qry);
			return $affected_relationships;
		}
			
		while($affected_users_array[]=mysqli_fetch_array($affected_user_results));
		# construct list of affected usernames
		$affected_users="";
		foreach($affected_users_array as $key=>$value){
			if(is_array($value)){
				if(isset($value[0])){
					# if current value is not the user dropping, and does not already exist in list then
					if (($value[0] != $user_dropping) && (strpos($affected_users, '$value[0]') == false)){
						$affected_users.=$value[0].", ";
						}
					}
			   }
		}
		$updated_role_string = "User $user_dropping dropped a $role role. Relationships with the following users may have been dropped: $affected_users";
		# replace the last comma and space from the list with a period.
		$affected_relationships = substr_replace($updated_role_string ,".",-2);
		
		# build query to insert into logs
		$users_change_role_qry = "insert into logs (time_stamp, event_description) values ('$mysqldate', '$affected_relationships')";
		
		# attempt to insert
		$insert_users_change_role = $GLOBALS['conn']->query($users_change_role_qry);
		
		return $affected_relationships;
	}
	elseif ($type == "add"){
		
		# get current date into sql date format
		date_default_timezone_set('America/Chicago');
		$getDate = date('Y-m-d H:i:s');
		$mysqldate = date( 'Y-m-d H:i:s', strtotime( $getDate ) );
		
		//send a message to the log about an added user role			
		$affected_relationships = "User $user_dropping added a $table role to the roles table. No relationships were affected.";
		
		# build query to insert into logs
		$users_change_role_qry = "insert into logs (time_stamp, event_description) values ('$mysqldate', '$affected_relationships')";
		
		# attempt to insert
		$insert_users_change_role = $GLOBALS['conn']->query($users_change_role_qry);
	}
}

//this funtion prints all of the data in the logs table
function printLogs() {
	$line = $lines = array();
	$sql = "SELECT * FROM `logs`"; 
	$results = $GLOBALS['conn']->query($sql);
	if ($results->num_rows > 0) {
		while ($row = $results->fetch_assoc()) {
			array_push($line, $row['id'], $row['time_stamp'], $row['event_description']);
			array_push($lines, $line);
			$line = array();
		}
	}
	
	return $lines;
}

//this function removes the role and relationship for the user that deleted a role
function removeRoleRel($uname, $role) {
		//determine the table
		if($role == "mentor") {
			$id = "`mentor_id`";
			$role_id = 2;
		}
		elseif($role == "mentee") {
			$id = "`mentee_id`";
			$role_id = 3;
		}
		else{
			echo "$role <br/>";
			return $role;
		}
		
		//first get the p_id
		$sql = "SELECT `id` FROM `person` WHERE `uname` = \"$uname\"";
		$result = $GLOBALS['conn']->query($sql);
		if ($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$p_id = $row['id'];
		}
		
		//next delete the role from the table
		$sql = "DELETE FROM `personroles` WHERE `person_id` = $p_id AND `role_id` = $role_id";
		$result = $GLOBALS['conn']->query($sql);
		
		//last delete the relationships
		$sql = "DELETE FROM `personmatches` WHERE \"$id\" = $p_id"; 
		$result = $GLOBALS['conn']->query($sql);
}
 
//this function deletes the selected log entries
function deleteLog($nums) {
	for ($i = 0; $i < count($nums); $i++){
		$sql = "DELETE FROM `logs` WHERE `id` = $nums[$i]";
		$result = $GLOBALS['conn']->query($sql);
	}
}
 
function insertNewSkills($skills) {
	for($i = 0; $i < count($skills); $i++) {
		//check if the skill is already in the table
		$sql = "SELECT * FROM `subject` WHERE `subjectName` LIKE \"$skills[$i]\"";
		$result = $GLOBALS['conn']->query($sql);
		if ($result->num_rows < 1) {
			$sql = "INSERT INTO `subject` (`subjectName`) VALUES (\"$skills[$i]\")";
			$GLOBALS['conn']->query($sql);
		}
	}
}

function approveUser($email){
		$sql = 	"DELETE * FROM PersonRoles as pr
					LEFT JOIN Person as p
						ON pr.person_id = p.id
					LEFT JOIN Role as r
						ON r.id = pr.role_id
					WHERE r.roleName LIKE 'Pending' AND p.email = '$email'"; 
		$result = $GLOBALS['conn']->query($sql);
		if ($result){
			echo "Success!";
		} else {
			echo mysqli_error($GLOBALS['conn']);
		}
}
function GetPendingUsers(){
	$results=array();
	$pending_users_query = "select Person.email from Person, PersonRoles, Role where Person.id = PersonRoles.person_id AND PersonRoles.role_id = Role.id AND Role.roleName = 'pending'";
	$pending_user_results = $GLOBALS['conn']->query($pending_users_query);
	if ($pending_user_results){
		while($pending_user_array=mysqli_fetch_array($pending_user_results)){
			array_push($results, $pending_user_array['email']);
			}
		$results = array_filter($results);
	}
	
	return $results;
}

//will return true is user has role (need to input role number)
function roleCheck($p_id, $role){
	$sql = "SELECT * FROM `personroles` where `person_id` = $p_id AND `role_id` = $role";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0)
		return true;
	else
		return false;
}
?>
