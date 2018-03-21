<?php
	include 'sql.php';
	$use_ldap = false;	//Only used to access public profile info; disabled temporarily - certs needed for public use
	
	$session_is_admin = false;
	$session_is_mentor = false;
	$session_is_mentee = false;
	$session_is_pending = false;
	
	$user_is_admin = false;
	$user_is_mentor = false;
	$user_is_mentee = false;
	$user_is_pending = false;
	
	$user_is_session = false;
	
	$user = false;
	$user_info = false;
	
	//Get token for session
	session_start();	
	if (isset($_SESSION['email'])){
		$session_email = $_SESSION['email'];
	} else {
			//sign-out and clear session
			session_unset();
			// Redirect to signin page
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'signin.php';
			header("Location: http://$host$uri/$extra");
			die();
	}
	
	//Get account being viewed
	if (isset($_GET['email'])){
		$email = $_GET['email'];
		//Check if the account being viewed is the same as the current session
		$user_is_session = $session_email === $email;
	} else{
		$email = $session_email;
		//The account being viewed is the same as the current session
		$user_is_session = true;
	}
	
	//get some relevant info
	$p_id = getIdByEmail($email);
	$uname = getUsername($email);
	$role_string = getRoles($p_id);
	
	//Query sql for user that's signed in
	include_once 'sql.php';
	if ($user_rows = GetUser($session_email)){
		//There should be one result per role
		if ($user_rows->num_rows > 0){
			while($row = $user_rows->fetch_assoc()){
	//			$user = $row;
				if (strtolower($row["roleName"]) === 'admin'){
					$session_is_admin = true;
				} else if (strtolower($row["roleName"]) === 'mentor'){
					$session_is_mentor = true;
				} else if (strtolower($row["roleName"]) === 'mentee'){
					$session_is_mentee = true;
				} else if (strtolower($row["roleName"]) === 'pending'){
					$session_is_pending = true;
				}
			}
		}
	} 

	if (!$user_is_session && !$session_is_admin){
	
		$email = $session_email;
	}
	//Query sql for user that's being viewed
	if ($user_rows = GetUser($email)){
		//There should be one result per role
		if ($user_rows->num_rows > 0){
			while($row = $user_rows->fetch_assoc()){
				$user = $row;
				if (strtolower($row["roleName"]) === 'admin'){
					$user_is_admin = true;
				} else if (strtolower($row["roleName"]) === 'mentor'){
					$user_is_mentor = true;
				} else if (strtolower($row["roleName"]) === 'mentee'){
					$user_is_mentee = true;
				} else if (strtolower($row["roleName"]) === 'pending'){
					$user_is_pending = true;
				}
			}

		} else {			
			//user roles not found
		}
	} else {
		//user roles not found
	}
	if ($user_rows = GetUserProfile($email)){
		//There should be one result per role
		if ($user_rows->num_rows > 0){
			while($row = $user_rows->fetch_assoc()){
				$user = $row;
			}
		} else {			
			//user profile not found
		}
	}
	
	//Get user's skills
	$subj_names = array();
	$sql = "SELECT Subject.subjectName FROM Person
    JOIN MentorSubjects
        ON MentorSubjects.person_id = Person.id
    JOIN Subject
        ON Subject.id = MentorSubjects.subject_id
    WHERE Person.email = '$email'";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc())
			array_push($subj_names, $row['subjectName']);
		
	}
	$mentor_skills= implode(", ", $subj_names);	
	
	$subj_names = array();
	$sql = "SELECT Subject.subjectName FROM Person
    JOIN MenteeSubjects
        ON MenteeSubjects.person_id = Person.id
    JOIN Subject
        ON Subject.id = MenteeSubjects.subject_id
    WHERE Person.email = '$email'";
	$result = $GLOBALS['conn']->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc())
			array_push($subj_names, $row['subjectName']);
		
	}
	$mentee_skills= implode(", ", $subj_names);
	
$mentee_subjects = getBoxSubjs($email, 'mentee');
$mentor_subjects = getBoxSubjs($email, 'mentor');

	//====AJAX stuff=========
	//Clear logs
	//get the line numbers from the log table that are checked
	
	if(isset($_POST['checked'])) {
		$deleteLines = array();
		foreach($_POST['checked'] as $lineNum) {
			array_push($deleteLines, $lineNum);
		}
		deleteLog($deleteLines);
	}

	
	
	//Approve new user
	if (isset($_POST['approve'])){
		
		if (deletePending(getIdByEmail($_POST['approve'])) && $conn->affected_rows > 0){
			echo "User approved";
		} else {
			echo "Unable to approve user; unknown error occurred";
		}
		die();
	}
	//Update password
	if (isset($_POST['newpwd'])){
		$new_pass = $_POST['newpwd'];
		$old_pass = $_POST['password'];
		$session_is_validated = false;
		//Confirm password
		$sql = "UPDATE `person` SET pass='$new_pass'  WHERE email = '$session_email' and pass = '$old_pass'";
        $result = $conn->query($sql);
        if($result  === TRUE && $conn->affected_rows > 0 ){
			//Updated password
			echo "Password changed successfully"; 
			die();
        } else {
			//User was not authorized to make request
			echo "Unauthorized to make request"; 
			http_response_code(403);
			die();
		}
	}
	//Set mentor status
	if (isset($_POST['mentor'])){
		$is_m = $_POST['mentor'];
		//Get id of existing user
		$sql = "SELECT id FROM person WHERE email = '$email'";
        $result= $conn->query($sql);
            
        if( $result-> num_rows > 0 ){
            while($row = $result ->fetch_assoc()){
				$person_id = $row['id'];
             }
         }else{
            echo "User does not exist"; 
			http_response_code(500);
			die();
        }
		
		//Get id of role
		$role_id = 2;
			
		 //Clear existing role
		$sql ="DELETE FROM personroles
			WHERE person_id='$person_id' AND role_id='$role_id'";
		$conn->query($sql) ;
		
		if ($is_m){
        $sql = "INSERT INTO PersonRoles (person_id, role_id)
			VALUES ($person_id, $role_id)";
		if ($conn->query($sql) == true){
            echo"User is now an mentor.";
        }
         else{
            echo "Unable to change user role"; 
			http_response_code(500);
			die();
		}
		}
		//Request completed successfully
		die();
	}
	//Set mentee status
	if (isset($_POST['mentee'])){
		$is_m = $_POST['mentee'];
		//Get id of existing user
		$sql = "SELECT id FROM person WHERE email = '$email'";
        $result= $conn->query($sql);
            
        if( $result-> num_rows > 0 ){
            while($row = $result ->fetch_assoc()){
				$person_id = $row['id'];
             }
         }else{
            echo "User does not exist"; 
			http_response_code(500);
			die();
        }
		
		//Get id of role
		$sql = "SELECT id FROM role WHERE roleName LIKE 'mentee'";
        $result= $conn->query($sql);
            
        if( $result-> num_rows > 0 ){
            while($row = $result ->fetch_assoc()){
                $role_id = $row['id'];
            }
        }else{
            echo "An error occured"; 
			http_response_code(500);
			die();
         }
		 //Clear existing role
		$sql ="DELETE FROM personroles
			WHERE person_id='$person_id' AND role_id='$role_id'";
		$conn->query($sql) ;
		
		if ($is_m){
        $sql = "INSERT INTO PersonRoles (person_id, role_id)
			VALUES ($person_id, $role_id)";
		if ($conn->query($sql) == true){
            echo"User is now an mentee.";
        }
         else{
            echo "Unable to change user role"; 
			http_response_code(500);
			die();
		}
		}
		//Request completed successfully
		die();
	}
	//Add new admin if requested
	if (isset($_POST['newadm'])){
		$new_admin = $_POST['newadm'];
		$password = $_POST['password'];
		$session_is_validated = false;
		//Confirm password
		$sql = "SELECT email FROM `person` WHERE email = '$session_email' and pass = '$password'";
        $result = $conn->query($sql);
        if($result-> num_rows > 0 ){
			$session_is_validated = true;
        }
		//Confirm user is validated to make the request
		if ($session_is_admin && $session_is_validated){
		
			//Get id of role
			$sql = "SELECT id FROM role WHERE roleName LIKE 'admin'";
                $result= $conn->query($sql);
            
                if( $result-> num_rows > 0 ){
                    while($row = $result ->fetch_assoc()){
                       $role_id = $row['id'];
                    }
                }else{
                    echo "An error occured"; 
					http_response_code(500);
					die();
                }
                
				//Get id of existing user
			$sql = "SELECT id FROM person WHERE email = '$new_admin'";
                $result= $conn->query($sql);
            
                if( $result-> num_rows > 0 ){
                    while($row = $result ->fetch_assoc()){
                       $person_id = $row['id'];
                    }
                }else{
                    echo "User does not exist"; 
					http_response_code(500);
					die();
                }
                $sql = "INSERT INTO PersonRoles (person_id, role_id)
				VALUES ($person_id, $role_id)";
			     if ($conn->query($sql) == true){
                    echo"$new_admin is now an admin.";
                }
                else{
                    echo "Unable to add admin"; 
					http_response_code(500);
					die();
                }
			//Request completed successfully
			die();
		}
		//User was not authorized to make request
		echo "Unauthorized to make request"; 
		http_response_code(403);
		die();
	}
	
	if(isset($_POST['mentor_user_skill'])){
		//Add options from multi-select box		
		$re= "/[ ,]\s*/";
		$skills = preg_split($re, $_POST['multiselect'], 0, PREG_SPLIT_NO_EMPTY);
		$u_skills = preg_split($re, $_POST['mentor_user_skill'], 0, PREG_SPLIT_NO_EMPTY);
		$skills = array_unique(array_merge($skills, $u_skills));
		insertNewSkills($skills);
		$mentor_subjects = sendSkills($email, 'mentor', $skills);
		
		//End of request
		echo "Skills updated";
		die();
	}
	
	if(isset($_POST['mentee_user_skill'])){
		$re= "/[ ,]\s*/";
		$skills = preg_split($re, $_POST['multiselect'], 0, PREG_SPLIT_NO_EMPTY);
		$u_skills = preg_split($re, $_POST['mentee_user_skill'], 0, PREG_SPLIT_NO_EMPTY);
		$skills = array_values(array_unique(array_merge($skills, $u_skills)));
		insertNewSkills($skills);
		$mentor_subjects = sendSkills($email, 'mentee', $skills);
	
		//End of request
		echo "Skills updated";
		die();
	}

	
	//Get misc info about user from ldap if available
	if ($use_ldap){
		include 'ldap.php';
		$user_info = ldap_find_user_by_email($user["email"]);
	} else {

		if (($user)){
			$user_info["cn"][0]  = $user["fName"]." ".$user["lName"];
			$user_info["mail"][0]  = $user["email"];
			$user_info["title"][0]  = $user["classification"];
		}
	}
	//====End AJAX stuff=========
?>
<!DOCTYPE html>
<html>
   <head>
      <title>Account Settings
      </title>
	  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
      <link rel="stylesheet" type="text/css" href="style2.css">
      
   </head>
   <body>
      <?php
			//Navigation header
			include 'navbar.php';
	  ?>
      <div class = "pane-header float-wrapper">
         <div class="profile-border" >
			<?php
				if (isset($user_info["uid"])){
					echo "<img  class=\"profile-pic\" src=\"http://www.mtsu.edu/empimages/".$user_info["uid"][0].".jpg\" alt=\"Image not found\" 	onerror=\"this.onerror=null;this.src='./Images/account.png';\"/>";
				} else {
					echo "<img  class=\"profile-pic\" src=\"./Images/account.png\" alt=\"Image not found\"/>";
				}
			?>
         </div>
         <div class="floating" style = "color:#EEE;">
            <br>
            <b style = "color:#FFF;">
			<?php
				echo $user_info["cn"][0] ;
			?>
			</b>
            <br>
            <?php
				echo $user_info["mail"][0];
			?>
            <br>
            <?php
				echo $user_info["title"][0] ;
			?>
         </div>
      </div>
      <div class = "pane-back float-wrapper">
		<!-- Tab Navigation -->
		<div class="pane-left">
         <ul class="pane-nav">
			<?php
				//Only include Admin tab if user is administrator
				echo '<li><a class="pane-nav-item active-tab" onclick="openPane(event,\'users\')" href="#users">Users</a></li>';
				if ($user_is_admin) {				
					echo '<li><a class="pane-nav-item" onclick="openPane(event,\'admin\')" href="#admin">Admin</a></li>';
					echo '<li><a class="pane-nav-item" onclick="openPane(event,\'notifications\')" href="#notifications">Notifications</a></li>';
				} else {
					echo '<li><a class="pane-nav-item" onclick="openPane(event,\'seekingskills\')" href="#seekingskills">Seeking Skills</a></li>';
					echo '<li><a class="pane-nav-item" onclick="openPane(event,\'knownskills\')" href="#knownskills">Mentoring Skills</a></li>';
				}
			?>
			
            <li><a class="pane-nav-item" onclick="openPane(event, 'settings')" href="#settings">Settings</a></li>
         </ul>
		 </div>
		<!--Tab Content-->
		<div class="pane-right">
		<!-- Admin Tab -->
		<?php
		//Only include this tab page if current user is an admin
		
			echo '<div id="users" class="pane">';
			
			if ($user_is_admin) {
				echo "<table id=\"logtable\">\n";
				echo "<tr><th>Username</th><th>Status</th></tr>\n";
				$pending_users = GetPendingUsers();
				foreach ($pending_users as $user) {
					echo "<tr><td>$user</td><td><input type=\"button\" class=\"button1\" value=\"Approve\" onclick=\"approveUser('$user')\"/></td></tr>\n";
				}
				echo "</table>";
				echo '<p id="approveresult"></p>';
					echo '<h3>Users</h3>';
					echo '<b>Mentors</b>';
                    echo '<br>';
					$users = GetMentors();
					if ($users){
					// output data of each row
					while($row = $users->fetch_assoc()) {
						echo "email: " . $row["email"]. " - Name: " . $row["fName"]." ".$row["lName"]. "<br>";
					}
			} else {
				echo "No mentors found <br>";
			}
			echo "<hr><b>Mentees</b><br>";
			$users = GetMentees();
			if ($users){
				// output data of each row
				while($row = $users->fetch_assoc()) {
					echo "email: " . $row["email"]. " - Name: " . $row["fName"]." ".$row["lName"]. "<br>";
				}
			} else {
				echo "No users are seeking a mentor<br>";
			}
			} else {
				Get_Relationships_Table($email);
			}
			
			echo "</div>";
			if ($user_is_admin) {
				
			echo <<<ADMINTAB
			<div id="admin" class="pane" style="display:none">
			 		<h3>Admin</h3>
					<b>Add new Admin</b>
                    <br>
					<form onsubmit="return addAdmin();" action="#" method="post">
                        <table>
                           <tr>
                              <td>
                                 <label>New admin's email</label>
                              </td>
                              <td>
                                 <input type="text" id="newadm" name="newadm">
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <label>Confirm email</label>
                              </td>
                              <td>
                                 <input type="text" id="confirmadm" name="confirmadm">
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <label>Your Password</label>
                              </td>
                              <td>
                                 <input type="password" id="password" name="password">
                              </td>
                           </tr>
                        </table>
						<br>
						<p id="admresult" ></p>
						<br>
						<input class = "button1" type="submit" value="Submit">
					</form>
			<!-- ADMINTAB; must not be indented -->
ADMINTAB;
			echo "<hr><b>Admins</b><br>";
			$users = GetAdmins();
			if ($users){
				// output data of each row
				while($row = $users->fetch_assoc()) {
					echo "email: " . $row["email"]. " - Name: " . $row["fName"]." ".$row["lName"]. "<br>";
				}
			} else {
				echo "No admins found<br>";
			}


			//Close the tab container
			echo "</div>";
			echo '<div id="notifications" class="pane" style="display:none">';
			echo '<h3>Notifications</h3>';
				
			//for printing to admin accounts page:
	
			$lines = printLogs();
			//logInsert('msp4k');
			echo "<br/>";
			echo "<div class=\"logform float-wrapper\">";
			echo "<form action=\"account.php#notifications\" method=\"post\">";
			echo "<table id=\"logtable\"><tr><th>Item</th><th>Date</th><th>Message</th><th>Select</th></tr>";
			foreach ($lines as $line) {
				echo "<tr>";
				for($i = 0; $i < 3; $i++) {
					if ($i == 0)
						$item_no = $line[0];
					echo "<td>$line[$i]</td>";
				}
				echo "<td><input type=\"checkbox\" name=\"checked[]\" value=$item_no /></tr>";
			}
			echo "</table><input type=\"submit\" class=\"button1\" value=\"Clear Selected\"/>";
			echo "</form>";
			echo "</div>";
			echo "</div>";
		}
		
		?>
		<div id="seekingskills" class = "pane" style="display:none">
		<h3>Seeking Skills</h3>
            <form action="#" onsubmit="return menteeUpdate();" method="post">
			
               <?PHP 
			   echo '<select id="mentee_skill_multiselect" name="mentee_skill_multiselect[]" class="multiselect" multiple="multiple">';
			
			foreach($mentee_subjects as $item){
				if(strpos($item, "!") === 0) {
					$skill = substr($item, 1);
					echo "<option value=\"$skill\" selected>$skill</option>";
				}
				else 
					echo "<option value=\"$item\">$item</option>";
			  }
			  
		
			echo '</select>';
               echo '<b>Enter skills you would like to learn:</b><br>';
			   
			   echo "<label class=\"mod\"><input type=\"text\" id=\"mentee_user_skill\"name=\"mentee_user_skill\" size=\"60\" value=\"\"/> *comma/space separated</label>"
			   ?>
			   <br>
			   <p id="menteeresult"></p>
               <br>
               <input class = "button1" type="submit" value="Save">
            </form>
         </div>
		 <div id="knownskills" class = "pane" style="display:none">
            <h3>Mentoring Skills</h3>
            <form action="#" onsubmit="return mentorUpdate();" method="post">
               <?PHP 
			   echo '<select id="mentor_skill_multiselect" name="mentor_skill_multiselect[]" class="multiselect" multiple="multiple">';
			
			foreach($mentor_subjects as $item){
				if(strpos($item, "!") === 0) {
					$skill = substr($item, 1);
					echo "<option value=\"$skill\" selected>$skill</option>";
				}
				else 
					echo "<option value=\"$item\">$item</option>";
			  }
			  
		
			echo '</select>';
               echo '<b>Enter your own skills:</b><br>';
			   
			   echo "<label class=\"mod\"><input type=\"text\" id=\"mentor_user_skill\"name=\"mentor_user_skill\" size=\"60\" value=\"\"/> *comma/space separated</label>"
			   ?>
			   <br>
			   <p id="mentorresult"></p>
               <br>
               <input class = "button1" type="submit" value="Save">
            </form>
         </div>
		 <!--Settings tab -->
         <div id="settings" class = "pane" style="display:none">
            <h3>Account Settings</h3>
            <form action="#"  method="post" onsubmit="return updateAccount();">
				<?PHP
					if (!$user_is_admin){
				echo '<b><u>User Role</u></b>';
				echo '<br><br>';
				echo "<label>I am seeking a mentor:&nbsp<input type=\"checkbox\" id=\"mentee\" name=\"mentee\" value=\"Mentee\"".($user_is_mentee?"checked":"")."></label>";
				echo '<br>';               
				echo "<label>I would like to be a mentor:&nbsp<input type=\"checkbox\" id=\"mentor\" name=\"mentor\" value=\"Mentor\"".($user_is_mentor?"checked":"")."></label>";
				echo '<br>';
                echo '<hr>';
				}
				?>
               <div class ="float-wrapper">
                  <div class ="float-wrapper" style="float:left;">
                     <div class ="floating">
                        <b><u>Change Password</u></b>
                        <br><br>
                        <table class="acct">
                           <tr>
                              <td>
                                 <label class="acct">Previous Password:&nbsp</label>
                              </td>
                              <td>
                                 <input type="password" id="previouspwd"name="previouspwd">
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <label class="acct">New Password:&nbsp</label>
                              </td>
                              <td>
                                 <input type="password" id="newpwd" name="newpwd">
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <label class="acct">Confirm Password:&nbsp</label>
                              </td>
                              <td>
                                 <input type="password" id="confirmpwd"name="confirmpwd">
                              </td>
                           </tr>
                        </table>
                     </div>
					 <!-- Temporarily hidden -->
                     <div class = "floating" style="display:none">
                        <b>Password Rules</b>
                        <ul>
                           <li>Must contain atleast 8 characters</li>
                           <li>Must contain letter (eg: a-z or A-Z)</li>
                           <li>Must contain number (eg: 0-9)</li>
                           <li>Must contain special character (eg: !@#$%^)</li>
                        </ul>
                     </div>
                  </div>
               </div>
               <br>
			   <p id="pwdresult"></p>
			   <br>
               <input class = "button1" type="submit" value="Save">
            </form>
         </div>
      </div>
	  </div>
	  <?PHP
	  //Do not load this script for non-admins
	  if ($user_is_admin){
	  echo <<<ADDADMIN
	  <script>
	  function addAdmin() {
			var newadmEmail=document.getElementById("newadm").value;
			var confirmadmEmail=document.getElementById("confirmadm").value;
			var password=document.getElementById("password").value;
			if (newadmEmail !== confirmadmEmail){
				document.getElementById("admresult").innerHTML = "E-mail adresses do not match";
				return false;
			}
			if (password.length == 0) { 
				document.getElementById("admresult").innerHTML = "Please enter your password";
				return false;
			}
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					//if (this.status == 200){
					//	//document.getElementById("txtHint").innerHTML = this.responseText;
					//} else {
						document.getElementById("admresult").innerHTML = this.responseText;
					//}
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("newadm="+encodeURIComponent(newadmEmail)+"&password="+password);
			
			return false;
		}
		</script>
ADDADMIN;
}
		?>
		
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
<script src="multiselect.js"></script>
<!--<script src="quicksearch.js"></script>-->
<script type="text/javascript">
	var selected = []
    $('.multiselect').multiSelect({
		selectableHeader: "<div class='custom-header'>Selectable Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"C++\"'>",
		selectionHeader: "<div class='custom-header'>Selected Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"Python\"'>",
		dblClick: true,
		afterInit: function(ms){
			selected[this.$container.attr('id')] = [];

			for (var i=0;i<this.$element[0].length;i++)
			{
				if (this.$element[0][i].selected)
				{
					selected[this.$container.attr('id')].push(this.$element[0][i].value)
				}
			}
			var that = this,
				$selectableSearch = that.$selectableUl.prev(),
				$selectionSearch = that.$selectionUl.prev(),
				selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
				selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

			that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
			.on('keydown', function(e){
				if (e.which === 40){
					that.$selectableUl.focus();
					return false;
				}
			});

			that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
			.on('keydown', function(e){
				if (e.which == 40){
					that.$selectionUl.focus();
					return false;
				}
			});
		},
		
		afterSelect: function(values){
			selected[this.$container.attr('id')] = selected[this.$container.attr('id')].concat(values);
			this.qs1.cache();
			this.qs2.cache();
		},
		afterDeselect: function(values){
			for (var i = 0; i< values.length; i++){
				var index = selected[this.$container.attr('id')] .indexOf(values[i]);
 
				if (index > -1) {
					selected[this.$container.attr('id')] .splice(index, 1);
				}
			}
			this.qs1.cache();
			this.qs2.cache();
		}
	});

</script>
<script>
	  function updateAccount(){
		<?PHP
			if (!$user_is_admin){
			echo 'changeMenteeStatus();';
			echo 'changeMentorStatus();';
			}
			?>
			changePassword();
			return false;
	  }
		//Update mentor info
		function mentorUpdate(){
			changeMentorSkills();
			return false;
		}
		//Update mentee info
		function menteeUpdate(){
			changeMenteeSkills();
			return false;
		}
		//Change mentor status
		function changeMentorStatus(){		
			var isMentor =document.getElementById("mentor").checked ? 1 : 0;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					//TODO show message
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("mentor="+isMentor);
			
			return false;
		}
		//Change mentee status
		function changeMenteeStatus(){		
			var isMentee =document.getElementById("mentee").checked ? 1 : 0;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					//TODO show message
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("mentee="+isMentee);
			
			return false;
		}
		//Change mentor skills
		function changeMentorSkills(){
		
			var skills=document.getElementById("mentor_user_skill").value;
			var multiselect = selected["ms-mentor_skill_multiselect"];
			var mskills = "";
			var x = 0;

			for (x=0;x<multiselect.length;x++)
			{
					if (mskills.length !== 0){
						mskills += ",";
					}
					mskills = mskills + encodeURIComponent(multiselect[x]);
				
			}
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					document.getElementById("mentorresult").innerHTML = this.responseText;
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("mentor_user_skill="+encodeURIComponent(skills)+"&multiselect="+mskills);
			
			return false;
		}
		//Change mentee skills
		function changeMenteeSkills(){
			
			var skills=document.getElementById("mentee_user_skill").value;
			var multiselect = selected["ms-mentee_skill_multiselect"];
			var mskills = "";
			var x = 0;

			for (x=0;x<multiselect.length;x++)
			{
					if (mskills.length !== 0){
						mskills += ",";
					}
					mskills = mskills + encodeURIComponent(multiselect[x]);
				
			}
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					document.getElementById("menteeresult").innerHTML = this.responseText;
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("mentee_user_skill="+encodeURIComponent(skills)+"&multiselect="+mskills);
			
			return false;
		}
		//Change password
		function changePassword(){
		
			var newPassword=document.getElementById("newpwd").value;
			var confirmPassword=document.getElementById("confirmpwd").value;
			var password=document.getElementById("previouspwd").value;
			if (newPassword !== confirmPassword){
				document.getElementById("pwdresult").innerHTML = "Passwords do not match";
				return false;
			}
			if (newPassword.length == 0){
				//do nothing
				return false;
			}
			if (password.length == 0) { 
				document.getElementById("pwdresult").innerHTML = "Please enter your password";
				return false;
			}
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					
					document.getElementById("pwdresult").innerHTML = this.responseText;
					
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("newpwd="+newPassword+"&password="+password);
			
			return false;
		}
		
		
		function approveUser(email){
		var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					//alert("User approved");
					document.getElementById("approveresult").innerHTML = this.responseText;
					
				}
			};
			xhttp.open("POST", "account.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("approve="+email);
			return false;
		}
		 //Change current tab
         function openPane(event, name) {
             var i;
             var x = document.getElementsByClassName("pane");
             for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
             }
         	tablinks = document.getElementsByClassName("pane-nav-item");
         	for (i = 0; i < tablinks.length; i++) {
         		tablinks[i].className = tablinks[i].className.replace(" active-tab", "");
         	}
            document.getElementById(name).style.display = "block";
			//Set link element to appear active
			if (typeof event.href !== "undefined"){
				//Link element was passed instead of an event
				event.className += " active-tab";
			} else {
				//Get link element from event
				event.currentTarget.className += " active-tab";
			}
         }
		 //Keep current tab selected after form submission
		 var selectedPane = location.hash;
		 if (selectedPane.length > 0){
			var foundTab = false;
			var event;
			var id = selectedPane.substr(1);
			tablinks = document.getElementsByClassName("pane-nav-item");
         	for (i = 0; i < tablinks.length; i++) {
				if (tablinks[i].hash === selectedPane){
					event = tablinks[i];
					foundTab = true;
					break;
				}
         	}
			//Just incase the link was invalid, show the first tab
			if (!foundTab){
				event = tablinks[0];
				id  =	tablinks[0].hash.substr(1);
			}
			openPane(event,id);
		}
      </script>
	  
   </body>
</html>
