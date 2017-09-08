<!DOCTYPE html>
<html>

<head>
	<title>Search</title>
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
	<link rel="stylesheet" href="../data-tip/dist/data-tip.css">
	<link rel="stylesheet" type="text/css" href="../slick/slick/slick.css"/>
	<link rel="stylesheet" type="text/css" href="../slick/slick/slick-theme.css"/>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="/Hoops/Loading/style.css">
	<link href="https://fonts.googleapis.com/css?family=Krona+One|Passion+One|Karla|Source+Sans+Pro|Rubik+Mono+One|Roboto+Condensed|PT+Sans" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/jquery.autocomplete.min.js"></script>
	<script type="text/javascript" src="../js/names-autocomplete.js"></script>
	<script type="text/javascript" src="../slick/slick/slick.min.js"></script>
	<script type="text/javascript" src="../tablesorter/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="../js/team_colors.js"></script>
	<script type="text/javascript" src="../js/team-coloring.js"></script>
	<script type="text/javascript" src="/Hoops/Loading/loading.js"></script>

	<script type="text/javascript">
	$(document).ready(function(){
		$('.team_roster').slick({
			slidesToShow: 3,
			arrows: true,
			dots: true,
			infinite: false
		});

		$("#rs_regular").tablesorter(); 
		$("#rs_advanced").tablesorter(); 
		$("#playoffs_regular").tablesorter(); 
		$("#playoffs_advanced").tablesorter();
	});

	function toggleTable(tableIndex) {
		buttonIDs = ['RSRegButton', 'RSAdvButton', 'PLRegButton', 'PSAdvButton'];
		tableIDs = ['rs_regular', 'rs_advanced', 'playoffs_regular', 'playoffs_advanced'];
		descIDs = ['rs_regular_d', 'rs_advanced_d', 'playoffs_regular_d', 'playoffs_advanced_d'];

		if (tableIndex <= 1) {
			if (document.getElementById("playoff_no_table") != null) {
				document.getElementById("playoff_no_table").style.display = 'none';
			}

			if (document.getElementById("rs_no_table") != null) {
				document.getElementById("rs_no_table").style.display = 'block';
			}
		}
		else {
			if (document.getElementById("playoff_no_table") != null) {
				document.getElementById("playoff_no_table").style.display = 'block';
			}
			
			if (document.getElementById("rs_no_table") != null) {
				document.getElementById("rs_no_table").style.display = 'none';
			}
		}

		for (var i in buttonIDs) {
			table = document.getElementById(tableIDs[i]);
			desc = document.getElementById(descIDs[i]);

			if (table === null || desc === null) {
				continue;
			}
			if (i == tableIndex) {
				table.style.display = 'table';
				desc.style.display = 'block';
			}
			else {
				table.style.display = 'none';
				desc.style.display = 'none';
			}
		}
	}
</script>
</head>

<body>
<?php
	include '../functions.php';
	include 'get_names.php';
	include 'option_form.php';

	$db = new MyDB();
	$name = '';
	$gotMatch = false;

	// Convert the searched name to a query string
	if (isset($_GET['q']) && strlen(trim($_GET['q'])) > 0) {
		$s = trim($_GET['q']);

		// Check if the query is for a team first
		$stmt = $db->prepare('SELECT * FROM TEAM
			WHERE LOWER(team_name) LIKE :name');
		$stmt->bindValue(':name', $s, SQLITE3_TEXT);
	  	$result = $stmt->execute();

	  	if ($result->fetchArray()) {
	  		$result = $stmt->execute();
		  	$logo_path = "../Images/Teams/Logos/no_name.png";
		  	$arena_path = '';
		  	include 'results_team.php';
		}
	  	else { // If the query isn't for a team, search players
			$stmt = $db->prepare('SELECT name, player_id, current_team, jersey_num, draft_pos, draft_year, position,
			  age, experience, height, weight, college, salary FROM PLAYER WHERE name = :name');
			$stmt->bindValue(':name', $s, SQLITE3_TEXT);
		  	$result = $stmt->execute();

		  	// If a player is found, show his information and then query for his '16-'17 stats
		  	if ($result->fetchArray()) { 
		  		$result = $stmt->execute();
		  		include 'results_player.php';
			}
			else {
				echo "<p id='no_result_found'>Sorry, there are no teams or players under the name '" . $_GET['q'] .  "'.</p>";
			}
		}
	}
	else {
		echo "<p id='no_result_found'>Sorry, looks like you entered an empty query. Please try again.</p>";
	}
?>

</body>

<div id="loading">
  <img id="loading-image" src="/Hoops/Loading/loader.gif" alt="Loading..." />
</div>

</html>