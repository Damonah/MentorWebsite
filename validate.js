
function uName(){
	var dom = document.getElementsByName('username')[0].value;  				//pulls in the username from form
	if (dom == "") {															
		document.getElementById('uNameError').style.display = "block";			//checks to make sure the username is not blank
		document.getElementById('uname').style.background = '#ffcce6';
		return -1;																//returns -1 if field is empty and changes field pinkish
	}	
	else {																		//if username is filled out return 0 and changes color of field 
		document.getElementById('uNameError').style.display = "none";
		document.getElementById('uname').style.background = '#ffffff';
		return 0;
	}
}

function pass() {
	var dom = document.getElementsByName('password')[0].value;					//pulls in password from the form
	if (dom.length > 10 || dom.length < 6 ) {									//checks to make sure the name is not to long or too short(6-10)
		document.getElementById('passError').style.display = "block";
		document.getElementById('password').style.background = '#ffcce6';		//changes feild pinkish if pass doesnt meet standard and returns -1
		return -1;
	}
	else {
		document.getElementById('passError').style.display = "none";			//if password is ok changes field back to to normal and returns 0
		document.getElementById('password').style.background = '#ffffff';
		return 0;
	}
}

function c_pass() {														
	var dom = document.getElementsByName('password')[0].value;				
	var dom1 = document.getElementsByName('c_password')[0].value;				//pulls in both password and confirm password
	if (dom != dom1 || dom1 == "") {											//checks to make sure that the password are the same and not empty
		document.getElementById('c_passError').style.display = "block";			//if they are empty or dont match changes field pinkish and return -1
		document.getElementById('c_password').style.background = '#ffcce6';
		return -1;
	}
	else {																		//if passwords match and are not empty changes field back to normal and returns a 0
		document.getElementById('c_passError').style.display = "none";
		document.getElementById('c_password').style.background = '#ffffff';
		return 0;
	}
}
	


function Fname(){
	var dom = document.getElementsByName('f_name')[0].value;					//pulls in the first name from the form
	var check = dom.search(/^[A-Z][a-zA-Z]+$/);									//checks to makesure the firstname starts with capital

	if(check == -1){ 															//if the first name does not match the regex if changes the field pinkish and returns -1
		document.getElementById("fName_error").style.display = "block"
		document.getElementById('f_name').style.background = '#ffcce6';
		return -1;
	}
	else {																		//if the first name does match the regex if changes the field and returns 0
		document.getElementById('fName_error').style.display = "none";
		document.getElementById('f_name').style.background = '#ffffff';
		return 0;
	}
}



function Email(){
var dom = document.getElementsByName('email')[0].value;							//pulls email from form 
var check = dom.search(/^\w+([\.-]?\w+)*@(mtmail.)?mtsu.edu$/);					//matches the email to make sure it either match username@mtsu.edu or username@mtmail.mtsu.edu

if(check == -1){ 																//if the email fails to match changes field pinkish and return -1
		document.getElementById("email_error").style.display = "block"
		document.getElementById('email').style.background = '#ffcce6';
		return -1;
	}
	else {																		//if the email  matches changes field white and return 0
		document.getElementById('email_error').style.display = "none";			
		document.getElementById('email').style.background = '#ffffff';	
		return 0;
	}
}


function Lname(){																//pulls in the last name from the form
var dom = document.getElementsByName('l_name')[0].value;						
var check = dom.search(/^[A-Z][a-zA-Z]+([\s][A-Z a-z]+)?$/);					//checks to see if the name match capital first letter then lowercase can have two last names

if(check == -1){ 																//if last name is wrong changes the field to a pinkish color and returns -1
		document.getElementById("lName_error").style.display = "block";
		document.getElementById('l_name').style.background = '#ffcce6';
		return -1;
	}
	else {																		//if last name is right changes the field back to a white color and returns 0
		document.getElementById('lName_error').style.display = "none";
		document.getElementById('l_name').style.background = '#ffffff';
		return 0;
	}
}

function validateForm(){
	var valTotal = (uName() + pass() + c_pass() + Fname() + Lname() 
	+ Email() + valMentee() + valMentor() + valSkillMatch()); 					// this checks all of the fields to make sure they return a 0
	if(valTotal < 0) {															//if anything return below a 0 it mean it has fail the check and returns false
		return false;
	}
	return true;																//else it returns true and submits form
}


function MenteeSkills(){
	
	if(document.getElementById("Mentee").checked){
		document.getElementById("menteeskills").style.display = "block";		//check to see if at least one skill was selected 
	}
	else {
		document.getElementById("menteeskills").style.display = "none";
	}
}

function MentorSkills(){
	
	if(document.getElementById("Mentor").checked){
		document.getElementById("mentorskills").style.display = "block";		//check to see if at least one skill was selected 
	}
	else {
		document.getElementById("mentorskills").style.display = "none";
	}
}
	
function valMentee() { 
	if(document.getElementById("Mentee").checked){
		var e_skills = document.getElementById("ms-menteebox").getElementsByClassName("ms-selection")[0].getElementsByClassName("ms-list")[0].getElementsByClassName("ms-selected").length;			//pulls the skills from the list for mentee
		var e_user_skill = document.getElementsByName("mentee_user_skill")[0].value;
		if (e_skills == 0 && e_user_skill == ""){																																					//make sure that field is not empty if empty gives error returns -1
			document.getElementById("mentee_error").style.display = "block";
			return -1;
		}
		else {																																														//if no error returns 0
			document.getElementById("mentee_error").style.display = "none";																						
			return 0;
		}
	}
}

function valMentor(){ 
	if(document.getElementById("Mentor").checked){
		var o_skills = document.getElementById("ms-mentorbox").getElementsByClassName("ms-selection")[0].getElementsByClassName("ms-list")[0].getElementsByClassName("ms-selected").length; 		//pulls the skills from the list for mentor
		var o_user_skill = document.getElementById("mentor_user_skill").value;
		if (o_skills == 0 && o_user_skill == ""){																																					//make sure that field is not empty if empty gives error returns -1
			document.getElementById("mentor_error").style.display = "block";
			return -1;
		}
		else{																																														//if no error returns 0
			document.getElementById("mentor_error").style.display = "none";
			return 0;
		}
	}	
}

//fail if both boxes are checked and if any of the skills match
function valSkillMatch() {
	if(document.getElementById("Mentor").checked && document.getElementById("Mentee").checked){
		var skillslength = document.getElementById("ms-mentorbox").getElementsByClassName("ms-selection")[0].getElementsByClassName("ms-list")[0].childNodes.length;
		var mentorskills = document.getElementById("ms-mentorbox").getElementsByClassName("ms-selection")[0].getElementsByClassName("ms-list")[0].childNodes;
		var menteeskills = document.getElementById("ms-menteebox").getElementsByClassName("ms-selection")[0].getElementsByClassName("ms-list")[0].childNodes;
		
		//get the nodes of the selected skills in each box
		var mentorselected = [], menteeselected = [];
		for (var i = 0; i < skillslength; i++) {
			if (mentorskills[i].className == "ms-elem-selection ms-selected")
				mentorselected.push(i);
			if (menteeskills[i].className == "ms-elem-selection ms-selected")
				menteeselected.push(i);
		}
		console.log("mentor", mentorselected, "mentee", menteeselected);
				
		//loop through each and see if there is a match
		for(var i = 0; i < mentorselected.length; i++){
			for (var j = 0; j < menteeselected.length; j++){
				console.log("o, e", mentorselected[i], menteeselected[j])
				if (mentorselected[i] === menteeselected[j]) {
					console.log("here");
					document.getElementById("match_error").style.display = "block";
					return -1;
				}
			}
		}
		document.getElementById("match_error").style.display = "none";
		return 0;
	}
	else {
		document.getElementById("match_error").style.display = "none";
		return 0;
	}
}
