function capitalize(s) {
	return s[0].toUpperCase() + s.slice(1);
}

function convertToTitle(k) {
	switch (k) {
		case "BPM":
	    case "VORP":
	    case "PER":
	        return k;

	    case "season":
	    case "points":
	    case "assists":
	    case "rebounds":
	    case "steals":
	    case "blocks":
	    case "turnovers":
	    case "fouls":
	    case "minutes":
	    case "playoffs":
	    case "position":
	    case "age":
	    case "experience":
	    case "height":
	    case "weight":
	    case "team":
	    case "college":
	    case "salary":
	    case "team":
	        return capitalize(k);

	   	case "fgm":
	   	case "fga":
	   	case "ftm":
	   	case "fta":
	   		return k.toUpperCase();

	   	case "fgp":
	   	case "ftp":
	   	case "tsp":
	   		return k[0].toUpperCase() + k[1].toUpperCase() + "%";

	   	case "o_rebounds":
	    	return "Offensive Rebounds";
	   	case "d_rebounds":
	   		return "Defensive Rebounds";
	   	case "tpm":
	   		return "3PM";
	   	case "tpa":
	   		return "3PA";
	   	case "tpp":
	   		return "3P%";
	   	case "games_played":
	   		return "Games Played";
	   	case "games_started":
	   		return "Games Started";
	   	case "draft_year":
	   		return "Draft Year";
	   	case "draft_pos":
	   		return "Draft Position";
	   	case "year_exp":
	   		return "Years of Experience";
	   	case "current_team":
	   		return "Current Team"
	   	case "efg_pct":
	   		return "eFG%";
	   	case "ftr":
	   		return "Free Throw Rate";
	   	case "trb_pct":
	   		return "Total Rebounding %";
	   	case "ast_pct":
	   		return "Assist %";
	   	case "stl_pct":
	   		return "Steal %";
	   	case "blk_pct":
	   		return "Block %";
	   	case "tov_pct":
	   		return "Turnover %";
	   	case "usg_pct":
	   		return "Usage %";
	   	case "WS":
	   		return "Win Shares";
	   	case "WS_per":
	   		return "Win Shares per 48";
	   	case "regular_season":
	   		return "Regular Season";

	    default:
	        return "";
	}
}

function checkTextInputs(elems) {
	var input;
	var valid = false;
	var valid_players = true;

	for (var i = 0; i < elems.length; i++) {
		if (elems[i].id.search("extra") != -1) {
			input = $.trim(elems[i].value);
			
			if (input.length !== 0) {
				if (names.includes(input)) {
					valid = true;
				}
				else {
					alert("Warning: '" + input + "' is not a valid player name.");
					valid_players = false;
				}
			}
		}
	}
	return [valid, valid_players];
}

function checkDropValues(elems) {
	var first_drop = "";
	var count = 0;

	for (var i = 0; i < elems.length; i++) {
		if (elems[i].id.search("drop") != -1) {
			if (elems[i].value === "_" || elems[i].value === "season" || elems[i].value === "years_exp") { continue; }

			count += 1;
			if (first_drop === "") {
				first_drop = elems[i].value;
			}
			else {
				if (elems[i].value != first_drop) {
					return true;
				}
			}
		}
	}
	return (count <= 1);
}

function displayAlerts(valid, dropsAreDifferent) {
	if (valid[0] === false && valid[1] === true) {
		alert("Please enter at least one valid player name.");
	}
	if (dropsAreDifferent === false) {
		alert("Please select two stats that are different.");
	}
}

function graphIfValid(data, layout) {
	if (data.length !== 0 && data[0].x.length !== 0) {
		Plotly.newPlot('output', data, layout);
	}
	else {
		document.getElementById("no_graph_alert").innerHTML = "Sorry, there's no graph to display."
	}
}

function resetTarget(target) {
	var node = document.getElementById(target);
	var cNode = node.cloneNode(false);
	node.parentNode.replaceChild(cNode, node);
}

function cloneIntoTarget(trgt) {
	var target = document.getElementById(trgt);
   	var filterDiv = document.getElementById('filter_input_div');
	[].forEach.call(filterDiv.childNodes, function (node) {
	    target.appendChild(node.cloneNode(true)); 
	});
}

function copySelectsToTarget(trgt) {
	var target = document.getElementById(trgt);
   	var filterDiv = document.getElementById('filter_input_div');
	
    var filter_selects = filterDiv.getElementsByTagName("select");
	var target_selects = target.getElementsByTagName("select");
	for (var p = 0; p < filter_selects.length; p++) {
		target_selects[p].value = filter_selects[p].value;
	}
}