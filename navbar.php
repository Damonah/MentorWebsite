        <?php
		//Display different content depending on user's roles
		$nav_session_is_admin = false;
		$nav_session_is_mentor = false;
		$nav_session_is_mentee = false;
		$nav_session_is_pending = false;
		
		include_once 'sql.php';
        // determine if user is already logged in		
        if (isset($_SESSION["email"])){
            // user logged in
            $accountstring = "Log Out";
            $nextpage ="logout.php";
            $loggedin = True;
			
			if ($user_rows = GetUser($_SESSION["email"])){
			
				//There should be one result per role
				if ($user_rows->num_rows > 0){
					$nav_session_is_admin = false;
					while($row = $user_rows->fetch_assoc()){
						$user = $row;
						if (strtolower($row["roleName"]) === 'admin'){
							$nav_session_is_admin = true;
						} else if (strtolower($row["roleName"]) === 'mentor'){
							$nav_session_is_mentor = true;
						} else if (strtolower($row["roleName"]) === 'mentee'){
							$nav_session_is_mentee = true;
						} else if (strtolower($row["roleName"]) === 'pending'){
							$nav_session_is_pending = true;
						}
					}

				}
			}
        }
        else{
            // user logged out
            $accountstring = "Log In";
            $nextpage ="signin.php";
            $loggedin = False;
        }

        // set up array of links for navbar construction
        $links = array(
        "About" => "about.php",
        "Skills" => "skills.php",
        "Admin" => "admin.php",
        "Account" => "account.php",
        "Sign Up" => "signup.php",
        "Log In" => "signin.php"
        );

        // place banner
        echo "<div id = \"banner\"><br><h1>MTSU Computer Science<br>    Mentoring Program</h1><br><nav id=\"navigationTable\">";

        // iterate through links and create navbar
        foreach($links as $key => $value) {
            if ($key == "About") {
                echo "<a href = '$value'><input class = \"button1\" type=\"button\" value =\"$key\"/></a>";
            }          
            if ($key == "Skills") {
                echo "<a href = '$value'><input class = \"button1\" type=\"button\" value =\"$key\"/></a>";
            }
            // if user is an admin display admin link
            if ($key == "Admin") {
                if ($nav_session_is_admin){
                    echo "<a href = '$value'><input class = \"button1\" type=\"button\" value =\"$key\"/></a>";                 
                }
            }
            if ($key == "Account") {
                if ($loggedin){
                    echo "<a href = '$value'><input class = \"button1\" type=\"button\" value =\"$key\"/></a>";
                }
            }
            // if user is logged in do not display sign up
            if ($key == "Sign Up") {
                if (!$loggedin) {
                    echo "<a href = '$value'><input class = \"button1\" type=\"button\" value = \"$key\"/></a>";
                }
            }
            // if user is logged-in
            if ($key == "Log In") {
                echo "<a href = 'signin.php?x=logout'><input class = \"button1\" type=\"button\" value =\"$accountstring\" onclick=\"unsetsession()\"/></a>";
            }          
        }
        // end nav bar
        echo "</nav></div>";
		?>
		<script type="text/javascript">
			function unsetsession() {
				document.location="navbar.php";
			}
		</script>
		
