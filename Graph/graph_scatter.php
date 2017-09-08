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
	$graph_type = "scatter";
	include 'queryDB.php';
?>

<script type="text/javascript">
	var x_label = <?php echo json_encode($x_label); ?>;
	var y_label = <?php echo json_encode($y_label); ?>;
	var results = <?php echo json_encode($results); ?>;
	var season_type = <?php echo json_encode($st); ?>;

	var data = []
	var all_x_vars = []
	var x_vals, y_vals, text, player, short_seasons;

	var players = Object.keys(results).sort();

	for (var i = 0; i < players.length; i++) {
		x_vals = [];
		y_vals = [];
		short_seasons = [];
		text = [];

		player = players[i];
		keys = Object.keys(results[player]);

	    for (var j = 0; j < keys.length; j++) {
	    	key = keys[j];
	    	if (key == "season" || key == "year_exp") {
	    		continue;
	    	}
	    	else {
	    		int_key = parseInt(key);
			   	y_vals[int_key] = results[player][key][1];
			   	x_vals[int_key] = results[player][key][0];
			   	text[int_key] = results[player]['season'][int_key];
			}
	    }

		var temp = {
			x: x_vals,
			y: y_vals,
			mode: 'markers',
			type: 'scatter',
			name: player,
			text: text,
			marker: {size: 15}
		};

		data.push(temp);
	}

	// sort the final results so that the x-axis values are in order on the graph
	data = data.sort(function(a, b) {
	    return a.x[0] > b.x[0] ? 1 : -1;
	});

	var layout = {
	  title: convertToTitle(x_label) + " vs. "  + convertToTitle(y_label) + " in the " + convertToTitle(season_type),
	  xaxis: {
	  	nticks: all_x_vars.filter(function (element, i, arr) {
			return arr.indexOf(element) === i; }).length,
		title: convertToTitle(x_label) },
	  yaxis: {title: convertToTitle(y_label) },
	  hovermode: "closest"
	};

	var count = 0;
	var title_player = '';

	for (var i = 0; i < data.length; i++) {
		if (data[i].x.length > 0) {
			count += 1;
			title_player = players[i];
		}
		if (count === 2) { break; }
	}
	if (count === 1) {
		layout.title = (title_player + "'s ").replace("s's", "s'") + layout.title;
	}

	graphIfValid(data, layout);

	document.getElementsByClassName("graph_again_div")[0].style.left = ($(window).innerWidth() * 0.78).toString() + "px";
	document.getElementsByClassName("graph_again")[0].style.font = ($(window).innerWidth() * 0.017).toString() + "px 'Rubik Mono One'";
	
</script>

</html>