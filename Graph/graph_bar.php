<!DOCTYPE html>
<html>
<head>
	<title>Generate Graphs</title>
	<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
	<link rel="stylesheet" type="text/css" href="style.css">
	<link href="https://fonts.googleapis.com/css?family=Rubik+Mono+One" rel="stylesheet">
	<script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
	<script src="../js/plotly-latest.min.js"></script>
	<script src="../js/graph_functions.js"></script>
</head>

<body>
	<div class="nonpie_output" id="output">
		<div class="graph_again_div">
			<a href="./"><h1 class="graph_again">Graph Again</h1></a>
		</div>

		<div id="alert_div">
			<h3 id="no_graph_alert"></h3>
		</div>
	</div>
</body>

<?php
	$graph_type = "bar";
	$bar_format = "";
	include 'queryDB.php';
?>

<script type="text/javascript">
	var stats = <?php echo json_encode($stats); ?>;
	var results = <?php echo json_encode($results); ?>;
	var player = <?php echo json_encode($player); ?>;
	var x_label = <?php echo json_encode($x_axis); ?>;
	var type = <?php echo json_encode($type); ?>;
	var season_type = <?php echo json_encode($st); ?>;

	var data = [];
	var y_vals;
	var stat;
	var season, seasons;
	var all_seasons = [];
	var player = [];
	var title;

	var players = Object.keys(results);

	// Find all the seasons that will be used first
	for (var p = 0; p < players.length; p++)
	{
		player = players[p];
		seasons = Object.keys(results[player]).sort();

		for (var i = 0; i < seasons.length; i++) {
			season = seasons[i];

			if (!(all_seasons.includes(season))) {
				all_seasons.push(season);
			}
		}
	}
	all_seasons = all_seasons.sort();

	// Input the data into the data array
	for (var p = 0; p < players.length; p++)
	{
		player = players[p];
		seasons = Object.keys(results[player]).sort();

		for (var j = 0; j < stats.length; j++) {
			stat = stats[j];
			y_vals = [];

			for (var i = 0; i < all_seasons.length; i++) {
				season = all_seasons[i];

				if (seasons.includes(season)) {
					y_vals.push(results[player][season][stat]);
				}
				else {
					y_vals.push(null);
				}
			}

			var temp = {
				x: all_seasons,
				y: y_vals,
				type: 'bar',
			};
			if (type == 'stat') {
				temp.name = player;
			}
			else {
				temp.name = convertToTitle(stat);
			}
			data.push(temp);
		}
	}

	// sort the final results so that the seasons are in order on the graph
	data = data.sort(function(a, b) {
	    return a.x[0] > b.x[0] ? 1 : -1;
	});

	// convert the season format if need be
	var passed = false;
	if (x_label == "season") {
		for (var i = 0; i < data.length; i++) {
			if (!passed) {
				for (var j = 0; j < data[i].x.length; j++) {
					x_var = data[i].x[j];
					data[i].x[j] = "'" + x_var.substring(2, 7).replace('-', "-'");
				}
				passed = true;
			}
		}
	}

	if (type == 'player') {
		if (stats.length == 1) {
			title = (player + "'s " + convertToTitle(stats[0]) + " in the " + convertToTitle(season_type) + ", by " + convertToTitle(x_label)).replace("s's", "s'");
		}
		else {
			title = (stats.length.toString() + " of " + player + "'s Statistics in the " + convertToTitle(season_type) + ", by " + convertToTitle(x_label)).replace("s's", "s'");
		}
	}
	else {
		if (players.length == 1) {
			title = (player + "'s " + convertToTitle(stats[0]) + " in the " + convertToTitle(season_type) + ", by " + convertToTitle(x_label)).replace("s's", "s'");
		}
		else {
			title = convertToTitle(stats[0]) + " in the " + convertToTitle(season_type) + ", by " + convertToTitle(x_label);
		}
	}
	var layout = {
		barmode: 'group',
		title: title,
		xaxis: { title: convertToTitle(x_label) }};

	graphIfValid(data, layout);

	document.getElementsByClassName("graph_again_div")[0].style.left = ($(window).innerWidth() * 0.78).toString() + "px";
	document.getElementsByClassName("graph_again")[0].style.font = ($(window).innerWidth() * 0.017).toString() + "px 'Rubik Mono One'";

</script>

</html>