<!DOCTYPE html>
<html>
<head>
	<title>Filter</title>
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="/Hoops/Loading/style.css">
	<link rel="stylesheet" type="text/css" href="../Search/style.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro|Rubik+Mono+One|Roboto+Condensed" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="../slick/slick/slick.min.js"></script>
	<script type="text/javascript" src="../js/jquery.autocomplete.min.js"></script>
  	<script type="text/javascript" src="../js/names-autocomplete.js"></script>
  	<script type="text/javascript" src="/Hoops/Loading/loading.js"></script>
</head>


<script type="text/javascript">
	var stat_drop_index = 5;

	$(document).ready(function(){
		var bgs = ['giannis', 'brow', 'lebron', 'russell', 'harden', 'kat', 'kawhi',
					'dame', 'steph', 'wall', 'dirk'];
		var colors = ['#00471B', '#01295D', '#6F263D', '#0A7EC2', '#CE1141',
			'#005084', '#000000', '#E03A3E', '#006BB6', '#E31837', '#0942B3'];
		var highlights = ['#E0D6BF', '#E81333', '#FFB81C', '#0A7EC2', '#CE1141',
			'#7AC142', '#DAA520', '#E03A3E', '#006BB6', '#E31837', '#0A7EC2'];
		var rand = getRandomInt(0, bgs.length);
		f_back = document.getElementById('filter_body');
		input = document.getElementsByClassName('filter_input_div')[0];
		f_back.style.backgroundImage = "url('../Images/Players/Backgrounds/" + bgs[rand] + "_bw.jpg')";
		input.style.border = "solid " + colors[rand];

		// Kawhi, Giannis, and Davis are different
		if (rand === 0) {
			$('.header_link_text').css("color", "#E0D6BF");
		}
		else if (rand === 1) {
			$('.header_link_text').css("color", "#E81333");
		}
		else if (rand == 6) {
			$('.header_link_text').css("color", "#81929E");
		}
		else {
			$('.header_link_text').css("color", colors[rand]);
		}

		var css = '#header a { text-decoration: none; }\
					#header a:hover, .header_link_text:hover { color: white !important; }';
		var css2 = '#filter_button:active { background: ' + colors[rand] + '; color:white; }';
		var css3 = '#filter_input_div input[type="text"] { height: ' + ($('select').height() * 0.9) + 'px; vertical-align: top; }';
		var css4 = '#season_stats_filter input[type="text"] { margin-top: 15px;} ';
		var style = document.createElement('style');

		if (style.styleSheet) {
		    style.styleSheet.cssText = css;
		} else {
		    style.appendChild(document.createTextNode(css));
		}
		style.appendChild(document.createTextNode(css2));
		style.appendChild(document.createTextNode(css3));
		style.appendChild(document.createTextNode(css4));
		document.getElementsByTagName('head')[0].appendChild(style);

		sessionStorage.setItem('bgs', bgs);
		sessionStorage.setItem('rand', rand);
		sessionStorage.setItem('color', colors[rand]);

		$("<style>").prop("type", "text/css").html("\
		    .autocomplete-suggestions strong {color: " + highlights[rand] + "; }").appendTo("head");

		$('.filter_input_div').css("color", "white");
	});

	function getRandomInt(min, max) {
		min = Math.ceil(min);
		max = Math.floor(max);
		return Math.floor(Math.random() * (max - min)) + min;
	}

	function addNewInput() {
		var lineHeights = ["6.5vh", "5vh", "4.5vh", "4vh"];
		var marginBottoms = ["-5vh", "-4vh", "-3vh", "-2vh"];
		var marginTops = ["-5px", "7px", "4px", "-2px"];
		var css = "";
		document.getElementById("season_stat_drop_" + stat_drop_index.toString()).style.display = "block";
		document.getElementById("season_stat_drops").style.lineHeight = lineHeights[stat_drop_index - 5];
		document.getElementById("season_stat_drops").style.marginBottom = marginBottoms[stat_drop_index - 5];

		css = '#season_stats_filter input[type="text"] { margin-top: ' + marginTops[stat_drop_index - 5] + ';} ';
		var style = document.createElement('style');

		if (style.styleSheet) {
		    style.styleSheet.cssText = css;
		} else {
		    style.appendChild(document.createTextNode(css));
		}

		stat_drop_index += 1;
		if (stat_drop_index == 9) {
			document.getElementById("season_stat_plus").style.display = "none";
		}
	}

</script>

<?php
	include '../functions.php';
	include 'get_preset_player_info.php';
	include '../Search/get_names.php';
	include '../Search/option_form.php';
?>

<body id="filter_body">
	<div id="filter_input_div" class="filter_input_div">
		<h1 id="find_every_player">Find every current player:</h1>

		<form method="post" id="filter_input" action="./filter.php">

		<div id="player_info_filter">
		whose name contains
			<input type="text" name="name_spec">
			<br/><br/>
		who plays for the
			<select name="current_team_drop">
				<option value="_"></option>
				<?php
					foreach ($teams as $t) {
						echo '<option value="' . $t .
						'">' . $t . '</option>';
					}
				?>
			</select>
			<br/><br/>
		whose height is
			<select name="height_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			<select name="height_spec">
				<option value="_"></option>
				<?php
					foreach ($heights as $h) {
						echo '<option value="' . str_replace(' ', '_', $h) .
						'">' . $h . '</option>';
					}
				?>
			</select>
			<br/><br/>
		who weighs
			<select name="weight_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			<input type="text" name="weight_spec">
			lbs. <br/><br/>
		who went to college at
			<select name="college_drop">
				<option value="_"></option>
				<?php
					foreach ($colleges as $c) {
						if (strlen($c) > 0) {
							echo '<option value="' . $c .
							'">' . $c . '</option>';
						}
					}
				?>
			</select>
			<br/><br/>
		who was drafted
			<select name="draft_pos_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			#<input type="text" name="draft_pos_spec"> overall
			<br/><br/>
		who was drafted
			<select name="draft_year_drop">
			  <option value="in">in</option>
			  <option value="before">before</option>
			  <option value="after">after</option>
			</select>
			<select name="draft_year_spec">
				<option value="_"></option>
				<?php
					foreach ($draft_years as $d) {
						if (strlen($d) > 0) {
							echo '<option value="' . $d .
							'">' . $d . '</option>';
						}
					}
				?>
			</select>
			<br/><br/>
		who's played for
			<select name="experience_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			<select name="experience_spec">
				<option value="_"></option>
				<?php
					foreach ($experiences as $e) {
						echo '<option value="' . $e .
						'">' . $e . '</option>';
					}
				?>
			</select>
			years <br/><br/>
		whose age is
			<select name="age_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			<input type="text" name="age_spec">
			years old <br/><br/>
		whose '17-'18 salary is
			<select name="salary_drop">
			  <option value="at_least">at least</option>
			  <option value="at_most">at most</option>
			  <option value="exactly">exactly</option>
			</select>
			$<input type="text" name="salary_spec">
			<br/><br/>
		who plays
			<input type="checkbox" name="PG_check" value="PG_check" checked>PG or
			<input type="checkbox" name="SG_check" value="SG_check" checked>SG or
			<input type="checkbox" name="SF_check" value="SF_check" checked>SF or
			<input type="checkbox" name="PF_check" value="PF_check" checked>PF or
			<input type="checkbox" name="C_check" value="C_check" checked>C
			<br/><br/>
		</div>

		<div id="season_stats_filter">
		<br>

		<div id="unique_break">
			<span id="season_header">who has any seasons matching the following criteria:</span>
			<img id="season_stat_plus" src="../Images/plus_button.png" alt="Plus Button" onclick="addNewInput()"/>
			<br>
		</div>
		
		<div id="season_stat_drops">
			<?php
				for ($i = 1; $i <= 8; $i++) {
					$n = strval($i);
					echo '<div id="season_stat_drop_' . $n . '"><select name="operator_' . $n . '">
						<option value="at_least">at least</option>
						<option value="at_most">at most</option>
						<option value="exactly">exactly</option>
					</select>
					<input type="text" name="spec_' . $n . '">
					<select name="stat_drop_' . $n . '">';
					foreach ($stats as $k=>$v) {
						echo '<option value="' . $k .
							'">' . $v . '</option>';
					}
					echo "</select><br><br></div>";
				}
			?>
		</div>

		Limit his team in the season to the
			<select name="season_team_drop">
				<option value="_"></option>
				<?php
					foreach ($teams as $t) {
						echo '<option value="' . $t .
						'">' . $t . '</option>';
					}
				?>
			</select>
			<br/><br/>

		Limit the filter to averages from
		<select name="season_drop">
		  <option value="only_the">only the</option>
		  <option value="after_the">after the</option>
		  <option value="before_the">before the</option>
		</select>
		<select name="season_spec">
			<option value="any">any</option>
			<?php
				foreach ($seasons as $s) {
					echo '<option value="' . $s .
					'">' . $s . '</option>';
				}
			?>
		</select>
		season <br> (
		<input type="checkbox" name="rs_check" value="rs_check" checked>Regular Season
		<input type="checkbox" name="playoffs_check" value="playoffs_check">Playoffs )

		</div>

		<input id="filter_button" class="ui-button ui-widget ui-corner-all" type="submit" value="Filter" name="filter_button">
		</form>
	</div>
</body>

<div id="loading">
  <img id="loading-image" src="/Hoops/Loading/loader.gif" alt="Loading..." />
</div>

</html>