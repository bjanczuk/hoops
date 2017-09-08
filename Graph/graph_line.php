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
		<div id="alert_div">
			<h3 id="no_graph_alert"></h3>
		</div>
	</div>


	<div class="graph_again_div">
		<a href="./"><h1 class="graph_again">Graph Again</h1></a>
	</div>
</body>

<?php
	$graph_type = "line";
	include 'queryDB.php';
?>

<script type="text/javascript">
	var x_label = <?php echo json_encode($x_label); ?>;
	var y_label = <?php echo json_encode($y_label); ?>;
	var results = <?php echo json_encode($results); ?>;
	var season_type = <?php echo json_encode($st); ?>

	var data = []
	var all_x_vars = []
	var all_seasons = [];
	var x_vals, y_vals, player;

	var players = Object.keys(results).sort();

	// Find all the seasons that will be used first
	for (var p = 0; p < players.length; p++)
	{
		player = players[p];

		seasons = Object.keys(results[player]);

		for (var i = 0; i < seasons.length; i++) {
			season = seasons[i];

			if (!(all_seasons.includes(season))) {
				all_seasons.push(season);
			}
		}
	}
	if (season_type === "season") {
		all_seasons = all_seasons.sort();
	}
	else {
		all_seasons = all_seasons.sort(function(a,b){return parseFloat(a) - parseFloat(b)});
	}

	// Input the data into the data array
	for (var i = 0; i < players.length; i++) {
		x_vals = [];
		y_vals = [];

		player = players[i];
		if (x_label != "season") {
			x_vars = Object.keys(results[player]).sort(function(a,b){return parseFloat(a) - parseFloat(b)});
		}
		else {
	    	x_vars = Object.keys(results[player]).sort();
	    }
		all_x_vars.push.apply(all_x_vars, x_vars);

	    for (var j = 0; j < all_seasons.length; j++) {
	    	var x_var = all_seasons[j];
	    	x_vals.push(x_var)
	    	if (x_vars.includes(x_var)) {
			   	y_vals.push(results[player][x_var]);
			}
			else {
				y_vals.push(null);
			}
	    }

		var temp = {
			x: x_vals,
			y: y_vals,
			type: 'scatter',
			name: player,
			line: { dash: "dashdot", width: 3}
		};

		data.push(temp);
	}

	// sort the final results so that the x-axis values are in order on the graph
	data = data.sort(function(a, b) {
	    return a.x[0] > b.x[0] ? 1 : -1;
	});

	// convert the season format if need be
	if (x_label == "season") {
		for (var i = 0; i < data.length; i++) {
			for (var j = 0; j < data[i].x.length; j++) {
				x_var = data[i].x[j];
				data[i].x[j] = "'" + x_var.substring(2, 7).replace('-', "-'");
			}
		}
	}

	var layout = {
		title: convertToTitle(y_label) + ", by "  + convertToTitle(x_label),
		xaxis: {
			nticks: all_x_vars.filter(function (element, i, arr) { return arr.indexOf(element) === i; }).length,
			title: convertToTitle(x_label) },
	};


	if (players.length == 1) {
		layout.title = (players[0] + "'s ").replace("s's", "s'") + layout.title;
	}

	graphIfValid(data, layout);

	document.getElementsByClassName("graph_again_div")[0].style.left = ($(window).innerWidth() * 0.78).toString() + "px";
	document.getElementsByClassName("graph_again")[0].style.font = ($(window).innerWidth() * 0.017).toString() + "px 'Rubik Mono One'";

</script>

</html>