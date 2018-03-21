
 <?php

include_once 'sql.php';
//get a list of subjects as a global for the multiselect boxes
$subjects = getSubjects();

//if all of the variables are set (user submitted form), then set globals
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['f_name']) 
	&& isset($_POST['l_name']) && isset($_POST['email']) && isset($_POST['classyear'])){
	$username= $_POST['username'];
	$password= md5($_POST['password']);
	$f_name= $_POST['f_name'];
	$l_name= $_POST['l_name'];
	$email= $_POST['email'];	
	$classyear= $_POST['classyear'];


	//if all of the variables are set, start querying the db for a pre-existing acct
	if(isset($username) && isset($password) && isset($f_name) && isset($l_name) && isset($email) && isset($classyear)) {
		$sql = "SELECT uname FROM person WHERE uname='$username'";
		$result= $GLOBALS['conn']->query($sql);
		$sql = "SELECT email FROM person WHERE email='$email'";
		$result2= $GLOBALS['conn']->query($sql);
		
		//set the flags on the return of the db query
		if ($result2-> num_rows > 0 || $result-> num_rows > 0){
		?>
		<!-- if the email and/or username are already in use, inform the user
			 and send them to the login page -->
			<!DOCTYPE html>
			<html lang = "eng">
			<head>
				<title>Email/Username In Use</title>
				<link rel="stylesheet" type="text/css" href="style2.css">
			</head>
			<body>
				<!-- grab navbar from external file -->
				<?php include "navbar.php" ?>
				
			<br/><br/>
			<h2>The email or username you have signed up with is already in use.</h2>
			<h3>Please try again with a unique username or email address, or sign in.</h3>
			<br><br>

			<div style="text-align: center;" ><a href="signin.php" ><input type="submit" class="button2" value="Sign In" /></a></div>
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
		<?php }
		//if the account does not exist already, create it, and push it to the db
		else{
			$sql = "INSERT INTO Person (fName, lName, email, classification, pass, uname)
					VALUES ( '$f_name', '$l_name', '$email', '$classyear', '$password', '$username')";
			if($GLOBALS['conn']->query($sql) === true){
				 $person_id = $GLOBALS['conn']->insert_id;
			}
			else {
				 echo "Error: " . $sql . "<br>" . $GLOBALS['conn']->error;
			}
			
			//build a list of the mentee skills selected to send to the db
			if(isset($_POST['menteeskills']) || isset($_POST['mentee_user_skill'])){
				$skills = array();
				if(isset($_POST['menteeskills']))
					$skills = $_POST['menteeskills'];
				if(isset($_POST['mentee_user_skill'])){
					$re= "/[ ,]\s*/";
					$u_skills = preg_split($re, $_POST['mentee_user_skill'], 0, PREG_SPLIT_NO_EMPTY);
					insertNewSkills($u_skills);
					for ($i = 0; $i < count($u_skills); $i++) {
						array_push($skills, $u_skills[$i]);
					}
				}
				//external function for sending skills to the appropriate table
				sendSkills($email, "mentee", $skills);
				unset($_POST['menteeskills']);
			}
			
			//mentor version of the same skill builder algorithm
			if(isset($_POST['mentorskills']) || isset($_POST['mentor_user_skill'])){
				$skills = array();
				if(isset($_POST['mentorskills']))
					$skills = $_POST['mentorskills'];
				if(isset($_POST['mentor_user_skill'])){
					$re= "/[ ,]\s*/";
					$u_skills = preg_split($re, $_POST['mentor_user_skill'], 0, PREG_SPLIT_NO_EMPTY);
					insertNewSkills($u_skills);
					for ($i = 0; $i < count($u_skills); $i++) {
						array_push($skills, $u_skills[$i]);
					}
				}
				sendSkills($email, "mentor", $skills);
				unset($_POST['mentorskills']);
			}
			//add the user roles to the table
			if(isset($_POST['role'])) {
				if (is_array($_POST['role'])){
					foreach($_POST['role'] as $role) {
						AddUserRole($person_id, $role);
					}
				} else {
					AddUserRole( $person_id, $_POST['role']);

				}
				//always add pending role
				AddUserRole($person_id, "Pending");
			}
					
				$GLOBALS['conn']->close();
				
		
			?>
			<!-- if everything was successful, inform the user that their account has been created 
				and is pending, send them back to the home page -->
			<!DOCTYPE html>
			<html lang = "eng">
			<head>
				<title>Account Created</title>
				<link rel="stylesheet" type="text/css" href="style2.css">
			</head>
			<body>
				<!-- grab navbar from external file -->
				<?php include "navbar.php" ?>
				
			<br/><br/>
			<h2>Your account has been created, and is awaiting Administrator approval.</h2>
			<h3>An Administrator will inform you when you have been activated and matched.</h3>
			<br><br>

			<div style="text-align: center;" ><a href="about.php" ><input type="submit" class="button1" value="Home" /></a></div>

			<div id = "bottomPage" style="text-align:center;">
				<br>
				<br>
				<br>
				<hr>
				<footer>
					<h6>Copyright &copy; 2017<br>MTSU CS Department</h6>
					<a href="mailto:joshua.phillips@mtsu.edu"><input class ="button2" type="button" value="Contact an Admin"/></a>
					<br>
					<br>
				</footer>
			</div>	
<?php
		}
	}
}
else{
		?>

<!-- if none of the post variables are set, then it is the first visit, show the signup form -->
<!DOCTYPE html>
<html lang = "eng">
    <head>
        <title>Mentoring - Sign Up</title>
		<link rel="stylesheet" type="text/css" href="style2.css">
	</head>
    <body>
        <!-- grab navbar from external file -->
        <?php include "navbar.php" ?>


	        <h2>Sign Up</h2>
            <hr>
	        <form  id="signIn" method="post" action="signup.php" onsubmit="return validateForm()">
		        <table class="signUp" cellspacing="0">

					<tr>
				        <td class="label">Username:</td>
				        <td><input type="text" id="uname" name="username" required="required" nonblur="uName()"/></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="uNameError">*You must enter a username (Pipeline ID)</span></td>
					</tr>

			        <tr>
				        <td class="label">Password:</td>
				        <td><input type="password" name="password" id="password" required="required" onblur="pass()"/></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="passError">*You must enter a valid password (6-10 characters)</span></td>
					</tr>
			        <tr>
				        <td class="label">Confirm Password:</td>
				        <td><input type="password" name="c_password" id="c_password" required="required"  onblur="c_pass()"/></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="c_passError">*Your password is not valid or does not match</span></td>
					</tr>
			        <tr class="split"></tr>
			        <tr class="split"></tr>
			        <tr>
				        <td class="label">First Name:</td>
				        <td><input type="text" name="f_name" id="f_name" onblur="Fname()" required="required" /></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="fName_error">*Invalid First Name</span></td>
			        <tr>
				        <td class="label">Last Name:</td>
				        <td><input type="text" name="l_name" id="l_name" onblur="Lname()" required="required" /></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="lName_error">*Invalid Last Name</span></td>
					</tr>
			        <tr>
				        <td class="label">Email:</td>
				        <td><input type="text" name="email" id="email" onblur="Email()" required="required" /></td>
			        </tr>
					<tr>
						<td />
						<td><span class="err" id="email_error" >*Invalid Email domain@(mtmail.)mtsu.edu</span></td>
					</tr>
			        <tr class="split"></tr>
		        </table>
		        <hr>
                <br>
		        <div class="radios">
			        <label><input type="radio" id="classyear" name="classyear" value="fresh" required="required"/>Freshman</label>
			        <label><input type="radio" id="classyear" name="classyear" value="soph"/>Sophomore</label>
			        <label><input type="radio" id="classyear" name="classyear" value="junior"/>Junior</label>
			        <label><input type="radio" id="classyear" name="classyear" value="senior" />Senior</label>
		        </div>
                <br>
		        <hr>
		        <table class="signUp">
			        <tr class="split"></tr>
			        <tr><th colspan="5">Role</th></tr>
			        <tr>
				        <td colspan="2"><label><input type="checkbox" onchange="MenteeSkills()" id="Mentee" name="role[]" value="mentee" />Mentee</label>
				        </td>
				        <td class="split"></td>
				        <td colspan="2"><label><input type="checkbox" onchange="MentorSkills()" id="Mentor" name="role[]" value="mentor" />Mentor</label>
				        </td>
			        </tr>
		        </table>
                <br>
                <hr>
				<div id="menteeskills"><select id="menteebox" name="menteeskills[]" multiple="multiple">
					<?php
					foreach($subjects as $item){
						echo "<option value=\"$item\">$item</option>";
					  }
					  
					?>
				</select>
				<br/>
				<label style="font-size: 10pt;">Enter your own: <label class="mod"><input type="text" id="mentee_user_skill" name="mentee_user_skill" size="53"/> *comma/space separated</label></label><br/>
				<div style="text-align:center;"><p id="mentee_error" style="display: none; color: red; font-size: 10pt;" >*Please select at least one.</p></div><hr></div>
				
				
				<div id="mentorskills"><select id="mentorbox" name="mentorskills[]" multiple="multiple">
					<?php
					foreach($subjects as $item){
						echo "<option value=\"$item\">$item</option>";
					  }
					  
					?>
				</select>
				<br/>
				<label style="font-size: 10pt;">Enter your own:<label class="mod"><input type="text" id="mentor_user_skill" name="mentor_user_skill" size="53"/> *comma/space separated</label></label>
				<div style="text-align:center;"><p id="mentor_error" style="display: none; color: red; font-size: 10pt;" >*Please select at least one.</p></div><hr></div></div><br/>
				<div style="text-align:center;"><p id="match_error" style="display: none; color: red; font-size: 10pt;" >*Mentor and Mentee skills cannot match.</p></div><hr></div></div>
		        <div id="form_button">
			        <button type="Submit" value="Submit" name="submitbutton" >Sign Up</button>
		        </div>

	        </form>
            <br><br>
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
	<script type="text/javascript" src = "validate.js"></script>
	<!-- jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
	<!-- Bootstrap JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
	<script src="multiselect.js"></script>
	
	
	<!-- instantiate the jquery objects with options (headers and search boxes, double click to shuttle skills) -->
	<script type="text/javascript">
		$('#mentorbox').multiSelect({
			selectableHeader: "<div class='custom-header'>Mentor Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"C++\"'>",
			selectionHeader: "<div class='custom-header'>Selected Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"Python\"'>",
			dblClick: true,
			afterInit: function(ms){
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
			
			afterSelect: function(){
				this.qs1.cache();
				this.qs2.cache();
			},
			afterDeselect: function(){
				this.qs1.cache();
				this.qs2.cache();
			}
		});
		$('#menteebox').multiSelect({
			selectableHeader: "<div class='custom-header'>Mentee Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"C++\"'>",
			selectionHeader: "<div class='custom-header'>Selected Skills</div> <input type='text' class='search-input' autocomplete='off' placeholder='e.g. \"Python\"'>",
			dblClick: true,
			afterInit: function(ms){
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
			
			afterSelect: function(){
				this.qs1.cache();
				this.qs2.cache();
			},
			afterDeselect: function(){
				this.qs1.cache();
				this.qs2.cache();
			}
		});
	
	</script>
    </body>
	
</html>
<?php } ?>