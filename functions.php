<?php

class MyDB extends SQLite3
{
    function __construct()
    {
    	if (file_exists('../Stats/stats.db')) {
        	$this->open('../Stats/stats.db');
    	}
    	else {
    		$this->open('./Stats/stats.db');
    	}
    }
}

function convertStringToQuery($s) {
	return str_replace(' ', '+', $s);
}

function convertNameToURL($s) {
	return strtolower(str_replace(' ', '_', str_replace("'", "", $s)));
}

function globString($s) {
	return strtolower(str_replace(' ', '*', str_replace("'", "", $s)));
}

function table_data($s) {
	return "<td align='center'>" . $s . "</td>";
}

function convert_to_info($k, $s) {
	if ($k == 'jersey_num') {
		if (strlen($s) == 0 || $s === null) { return ''; }
		else { return '<span class="category"> | </span>#' . $s; }
	}
	if ($k == 'experience' && $s == 0) { return 'R'; }
	if ($k == 'salary') { return convert_salary($s); }
	return (strlen($s) > 0 && $s != null) ? $s : 'N/A';
}

function convert_to_filter_link($k, $actual, $s) {
	if ($k == 'jersey_num' || $s == 'N/A') { return $s; }

	$start = '<a href="';
	$end = '">' . $s . '</a>';
	$form = 'form';
	if ($k == 'current_team') {
		$team_link = convertNameToURL($s);
		return $start . 'results.php?q=' . $team_link . $end;
	}

	return "<a id='filter_link' href='javascript:void(0);' " .
		"onclick='submitForm(\"" .
		$k . '__' . $actual . "\");'>" . 
		$s . "</a>";
}

function convert_season_type($type) {
	if ($type === "regular_season") {
		return "RS";
	}
	else {
		return "PL";
	}
}

function convert_salary($sal) {
	$flipped = strrev($sal);
	$sal = '';
	for ($i = 0; $i < strlen($flipped); $i++) {
		$sal = $sal . $flipped[$i];
		if ($i == 2 || $i == 5) {
			$sal = $sal . ',';
		}
	}
	if (substr($sal, -1) == ',') {
		$sal = substr($sal, 0, strlen($sal) - 1);
	}
	return strlen(trim($sal)) > 0 ? '$' . strrev($sal) : "N/A";
}

function convert_salary_back($sal) {
	return str_replace(",", "", substr($sal, 1));
}

function filterDownPlayers($query, $player_info) {
	global $filtered_playerIDs, $filtered_seasonIDs, $filtered;
	$new_players = array();
	$new_seasons = array();

	$result = $query->execute();
	while ($row = $result->fetchArray()) {
		if (in_array($row['player_id'], $filtered_playerIDs)) {
			if ($player_info == false) {
				array_push($new_seasons, $row['season_id']);
			}
		array_push($new_players, $row['player_id']);
		}
	}

	if ($player_info == false) {
		$filtered_seasonIDs = $new_seasons;
	}
	$filtered_playerIDs = $new_players;
	$filtered = true;
}

function fieldIsEmpty($s) {
	return (!(strlen(trim($s)) > 0));
}

function dropIsEmpty($s) {
	return ($s == '_');
}

function adjustInput($n, $stat) {
	$n = floatval($n);
	if (!($stat == 'fgp' || $stat == 'tpp' || $stat == 'ftp' ||
			$stat == 'ftr' || $stat == 'tsp' || $stat == 'efg_pct')) {
		return $n;
	}

	if ($n > 1) { // make sure percentages are in the correct form
		return ($n / 100.0);
	}

	return $n;
}

function determineOperatorSign($op) {
	if ($op == 'at_most') {
		return ' <= ';
	}
	if ($op == 'exactly' || $op == 'only_the' || $op == 'in') {
		return ' = ';
	}

	if ($op == 'before_the' || $op == 'before') {
		return ' < ';
	}
	if ($op == 'after_the' || $op == 'after') {
		return ' > ';
	}

	return ' >= '; // default
}

function getSeasonValue() {
	$spec = $_POST['season_spec'];
	$drop = $_POST['season_drop'];

	if ($spec == 'any') { return ''; }

	$op = determineOperatorSign($drop);
	return ' AND season' . $op . '"' . $spec . '"';
}

function getSeasonTypeValue() {
	// if both checks are checked (or neither), don't filter anything
	if (isset($_POST['rs_check']) == isset($_POST['playoffs_check'])) { return ''; }

	if (isset($_POST['rs_check'])) {
		return ' AND season_type = "regular_season"';
	}
	else {
		return ' AND season_type = "playoffs"';
	}
}

function getTeamValue() {
	$drop = $_POST['season_team_drop'];

	if ($drop == '_') { return ''; }

	return ' AND team = "' . getTeamMap()[$drop] . '"';
}

function getTeamMap() {
	return array("Atlanta Hawks"=>"ATL",
                        "Boston Celtics"=>"BOS",
                        "Brooklyn Nets"=>"BKN",
                        "Charlotte Hornets"=>"CHA",
                        "Chicago Bulls"=>"CHI",
                        "Cleveland Cavaliers"=>"CLE",
                        "Dallas Mavericks"=>"DAL",
                        "Denver Nuggets"=>"DEN",
                        "Detroit Pistons"=>"DET",
                        "Golden State Warriors"=>"GS",
                        "Houston Rockets"=>"HOU",
                        "Indiana Pacers"=>"IND",
                        "Los Angeles Clippers"=>"LAC",
                        "Los Angeles Lakers"=>"LAL",
                        "Memphis Grizzlies"=>"MEM",
                        "Miami Heat"=>"MIA",
                        "Milwaukee Bucks"=>"MIL",
                        "Minnesota Timberwolves"=>"MIN",
                        "New Orleans Pelicans"=>"NO",
                        "New York Knicks"=>"NY",
                        "Oklahoma City Thunder"=>"OKC",
                        "Orlando Magic"=>"ORL",
                        "Philadelphia 76ers"=>"PHI",
                        "Phoenix Suns"=>"PHX",
                        "Portland Trail Blazers"=>"POR",
                        "Sacramento Kings"=>"SAC",
                        "San Antonio Spurs"=>"SA",
                        "Toronto Raptors"=>"TOR",
                        "Utah Jazz"=>"UTAH",
                        "Washington Wizards"=>"WSH");
}

function getTeamMapFlipped() {
	return array_flip(getTeamMap());
}

function displayArrayAsSelectOptions($arr) {
	foreach ($arr as $k=>$v) {
		echo '<option value="' . $k .
			'">' . $v . '</option>';
	}
}

?>