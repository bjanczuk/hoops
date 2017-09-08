<?php
	include '../functions.php';

	$db = new MyDB();

	switch ($graph_type) {
		case "line":
			include "query_line.php";
			break;

		case "bar":
			include "query_bar.php";
			break;

		case "pie":
			include "query_pie.php";
			break;
			
		case "scatter":
			include "query_scatter.php";
			break;

		default:
			echo "ERROR - invalid graph type";
	}
?>