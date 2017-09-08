<!DOCTYPE html>
<html>
<head>
	<title>Filter</title>
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="/Hoops/Loading/style.css">
	<link rel="stylesheet" href="../data-tip/dist/data-tip.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro|Rubik+Mono+One|Roboto+Condensed" rel="stylesheet">
	<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="../slick/slick/slick.min.js"></script>
	<script type="text/javascript" src="../tablesorter/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="/Hoops/Loading/loading.js"></script>
</head>

<script type="text/javascript">
$(document).ready(function(){
	$("body").css("color", "white");
	
	graph_filter = <?php
		if (isset($graph_filter)) { echo json_encode("true"); }
		else { echo json_encode("false"); }?>;

	if (graph_filter != "true") {
		$("#filter_results").tablesorter();

		var click = sessionStorage.getItem("tableHeaderClick");
		var headers, inner;
		if (["weight", "salary", "draft_pos", "draft_year"].includes(click)) {
			headers = document.getElementsByTagName("th");
			for (var i = 0; i < headers.length; i++) {
				inner = headers[i].innerHTML;
				if (click === "weight" && inner === "WT" ||
					click === "salary" && inner === "SAL" ||
					click === "draft_pos" && inner === "DY" ||
					click === "draft_year" && inner === "DP") {
						$("th:eq(" + i.toString() + ")").click();
						if (click === "draft_pos") { $("th:eq(" + i.toString() + ")").click(); } // repeat this click to sort descending
						break;
				}
			}
			sessionStorage.removeItem("tableHeaderClick");
		}
		
		if (sessionStorage.getItem('rand') != null && sessionStorage.getItem('bgs') != null) {
			var rand = sessionStorage.getItem('rand');
			var bgs = sessionStorage.getItem('bgs').split(',');
			if (document.getElementsByTagName("table")[0] != undefined) {
				document.getElementsByTagName("table")[0].style.color = sessionStorage.getItem('color');
			}
		}
		else {
			function getRandomInt(min, max) {
				min = Math.ceil(min);
				max = Math.floor(max);
				return Math.floor(Math.random() * (max - min)) + min;
			}
			var bgs = ['giannis', 'brow', 'lebron', 'russell', 'harden', 'kat', 'kawhi',
						'dame', 'steph', 'wall', 'dirk'];
			var colors = ['#00471B', '#01295D', '#6F263D', '#0A7EC2', '#CE1141',
				'#005084', '#000000', '#E03A3E', '#006BB6', '#E31837', '#0942B3'];
			var rand = getRandomInt(0, bgs.length);
			if (document.getElementsByTagName("table")[0] != undefined) {
				document.getElementsByTagName("table")[0].style.color = colors[rand];
			}
		}

		r_back = document.getElementById('results_body');
		r_back.style.backgroundImage = "url('../Images/Players/Backgrounds/" + bgs[rand] + "_bw.jpg')";
		

		if (document.getElementById("filter_results") != null) {
			var width = 15 * document.getElementById("filter_results").rows[0].cells.length;
			if (width <= 90) {
				document.getElementById('filter_results').style.width = width.toString() + 'vw';
				document.getElementById('filter_results').style.marginLeft = (-0.5 * width).toString() + 'vw';
			}
			else {
				document.getElementById('filter_results').style.width = '90vw';
				document.getElementById('filter_results').style.marginLeft = '-45vw';
			}
		}
	}
});
</script>


<body id="results_body">

<?php
	if (isset($graph_filter) == false) {
		include '../functions.php';
	}
	
    // set up initial variables
    $db = new MyDB();
    $filtered_playerIDs = array(); // stores every player ID who passes player info filters
    $filtered_seasonIDs = array(); // stores every season ID that passes season stat filters
    $player_info_keys = array(); // stores the keys that have been used to filter personal info
    $season_stat_keys = array(); // stores the keys that have been used to filter season stats
    $final_filtered = array();
    $filtered = false;
    $filtered_by_stat = false;

    // define all the player info that will have possible filters
    $group1_info = array('weight', 'age', 'salary', 'draft_pos'); // player info

   	$group2_info = array('current_team', 'college'); // player info

   	$group3_info = array('height', 'experience', 'draft_year'); // player info

	$group4_info = array('position');


    $query = $db->prepare('SELECT player_id FROM PLAYER'); // add every player to start
	$result = $query->execute();
	while ($row = $result->fetchArray()) {
		array_push($filtered_playerIDs, $row['player_id']);
	}

	// Filter by the name box
	if (isset($_POST['name_spec']) && strlen(trim($_POST['name_spec'])) > 0) {
		$name_q = '%' . str_replace(' ', '%', trim($_POST['name_spec'])) . '%';
	    $query = $db->prepare('SELECT * FROM PLAYER WHERE LOWER(name) like :name');
	    $query->bindValue(':name', strtolower($name_q), SQLITE3_TEXT);
		filterDownPlayers($query, true);
	}

	// Filter by the position checkboxes
	$psts = array("PG", "SG", "SF", "PF", "C");
	$count_checked = 0;
	$q_str = "SELECT player_id FROM PLAYER WHERE ";
	foreach ($psts as $p) {
		if (isset($_POST[$p . '_check'])) {
			$count_checked = $count_checked + 1;
			$q_str = $q_str . "position = '" . $p . "' OR ";
		}
	}
	$q_str = substr($q_str, 0, strlen($q_str) - 3); // chop off the trailing OR

	// only filter if something specific was selected
	if ($count_checked > 0 && $count_checked < 5) {
		$query = $db->prepare($q_str);
		filterDownPlayers($query, true);
	}
	if ($count_checked > 0) {
		array_push($player_info_keys, 'position');
	}

	// Filter by the group 1 boxes
	foreach ($group1_info as $info) {
		if (isset($_POST[$info . '_drop']) == false ||
			isset($_POST[$info . '_spec']) == false) {
			continue;
		}

		$drop_input = $_POST[$info . '_drop'];
		$spec_input = str_replace(',', '', $_POST[$info. '_spec']);

		if (fieldIsEmpty($spec_input)) { continue; } // skip if the user entered nothing
		if (is_numeric($spec_input) == false) { continue; } // skip if input was not numeric

		$op = determineOperatorSign($drop_input);
		$spec_input = adjustInput($spec_input, $info);

		$q_str = "SELECT player_id FROM PLAYER WHERE " . $info . $op . $spec_input;
		$query = $db->prepare($q_str);
		filterDownPlayers($query, true);
		array_push($player_info_keys, $info);
	}

	// Filter by the group 2 boxes
	foreach ($group2_info as $info) {
		if (isset($_POST[$info . '_drop']) == false) {
			continue;
		}

		$drop_input = $_POST[$info . '_drop'];

		if (dropIsEmpty($drop_input)) { continue; } // skip if the user chose nothing

		$q_str = "SELECT player_id FROM PLAYER WHERE " . $info . ' = "' . $drop_input . '"';
		$query = $db->prepare($q_str);
		filterDownPlayers($query, true);
		array_push($player_info_keys, $info);
	}

	// Filter by the group 3 boxes
	foreach ($group3_info as $info) {
		if (isset($_POST[$info . '_drop']) == false ||
			isset($_POST[$info . '_spec']) == false) {
			continue;
		}

		$drop_input = $_POST[$info . '_drop'];
		$spec_input = $_POST[$info . '_spec'];

		if (dropIsEmpty($spec_input)) { continue; } // skip if the user chose nothing
		$op = determineOperatorSign($drop_input);

		$q_str = "SELECT player_id FROM PLAYER WHERE " .
					$info . $op . '"' . $spec_input . '"';
		$query = $db->prepare($q_str);
		filterDownPlayers($query, true);
		array_push($player_info_keys, $info);
	}

	// Filter by the stat options
	$q_str = "SELECT DISTINCT p.player_id, s.season_id
		FROM PLAYER as p INNER JOIN SEASON_STATS as s ON 
		p.player_id = s.player_id AND ";

	for ($i = 1; $i <= 8; $i++) {
		if (isset($_POST["operator_" . strval($i)]) == false ||
			isset($_POST["spec_" . strval($i)]) == false ||
			isset($_POST["stat_drop_" . strval($i)]) == false) {
			continue;
		}

		$current_op = determineOperatorSign($_POST["operator_" . strval($i)]);
		$current_spec = $_POST["spec_" . strval($i)];
		$stat_drop = $_POST["stat_drop_" . strval($i)];

		// skip if nothing was entered
		if (fieldIsEmpty($current_spec) || dropIsEmpty($stat_drop)) { continue; }
		// skip if input was not numeric
		if (is_numeric($current_spec) == false) { continue; }

		$current_spec = adjustInput($current_spec, $stat_drop);

		$q_str = $q_str . $stat_drop . $current_op . $current_spec . ' AND ';
		$filtered_by_stat = true;
		array_push($season_stat_keys, $stat_drop);
	}	

	if ($filtered_by_stat) {
		$season = getSeasonValue();
		$season_type = getSeasonTypeValue();

		$team = getTeamValue();

		$q_str = substr($q_str, 0, strlen($q_str) - 4) .
			$season . $season_type . $team;
		$query = $db->prepare($q_str);
		filterDownPlayers($query, false);
	}

	// Always filter by a season if one was specified and nothing else was
	if (isset($_POST['season_spec'])) {
		if ($filtered == false && $_POST['season_spec'] != 'any') {
			$season = getSeasonValue();
			$season_type = getSeasonTypeValue();
			$q_str = "SELECT DISTINCT p.player_id, s.season_id
						FROM PLAYER as p INNER JOIN SEASON_STATS as s ON 
						p.player_id = s.player_id " . $season . $season_type;
					$query = $db->prepare($q_str);
			filterDownPlayers($query, false);
			$filtered_by_stat = true;
		}
	}

	if ($filtered_by_stat) {
		array_push($season_stat_keys, "season");
		array_push($season_stat_keys, "season_type");
	}

	if (isset($graph_filter) == false) {
		include 'merge_arrays.php';
	}
?>

</body>

<div id="loading">
  <img id="loading-image" src="/Hoops/Loading/loader.gif" alt="Loading..." />
</div>

</html>