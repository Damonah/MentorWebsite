<?php
	session_start();

	$use_ldap = false;	//Only used to access public profile info; disabled temporarily
	$user_is_admin = false;
	$user = false;
	$user_info = false;
	if(isset($_SESSION['email']))
		$email = $_SESSION['email'];

	//Query sql for user
	include 'sql.php';
?>


<!DOCTYPE html>
<html lang = "eng">
    <head>
        <title>Welcome to MTSU CS Mentoring</title>
        <link rel = "stylesheet" type = "text/css" href = "style2.css"/>
    </head>
    <body>

        <!-- grab navbar from external file -->
        <?php include "navbar.php" ?>

        <h2>FAQs</h2>
        <hr>
        <br>
        <div class = "column-left"><pre> </pre></div>
        <div class = "column-center">
            <ul>
                <li><a href="#one">How does this work exactly?</li></a>
                <li><a href="#two">Will it conflict with my class schedule?</li></a>
                <li><a href="#three">What are the advantages?</li></a><br>
				<li><a href="#ten">Who can I contact for more help?</li></a>
                <li><a href="#four">How much does mentoring cost?</li></a>
                <li><a href="#five">Is this just for a certain class I may be taking?</li></a>
                <li><a href="#seven">Is mentoring a paid position?</li></a>
                <li><a href="#eight">Do I need to have taken certain classes in order to mentor?</li></a>
            </ul>
        </div>
        <div class = "column-right"><pre> </pre></div>
        <hr>
        <div class = "column-left"><pre> </pre></div>
        <div class = "column-center">
            <ul>
                <strong>How does this work exactly?</strong>
                <li id="one">When you sign-up, you will have to opportunity to choose whether
                    you would like to recieve mentoring, or be a mentor. Other information you
                    will be asked to proved includes your grade year, tiem availability, and most
                    importantly, the skills you would like to learn or share. Afterwards, an
                    administrator will be able to find you a match and connect you!
                </li>
                <br>
                <strong>Will it conflict with my class schedule?</strong>
                <li id="two">Not at all! We take your schedule into account when looking for mentoring
                    matches. You can also enable/disable your account from semester to semester. You're
                    in charge of your time, use it how you want!
                </li>
                <br>
                <strong>What are the advantages?</strong>
                <li id="three">There are so many! The mentoring program provides a wonderful networking
                    opportunity, for all students. Students can learn new skills outside of traditional
                    course work. Those struggling with coursework can get additional help.
                    <br>Students who work as mentors can list the works they have done on their resume as
                    an example of leadership in action. The list goes on!
                </li>
				<br>
				<strong>Who can I contact for more help?</strong>
				<li id="ten">If you have more questions or would like to contact and administrator about
					your mentor or mentee, <a href="mailto:joshua.phillips@mtsu.edu">click here!</a>
				</li>
                <br>
                <hr>
                <br>
                <strong>How much does mentoring cost?</strong>
                <li id="four">Mentoring is provided for free!
                </li>
                <br>
                <strong>Is this just for a certain class I may be taking?</strong>
                <li id="five">It can be! If there is a course you need a little or a lot
                     of help in, we can match you with a mentor that can help. If you want to
                     learn something outside of your coursework, you can do that too!
                </li>
                <br>
                <hr>
                <br>
                <strong>Is mentoring a paid position?</strong>
                <li id="seven">Mentoring is not a paid position, but it has many benefits outside
                    of financial compensation. <a href="#three">See above!</a>
                </li>
                <br>
                <strong>Do I need to have taken certain classes in order to mentor?</strong>
                <li id="eight">Nope! Any classes you have taken that you would like to mentor in are
                    great, but you can also include anything you have learned outside of school that
                    you might like to share!
                </li>
                <br>
            </ul>
        </div>
        <div class = "column-right"><pre> </pre></div>

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
