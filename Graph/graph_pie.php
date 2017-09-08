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
	<div class="pie_output" id="output">
		<div id="alert_div">
			<h3 id="no_graph_alert"></h3>
		</div>
	</div>


	<div class="graph_again_div">
		<a href="./"><h1 class="graph_again">Graph Again</h1></a>
	</div>

</body>

<?php
	$graph_type = "pie";
	include 'queryDB.php';
?>

<script type="text/javascript">
	var valid = <?php echo json_encode($valid); ?>;
	var results = <?php echo json_encode($results); ?>;

	if (valid === true && results.length !== 0) {
		var group = <?php echo json_encode($group); ?>;
		var breakdown = <?php echo json_encode($breakdown); ?>;

		var values = [];
		var category;

		var categories = Object.keys(results).sort();

		for (var i = 0; i < categories.length; i++) {
			category = categories[i];
			values.push(results[category]);
		}

		var data = [{
		  values: values,
		  labels: categories,
		  type: 'pie'
		}];

		var layout = {
		  height: 750,
		  width: 750,
		  title: "Breakdown of "
		};

		if (group == "league") {
			layout.title += "the NBA ";
		}
		else if (group == "team") {
			layout.title += 
			<?php
				if (isset($team)) { echo json_encode("the " . $team . " "); }
				else { echo json_encode(""); }
			?>;
		}
		else if (group == "filter") {
			layout.title += "Filtered Players ";
		}

		layout.title = $.trim(layout.title) + ", by " + convertToTitle(breakdown);

		Plotly.newPlot('output', data, layout);
	}
	else {
		document.getElementById("no_graph_alert").innerHTML = "Sorry, there's no graph to display."
	}

	document.getElementsByClassName("pie_output")[0].style.width = document.getElementsByClassName("svg-container")[0].style.width;
	document.getElementsByClassName("pie_output")[0].style.height = document.getElementsByClassName("svg-container")[0].style.height;
	$(".pie_output:eq(0)").css("left", ($(window).innerWidth() - $(".pie_output:eq(0)").width()) / 2);

	document.getElementsByClassName("graph_again_div")[0].style.left = ($(window).innerWidth() * 0.78).toString() + "px";
	document.getElementsByClassName("graph_again")[0].style.font = ($(window).innerWidth() * 0.017).toString() + "px 'Rubik Mono One'";
	
</script>

</html>