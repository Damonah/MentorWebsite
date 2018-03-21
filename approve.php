<?php
include 'sql.php';
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>

<?php
# this function returns an array of all the pending users
function GetPendingUsers(){
	$results=array();
	$pending_users_query = "select Person.uname from Person, PersonRoles where Person.id = PersonRoles.person_id and PersonRoles.role_id = 4";
	$pending_user_results = $GLOBALS['conn']->query($pending_users_query);
	while($pending_user_array[]=mysqli_fetch_array($pending_user_results));
	if(is_array($pending_user_array)){
		foreach($pending_user_array as $user){
		if(isset($pending_user_array[0])){
			$temp_username=$user['uname'];
			array_push($results, $temp_username);
	   }
		}
	}
	$results = array_filter($results);
	return $results;
}



$pending_users = GetPendingUsers();

echo "<br/>";
echo "<div class=\"logform\"><form action=\"changeroles.php\" method=\"post\">";
echo "<table id=\"logtable\">";
echo "<tr><th>Username</th><th>Status</th></tr>";

foreach ($pending_users as $user) {
	echo "<tr><td>$user</td><td><input type=\"button\" class=\"button1\" value=\"Approve\" action=\"\"/></td></tr>";
}
echo "</table>";


?>