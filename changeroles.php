<?php
include 'sql.php';

#hardcoded here for testing
$user_dropping="msp4k";
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
<?php

//get the line numbers from the log table that are checked
$deleteLines = array();
if(isset($_POST['checked'])) {
	foreach($_POST['checked'] as $lineNum) {
		array_push($deleteLines, $lineNum);
	}	
}

deleteLog($deleteLines);

/* //for insertion:
$logstring = logInsert($user_dropping);

# show results here for now
if ($logstring){
	echo ("inserted successfully:");
	echo "<br>";
	echo "$logstring <br/>";
} */


//for printing to admin accounts page:
$lines = printLogs();

echo "<br/>";
echo "<div class=\"logform\"><form action=\"changeroles.php\" method=\"post\">";
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
echo "</table><input type=\"submit\" class=\"button1\" value=\"Clear Selected\"/></div>";

//actually remove the role and the relationships from the table
//removeRoleRel();

?>