<?php
	include 'sql.php';
	session_start();
	//if redirected with logout, kill the session
	if(isset($_REQUEST['x']))
		session_unset();

	//show the form for signing in if no post variables are set (first visit)
	if(!isset($_POST['password'])) {
?>
<html lang = "eng">
	<head>
		<title>Mentoring - Sign In
		</title>
		<link rel="stylesheet" type="text/css" href="style2.css">
	</head>
	<body>
		<!-- grab navbar from external file -->
		<?php include "navbar.php" ?>

		<div id="SignIn">
			<h2>Sign In</h2>
			<hr>
			<form action="signin.php" id="signIn" onsubmit="return validateForm()" method="post">
				<table class="signIn">
					<tr>
						<td class="label">Username:</td>
						<td><input type="text" name="username" id="uname" required="required" onblur="uName()"/></td>
					</tr>
					<tr>
						<td />
						<td><span class="err" id="uNameError">*Invalid Username (Pipeline ID)</span></td>
					</tr>
					<tr>
						<td class="label">Password:</td>
						<td><input type="password" name="password" id="password" required="required" onblur="pass()"/></td>
					</tr>
					<tr>
						<td />
						<td><span class="err" id="passError">*Invalid Password (6-10 Characters)</span></td>
					</tr>
				</table>
				<div id="form_button">
					<button type="Submit" value="Submit" name="submitbutton">Sign In</button>
				</div>
			</form>
		</div>
		<br><br><br>
		<hr>
		<div id = "bottom_page">
			<footer>
				<br>
				<br>
				   <h6>Copyright &copy; 2017<br>MTSU CS Department</h6>
				   <a href="mailto:joshua.phillips@mtsu.edu"><input class ="button1" type="button" value="Contact an Admin"/></a>
                   <br>
                   <br>
			</footer>
		</div>
	</body>
	<script type="text/javascript" src = "validate.js"></script>
</html>
<?php
}
//otherwise, do server side validation
else {

	//if the username and password are set, grab the post variables
	if(isset($_POST['username'])){
			$username= $_POST['username'];
		}
	if (isset($_POST['password'])){
		$password = md5($_POST['password']);
	}
      //$password=md5($password);
	//grab the uname and password from the database if they exist
	$sql = "SELECT uname, pass FROM `person` WHERE uname = '$username' AND pass = '$password' ";
	$result = $conn->query($sql);
	if($result-> num_rows > 0 ){
		while($row = $result ->fetch_assoc()){
			$databaseUsername= $row['uname'];
			$databasePassword= $row['pass'];
		}
		//if the uname and password match, then set some session variables for email and role
		if( $databaseUsername === $username and  $databasePassword === $password ){
			$sql = "SELECT  email, id FROM `person` WHERE uname = \"$username\" and pass = \"$password\"";
			$result = $conn->query($sql);
			if($result-> num_rows > 0 ){
				while($row = $result ->fetch_assoc()){
				   $databaseEmail = $row['email'];
				   $databaseid = $row['id'];
				}
			}
			$_SESSION['email'] = $databaseEmail;
			$role = getRoles($databaseid);
			//if the user is a pending status, show a page informing them that they cannot go further,
			//send them to the home page
			if ($role == "Pending") {
				session_unset();
				?>
				 <!DOCTYPE html>
					<html lang = "eng">
					<head>
						<title>Pending Account</title>
						<link rel="stylesheet" type="text/css" href="style2.css">
					</head>
					<body>
						<!-- grab navbar from external file -->
						<?php include "navbar.php" ?>

					<br/><br/>
					<h2>Your Account is in pending status.</h2>
					<h3>An administrator will notify you when your account is active.</h3>
					<br><br>

					<div style="text-align: center;" ><a href="about.php" ><input type="submit" class="button1" value="Home" /></a></div>
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
					</div>  <?php
				}
				//if the user is a dual role, send them to accounts page with amended role
				elseif ($role == "Mentor, Mentee") {
					$_SESSION['role'] == "Both";
					header("Location: account.php");
				}
				//if the user is a single role, send them to accounts page
				else {
					$_SESSION['role'] = $role;
					header("Location: account.php");
				}
		}
	}
	//if the credentials do not match, show a page
	else {
		unset($_SESSION['email'], $_SESSION['role']);
	?>
		<html>
			<head>
				<title>Bad Credentials</title>
				<link rel="stylesheet" type="text/css" href="style2.css">
			</head>
			<body>
				<!-- grab navbar from external file -->
				<?php include "navbar.php" ?>
				<br />
				<br />

				<h2>Sorry, those credentials do not match.</h2>
				<div style="text-align: center;"><input type="button" class="button2" id="button1" value="Try Again" onclick="window.location.href='signin.php'"/></div>
			</body>
		</html>

<?php 	}   } ?>
