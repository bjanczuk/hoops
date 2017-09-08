<?php
	echo $description;
  	echo "<table border=1 class='tablesorter " . $team_class .  " player_stats' id='" . $table_id . "'>
  			<thead>
			<tr>
			<th data-tip='Season played' class=data-tip-top>Season</th>
			<th data-tip='Team for which the most games were played' class=data-tip-top>Team</th>
			<th data-tip='Years of experience before that season' class=data-tip-top>EXP</th>
			<th data-tip='Total games in the season in which this player played' class=data-tip-top>GP</th>
			<th data-tip='Total games in the season in which this player started' class=data-tip-top>GS</th>
			<th data-tip='Minutes played per game' class=data-tip-top>MIN</th>

			<th data-tip='Effective field goal percentage' class=data-tip-top>eFG%</th>
			<th data-tip='True shooting percentage' class=data-tip-top>TS%</th>
			<th data-tip='Free throw rate' class=data-tip-top>FTR</th>
			<th data-tip='Total rebounding percentage' class=data-tip-top>TRB%</th>
			<th data-tip='Assist percentage' class=data-tip-top>AST%</th>
			<th data-tip='Steal percentage' class='spaced' class=data-tip-top>STL%</th>
			<th data-tip='Block percentage' class=data-tip-top>BLK%</th>
			<th data-tip='Turnover percentage' class=data-tip-top>TOV%</th>
			<th data-tip='Usage percentage' class=data-tip-top>USG%</th>
			<th data-tip='Win shares' class=data-tip-top>WS</th>
			<th data-tip='Win shares per 48 minutes' class=data-tip-top>WS/48</th>
			<th data-tip='Box plus/minus' class=data-tip-top>BPM</th>
			<th data-tip='Value over replacement player' class=data-tip-top>VORP</th>
			<th data-tip='Player Effiency Rating' class=data-tip-top>PER</th>
		</tr></thead>
		<tbody>
		<tr>";

	while ($stats_row = $stats_result->fetchArray()) {
	    echo table_data($stats_row['season']) .
	    	 table_data($stats_row['team']) .
	    	 table_data($stats_row['year_exp']) .
	    	 table_data($stats_row['games_played']) .
	    	 table_data($stats_row['games_started']) .
	    	 table_data($stats_row['minutes']) .
	    	 table_data($stats_row['efg_pct']) .
	    	 table_data($stats_row['tsp']) .
	    	 table_data($stats_row['ftr']) .
	    	 table_data($stats_row['trb_pct']) .
	    	 table_data($stats_row['ast_pct']) .
	    	 table_data($stats_row['stl_pct']) .
	    	 table_data($stats_row['blk_pct']) .
	    	 table_data($stats_row['tov_pct']) .
	    	 table_data($stats_row['usg_pct']) .
	    	 table_data($stats_row['WS']) .
	    	 table_data($stats_row['WS_per']) .
	    	 table_data($stats_row['BPM']) .
	    	 table_data($stats_row['VORP']) .
	    	 table_data($stats_row['PER']) . "</tr><tr>";
	}

	echo "</tr></tbody></table>";
?>