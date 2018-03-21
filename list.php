<?php
	include_once 'sql.php';


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
