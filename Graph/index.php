<!DOCTYPE html>
<html>
<head>
	<title>Generate Graphs</title>
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="/Hoops/Loading/style.css">
	<link rel="stylesheet" type="text/css" href="../Search/style.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro|Rubik+Mono+One|Roboto+Condensed" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/Hoops/Loading/loading.js"></script>
	<script src="../js/graph_functions.js"></script>
	<script type="text/javascript" src="../js/team_colors.js"></script>

  	<script>
	    // rename the local autocomplete function to avoid a conflict with the new one
	    $.fn.basic_autocomplete = $.fn.autocomplete;
	    delete $.fn.autocomplete;
	</script>

	<script type="text/javascript" src="../js/jquery.autocomplete.min.js"></script>
  	<script type="text/javascript" src="../js/names-autocomplete.js"></script>
	<script src="../js/plotly-latest.min.js"></script>
</head>

<?php
	include '../functions.php';
	include '../Filter/get_preset_player_info.php';
	include '../Search/get_names.php';
	include '../Search/option_form.php';
?>

<script type="text/javascript">
	var counts = [];
	var names = [];
	counts["line"] = 3;
	counts["scatter"] = 3;
	counts["pie"] = 3;
	counts["bar_stat"] = 3;
	counts["bar_player"] = 3;
	var openGraph = "";
	var team;
	var filterConfirmed = false;
	var pie_breakdown = "position";

	$(document).ready(function() {
		for (var i = 0; i < document.getElementById("pie_team_drop").options.length; i++) {
			team = document.getElementById("pie_team_drop").options[i].text.toLowerCase().replace(" ", "_").replace(" ", "_");
			document.getElementById("pie_team_drop").options[i].style.color = team_sc[team];
		}

		// Change the highlight color of buttons on click
		var css = '.graph_button:active { background: #DAA520; color:white; }';
	    var style = document.createElement('style');

	    if (style.styleSheet) {
	        style.styleSheet.cssText = css;
	    } else {
	        style.appendChild(document.createTextNode(css));
	    }
	    document.getElementsByTagName('head')[0].appendChild(style);
	});

	$( function() {
    	names = JSON.parse(sessionStorage.getItem("names"));
    	$(".player_names").basic_autocomplete({
    		source: names
    	});
    } );

	function showGraphHeader(graph) {
		if (openGraph == "") {
			document.getElementById(graph + "_header").style.display = "block";
			document.getElementById(graph + "_background").setAttribute("style","-webkit-filter: blur(5px)");
		}
	}

	function hideGraphHeader(graph) {
		if (openGraph == "") {
			document.getElementById(graph + "_header").style.display = "none";
			document.getElementById(graph + "_background").setAttribute("style","-webkit-filter: blur(0px)");
		}
	}

	function graphClick(graph) {
		if (openGraph == "") {
			if (graph != "bar") {
				document.getElementById(graph + "_form").style.display = "block";
			}
			else {
				document.getElementById("bar_form_stat").style.display = "block";
				document.getElementById("bar_plus_button_stat").style.display = "block";
			}

			if (graph != "pie" && graph != "bar") {
				document.getElementById(graph + "_plus_button").style.display = "block";
			}

			openGraph = graph;
			document.getElementById("four_options").setAttribute("style","-webkit-filter: blur(5px)");
			document.getElementById("header").setAttribute("style","-webkit-filter: blur(5px)");
		}
	}

	function closeGraph(graph) {
		if (graph === "filter") {
			document.getElementById("filter_input_div").style.display = "none";
		    document.getElementById("pie_form_wrapper").style.display = "inline";
		    document.getElementById("header").style.display = "block";
		}
		if (graph != "bar") {
			document.getElementById(graph + "_form").style.display = "none";
		}
		else {
			document.getElementById("bar_form_stat").style.display = "none";
			document.getElementById("bar_plus_button_stat").style.display = "none";
			document.getElementById("bar_form_player").style.display = "none";
			document.getElementById("bar_plus_button_player").style.display = "none";
		}

		if (graph != "pie" && graph != "bar") {
			document.getElementById(graph + "_plus_button").style.display = "none";
		}

		openGraph = "";
		document.getElementById("four_options").setAttribute("style","-webkit-filter: blur(0px)");
		document.getElementById("header").setAttribute("style","-webkit-filter: blur(0px)");
		hideGraphHeader(graph);
	}

	function addNewInput(graph) {
		count = counts[graph];
		if (openGraph != "bar") {
			if (count <= 10) {
				document.getElementById(openGraph + "_extra_name_" + String(count)).style.display = "block";
				document.getElementById(openGraph + "_form_wrapper").style.height = String(160 + (count - 1) * 35) + "px";
			}
		}
		else {
				var bar_type = graph.split('_')[1];
				if (bar_type == "stat") {
					if (count <= 10) {
						document.getElementById("bar_extra_name_" + String(count) + "_stat").style.display = "block";
						document.getElementById("bar_form_wrapper_" + bar_type).style.height = String(185 + (count - 1) * 37) + "px";
					}
				}
				else {
					if (count <= 5) {
						document.getElementById("bar_y_axis_drop_player_" + String(count)).style.display = "block";
						document.getElementById("bar_form_wrapper_" + bar_type).style.height = String(180 + (count - 1) * 40) + "px";
					}
				}
			}
		counts[graph] += 1;
	}

	function checkGroupDropdown(select) {
		if (select.options[select.selectedIndex].text == "a team") {
			var teams = <?php echo json_encode($teams); ?>;
			var option;

			var new_drop = document.createElement("select");
			new_drop.name = "pie_team_drop";
			new_drop.id = "pie_team_drop";

			for (var i = 0; i < teams.length; i++) {
				option = document.createElement("option");
				option.text = teams[i];
				new_drop.add(option);
			}

			var width = $('#pie_team_drop').width();
			var parentWidth = $('#pie_team_drop').parent().width();
			var percent = Math.round(Math.abs(100 * width / parentWidth));

			document.getElementById("pie_team_drop").style.display = "block";
			document.getElementById("pie_team_drop").style.left = ((100 - percent) / 2).toString() + "%";
			document.getElementById("pie_breakdown_label").style.top = "50px";
			document.getElementById("pie_breakdown_drop").style.top = "30px";
			document.getElementById("pie_form_wrapper").style.height = "160px";
		}
		else {
			document.getElementById("pie_team_drop").style.display = "none";
			document.getElementById("pie_breakdown_label").style.top = "";
			document.getElementById("pie_breakdown_drop").style.top = "";
			document.getElementById("pie_form_wrapper").style.height = "135px";
		}

		if (select.options[select.selectedIndex].text == "filtered players") {
			document.getElementsByClassName("filter_input_div")[0].style.display = "table";
			document.getElementById("pie_form_wrapper").style.display = "none";
			document.getElementById("header").style.display = "none";
		}
		else {
			document.getElementsByClassName("filter_input_div")[0].style.display = "none";
			document.getElementById("pie_form_wrapper").style.display = "inline";
			document.getElementById("header").style.display = "block";
			filterConfirmed = false;
		}
	}

	function barRadioChange(type) {
		var opposite = type == 'stat' ? 'player' : 'stat';
		document.getElementById("bar_form_" + type).style.display = "block";
		document.getElementById("bar_form_" + opposite).style.display = "none";

		// make the radio buttons in the newly visible form checked correctly 
		document.getElementById("bar_radio_" + type + "_btn").checked = true;
		document.getElementById("bar_radio_" + opposite + "_btn").checked = true;
	}

	function markSelected(s) {
		s.options[s.selectedIndex].setAttribute("selected","");

		if (s.name === "pie_breakdown_drop") {
			var elem;
			var filter_break = document.getElementById("filter_breakdown_drop");

			filter_break.selectedIndex = s.selectedIndex;
			filter_break.options[filter_break.selectedIndex].setAttribute("selected","");
			filter_break.value = s.value;

			var elems = document.getElementById("new_tab_data").getElementsByTagName("*");
		    for (var i = 0; i < elems.length; i++) {
		        if (elems[i].id === "filter_breakdown_drop") {
		            elem = elems[i];
		            break;
		        }
		    }
		    if (elem !== undefined) {
			    elem.selectedIndex = s.selectedIndex;
				elem.options[filter_break.selectedIndex].setAttribute("selected","");
				elem.value = s.value;
			}
		}
  	}

	function filterConfirm() {
		// reset the target divs
		resetTarget("pie_filter_data");
		resetTarget("new_tab_data");

		// clone all of the elements into the target divs
		cloneIntoTarget("pie_filter_data");
		cloneIntoTarget("new_tab_data");
	   	
		// explicitly copy each of the select values to the target divs
    	copySelectsToTarget("pie_filter_data");
		copySelectsToTarget("new_tab_data");

    	// hide the filter form and bring the main pie form back up
	    document.getElementById("filter_input_div").style.display = "none";
	    document.getElementById("pie_form_wrapper").style.display = "inline";
	    document.getElementById("header").style.display = "block";

	    filterConfirmed = true;
	}

	$(document).keypress(function(e) {
		if(e.which == 13 && openGraph === "pie") {
			filterConfirm();
		}
	});

	function isValidForm(graph) {
		var elems;
		var valid;
		var dropsAreDifferent;
		var input;

		switch (graph) {
			case "line":
				elems = document.getElementById("line_form").elements;
				valid = checkTextInputs(elems);

				displayAlerts(valid, true);

				return (valid[0] && valid[1]);
				break;

			case "scatter":
				elems = document.getElementById("scatter_form").elements;
				valid = checkTextInputs(elems);
			 	dropsAreDifferent = checkDropValues(elems);

				displayAlerts(valid, dropsAreDifferent);

				return (valid[0] && valid[1] && dropsAreDifferent);
				break;

			case "bar_stat":
				elems = document.getElementById("bar_form_stat").elements;
 				valid = checkTextInputs(elems);

 				displayAlerts(valid, true);

 				return (valid[0] && valid[1]);
				break;

			case "bar_player":
				elems = document.getElementById("bar_form_player").elements;
				valid = checkTextInputs(elems);
			 	dropsAreDifferent = checkDropValues(elems);

			 	displayAlerts(valid, dropsAreDifferent);

			 	return (valid[0] && valid[1] && dropsAreDifferent);
				break;

			default:
				alert("Error: Invalid graph");
		}
	}

	function submitPieForms() {
		if (filterConfirmed) {
	    	document.getElementById("filter_new_tab").submit();
	    	setTimeout("submitMainPieForm()", 1000);
		}
		else {
			submitMainPieForm();
		}
		
		return false;
	}

	function submitMainPieForm() {
		document.getElementById("pie_form").submit();
	}

</script>

<body>
	<div class="graph_backgrounds" id="four_options">
		
		<div class="graph_wrapper" id="line_wrapper" onmouseover="showGraphHeader('line')" onmouseout="hideGraphHeader('line')" onclick="graphClick('line')">
			<div class="graph_backgrounds" id="line_background"></div>
			<h1 class="graph_header" id="line_header">Line</h1>
		</div>

		<div class="graph_wrapper" id="bar_wrapper" onmouseover="showGraphHeader('bar')" onmouseout="hideGraphHeader('bar')" onclick="graphClick('bar')">
			<div class="graph_backgrounds" id="bar_background"></div>
			<h1 class="graph_header" id="bar_header">Bar</h1>
		</div>

		<div class="graph_wrapper" id="scatter_wrapper" onmouseover="showGraphHeader('scatter')" onmouseout="hideGraphHeader('scatter')" onclick="graphClick('scatter')">
			<div class="graph_backgrounds" id="scatter_background"></div>
			<h1 class="graph_header" id="scatter_header">Scatter</h1>
		</div>

		<div class="graph_wrapper" id="pie_wrapper" onmouseover="showGraphHeader('pie')" onmouseout="hideGraphHeader('pie')" onclick="graphClick('pie')">
			<div class="graph_backgrounds" id="pie_background"></div>
			<h1 class="graph_header" id="pie_header">Pie</h1>
		</div>
	</div>

	<div class="form_wrapper" id="line_form_wrapper">
		<form id="line_form" class="input_form" method="post" action="graph_line.php">
			<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('line')"/>

			<div class="line_season_type">
					<input type="radio" name="line_season_type_radio" value="regular_season" checked> Regular Season
					<input type="radio" name="line_season_type_radio" value="playoffs"> Playoffs<br>
	  		</div>

			<div id="line_drops">
				<div id="line_left_side">
					<p id="line_x_axis_label">x-axis:</p><br><br>
					<select id="line_x_axis_drop" name="line_x_axis_drop">
						<?php
							displayArrayAsSelectOptions($line_x_axis_choices);
						?>
					</select><br>
					<input name="extra_name_1" class="player_names" id="line_extra_name_1" placeholder="Select a player:"><br>
				</div>

				<div id="line_right_side">
					<p id="line_y_axis_label">y-axis:</p><br><br>
					<select id="line_y_axis_drop" name="line_y_axis_drop">
						<?php
							displayArrayAsSelectOptions($line_y_axis_choices);
						?>
					</select><br>
					<input name="extra_name_2" class="player_names" id="line_extra_name_2" placeholder="Select a player:"><br>
				</div>
			</div>

			<div class="hidden_text_inputs">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_3" id="line_extra_name_3" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_4" id="line_extra_name_4" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_5" id="line_extra_name_5" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_6" id="line_extra_name_6" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_7" id="line_extra_name_7" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_8" id="line_extra_name_8" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_9" id="line_extra_name_9" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_10" id="line_extra_name_10" placeholder="Select another player:">
			</div>

			<img id="line_plus_button" src="../Images/plus_button.png" alt="Plus Button" onclick="addNewInput('line')"/>
			<input class="submit_button ui-button ui-widget ui-corner-all graph_button" type="submit" value="Graph" onclick="return isValidForm('line')">
		</form>
	</div>

	<div class="form_wrapper" id="bar_form_wrapper_stat">
		<form id="bar_form_stat" class="input_form" method="post" action="graph_bar.php">
			<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('bar')"/>

			<br>
			<div id="bar_radio_div_stat">
				<input id="bar_radio_stat_btn" type="radio" name="bar_radio" value="stat" onchange="barRadioChange('stat')" checked> Select Multiple Players, 1 Stat
				<input type="radio" name="bar_radio" value="player" onchange="barRadioChange('player')"> Select 1 Player, Multiple Stats<br>
  			</div>

  			<div class="bar_season_type">
				<input type="radio" name="bar_season_type_radio" value="regular_season" checked> Regular Season
				<input type="radio" name="bar_season_type_radio" value="playoffs"> Playoffs<br>
  			</div>

				<div id="bar_drops_stat">
					<div id="bar_left_side_stat">
						<p id="bar_x_axis_label_stat">x-axis:</p><br><br>
						<select id="bar_x_axis_drop_stat" name="bar_x_axis_drop">
							<?php
								displayArrayAsSelectOptions($line_x_axis_choices);
							?>
						</select><br>
						<input name="extra_name_1" class="player_names" id="bar_extra_name_1_stat" placeholder="Select a player:"><br>
					</div>

					<div id="bar_right_side_stat">
						<p id="bar_y_axis_label_stat">stat:</p><br><br>
						<select id="bar_y_axis_drop_stat" name="bar_y_axis_drop_1">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices);
							?>
						</select><br>
						<input name="extra_name_2" class="player_names" id="bar_extra_name_2_stat" placeholder="Select a player:"><br>
					</div>
				</div>

				<div class="hidden_text_inputs">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_3" id="bar_extra_name_3_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_4" id="bar_extra_name_4_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_5" id="bar_extra_name_5_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_6" id="bar_extra_name_6_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_7" id="bar_extra_name_7_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_8" id="bar_extra_name_8_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_9" id="bar_extra_name_9_stat" placeholder="Select another player:">
					<input class="text_inputs hidden_text_input player_names" name="extra_name_10" id="bar_extra_name_10_stat" placeholder="Select another player:">
				</div>

			<img id="bar_plus_button_stat" src="../Images/plus_button.png" alt="Plus Button" onclick="addNewInput('bar_stat')"/>
			<input class="submit_button ui-button ui-widget ui-corner-all graph_button" type="submit" value="Graph" onclick="return isValidForm('bar_stat')">
		</form>
	</div>

	<div class="form_wrapper" id="bar_form_wrapper_player">
		<form id="bar_form_player" class="input_form" method="post" action="graph_bar.php">
			<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('bar')"/>

			<br>
			<div id="bar_radio_div_player">
				<input type="radio" name="bar_radio" value="stat" onchange="barRadioChange('stat')"> Select Multiple Players, 1 Stat
				<input id="bar_radio_player_btn" type="radio" name="bar_radio" value="player" onchange="barRadioChange('player')" checked> Select 1 Player, Multiple Stats<br>
  			</div>

  			<div class="bar_season_type">
				<input type="radio" name="bar_season_type_radio" value="regular_season" checked> Regular Season
				<input type="radio" name="bar_season_type_radio" value="playoffs"> Playoffs<br>
  			</div>

				<div id="bar_drops_player">
					<div id="bar_left_side_player">
						<p id="bar_x_axis_label_player">x-axis:</p><br><br>
						<select id="bar_x_axis_drop_player" name="bar_x_axis_drop">
							<?php
								displayArrayAsSelectOptions($line_x_axis_choices);
							?>
						</select><br>
						<select id="bar_y_axis_drop_player_1" name="bar_y_axis_drop_1">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices);
							?>
						</select><br>
					</div>

					<div id="bar_right_side_player">
						<p id="bar_y_axis_label_player">player:</p><br><br><br>
						<input name="extra_name_1" class="player_names" id="bar_extra_name_1_player" placeholder="Select a player:"><br>
						<select id="bar_y_axis_drop_player_2" name="bar_y_axis_drop_2">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices_optional);
							?>
						</select><br>
					</div>
				</div>

				<div class="hidden_text_inputs">
						<select class="hidden_text_input" id="bar_y_axis_drop_player_3" name="bar_y_axis_drop_3">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices_optional);
							?>
						</select><br>
						<select class="hidden_text_input" id="bar_y_axis_drop_player_4" name="bar_y_axis_drop_4">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices_optional);
							?>
						</select><br>
						<select class="hidden_text_input" id="bar_y_axis_drop_player_5" name="bar_y_axis_drop_5">
							<?php
								displayArrayAsSelectOptions($bar_y_axis_choices_optional);
							?>
						</select><br>
				</div>

			<img id="bar_plus_button_player" src="../Images/plus_button.png" alt="Plus Button" onclick="addNewInput('bar_player')"/>
			<div id="bar_player_submit_div">
				<input id="bar_player_submit_button" class="ui-button ui-widget ui-corner-all graph_button" type="submit" value="Graph" onclick="return isValidForm('bar_player')">
			</div>
		</form>
	</div>

	<div class="form_wrapper" id="pie_form_wrapper">
		<form id="pie_form" class="input_form" method="post" action="graph_pie.php">
			<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('pie')"/>

			<br>
			<div id="pie_drops">
				<div id="pie_left_side">
					<p id="pie_group_label">Break down:</p><br>
					<p id="pie_breakdown_label">by:</p><br><br><br>
				</div>

				<div id="pie_right_side">
					<br><select id="pie_group_drop" name="pie_group_drop" onchange="checkGroupDropdown(this)">
						<option value="league">the league</option>
						<option value="team">a team</option>
						<option value="filter">filtered players</option>
					</select><br>
					<br id="pie_break">
					<br><select id="pie_breakdown_drop" onchange="markSelected(this);" name="pie_breakdown_drop">
						<?php
							displayArrayAsSelectOptions($player_info_choices);
						?>
					</select><br>
				</div>
			</div>

			<select id="pie_team_drop" name="pie_team_drop">
				<?php
					displayArrayAsSelectOptions($teams);
				?>
			</select>

			<div id="pie_submit_div">
				<input id="pie_submit_button" class="ui-button ui-widget ui-corner-all graph_button" type="submit" value="Graph" onclick="return submitPieForms()">
			</div>

			<div class="copied_filter_data" id="pie_filter_data"></div>
		</form>
	</div>

	<div class="form_wrapper" id="scatter_form_wrapper">
		<form id="scatter_form" class="input_form" method="post" action="graph_scatter.php">
			<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('scatter')"/>

			<div class="scatter_season_type">
				<input type="radio" name="scatter_season_type_radio" value="regular_season" checked> Regular Season
				<input type="radio" name="scatter_season_type_radio" value="playoffs"> Playoffs<br>
	  		</div>

			<div id="scatter_drops">
				<div id="scatter_left_side">
					<p id="scatter_x_axis_label">x-axis:</p><br><br>
					<select id="scatter_x_axis_drop" name="scatter_x_axis_drop">
						<?php
							displayArrayAsSelectOptions($line_y_axis_choices);
						?>
					</select><br>
					<input name="extra_name_1" class="player_names" id="scatter_extra_name_1" placeholder="Select a player:"><br>
				</div>

				<div id="scatter_right_side">
					<p id="scatter_y_axis_label">y-axis:</p><br><br>
					<select id="scatter_y_axis_drop" name="scatter_y_axis_drop">
						<?php
							displayArrayAsSelectOptions($line_y_axis_choices);
						?>
					</select><br>
					<input name="extra_name_2" class="player_names" id="scatter_extra_name_2" placeholder="Select a player:"><br>
				</div>
			</div>

			<div class="hidden_text_inputs">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_3" id="scatter_extra_name_3" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_4" id="scatter_extra_name_4" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_5" id="scatter_extra_name_5" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_6" id="scatter_extra_name_6" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_7" id="scatter_extra_name_7" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_8" id="scatter_extra_name_8" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_9" id="scatter_extra_name_9" placeholder="Select another player:">
				<input class="text_inputs hidden_text_input player_names" name="extra_name_10" id="scatter_extra_name_10" placeholder="Select another player:">
			</div>

			<img id="scatter_plus_button" src="../Images/plus_button.png" alt="Plus Button" onclick="addNewInput('scatter')"/>
			<input class="submit_button ui-button ui-widget ui-corner-all graph_button" type="submit" value="Graph" onclick="return isValidForm('scatter')">
		</form>
	</div>

	<div id="filter_input_div" class="filter_input_div">
		<img class="exit_button" src="../Images/exit_button.png" alt="Exit Button" onclick="closeGraph('filter')"/>
		<h1 id="find_every_player">Include every current player:</h1>

			<div id="player_info_filter">
			whose name contains
				<input type="text" name="name_spec">
				<br/><br/>
			who plays for the
				<select onchange="markSelected(this);" name="current_team_drop">
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
				<select onchange="markSelected(this);" name="height_drop">
				  <option value="at_least">at least</option>
				  <option value="at_most">at most</option>
				  <option value="exactly">exactly</option>
				</select>
				<select onchange="markSelected(this);" name="height_spec">
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
				<select onchange="markSelected(this);" name="weight_drop">
				  <option value="at_least">at least</option>
				  <option value="at_most">at most</option>
				  <option value="exactly">exactly</option>
				</select>
				<input type="text" name="weight_spec">
				lbs. <br/><br/>
			who went to college at
				<select onchange="markSelected(this);" name="college_drop">
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
				<select onchange="markSelected(this);" name="draft_pos_drop">
				  <option value="at_least">at least</option>
				  <option value="at_most">at most</option>
				  <option value="exactly">exactly</option>
				</select>
				#<input type="text" name="draft_pos_spec"> overall
				<br/><br/>
			who was drafted
				<select onchange="markSelected(this);" name="draft_year_drop">
				  <option value="in">in</option>
				  <option value="before">before</option>
				  <option value="after">after</option>
				</select>
				<select onchange="markSelected(this);" name="draft_year_spec">
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
				<select onchange="markSelected(this);" name="experience_drop">
				  <option value="at_least">at least</option>
				  <option value="at_most">at most</option>
				  <option value="exactly">exactly</option>
				</select>
				<select onchange="markSelected(this);" name="experience_spec">
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
				<select onchange="markSelected(this);" name="age_drop">
				  <option value="at_least">at least</option>
				  <option value="at_most">at most</option>
				  <option value="exactly">exactly</option>
				</select>
				<input type="text" name="age_spec">
				years old <br/><br/>
			whose '17-'18 salary is
				<select onchange="markSelected(this);" name="salary_drop">
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
			<br/>

			<div id="unique_break">
				<span id="season_header">who has any seasons matching the following criteria:</span>
				<br>
			</div>
			
			<?php
				for ($i = 1; $i <= 8; $i++) {
					$n = strval($i);
					echo '<select onchange="markSelected(this);" name="operator_' . $n . '">
						<option value="at_least">at least</option>
						<option value="at_most">at most</option>
						<option value="exactly">exactly</option>
					</select>
					<input type="text" name="spec_' . $n . '">
					<select onchange="markSelected(this);" name="stat_drop_' . $n . '">';
					foreach ($stats as $k=>$v) {
						echo '<option value="' . $k .
							'">' . $v . '</option>';
					}
					echo "</select><br/><br/>";
				}
			?>

			Limit his team in the season to the
				<select onchange="markSelected(this);" name="season_team_drop">
					<option value="_"></option>
					<?php
						foreach ($teams as $t) {
							echo '<option value="' . $t .
							'">' . $t . '</option>';
						}
					?>
				</select>
				<br/><br/>

			Limit the search to averages from
			<select onchange="markSelected(this);" name="season_drop">
			  <option value="only_the">only the</option>
			  <option value="after_the">after the</option>
			  <option value="before_the">before the</option>
			</select>
			<select onchange="markSelected(this);" name="season_spec">
				<option value="any">any</option>
				<?php
					foreach ($seasons as $s) {
						echo '<option value="' . $s .
						'">' . $s . '</option>';
					}
				?>
			</select>
			season <br>(
			<input type="checkbox" name="rs_check" value="rs_check" checked>Regular Season
			<input type="checkbox" name="playoffs_check" value="playoffs_check">Playoffs )

			</div>

			<select id="filter_breakdown_drop" name="filter_breakdown_drop">
				<?php
					displayArrayAsSelectOptions($player_info_choices);
				?>
			</select>

			<button class="ui-button ui-widget ui-corner-all graph_button" id="confirm_button" type="button" name="confirm_button" onclick="filterConfirm()">Confirm</button>
	</div>

	<form id="filter_new_tab" target="_blank" method="post" action="../Filter/filter.php"><div id="new_tab_data"></div></form>
</body>

<div id="loading">
  <img id="loading-image" src="/Hoops/Loading/loader.gif" alt="Loading..." />
</div>

</html>