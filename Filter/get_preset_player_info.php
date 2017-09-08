<?php
    $db = new MyDB();
    $all_teams = array();
    $all_seasons = array();
    $all_colleges = array();
    $all_experiences = array();
    $all_draft_years = array();

    $query = $db->prepare('SELECT current_team, college, height, experience, draft_year FROM PLAYER');
	$result = $query->execute();
	while ($row = $result->fetchArray()) {
		array_push($all_teams, $row['current_team']);

        if ($row['college'] != NULL) {
		  array_push($all_colleges, $row['college']);
        }
        
        if ($row['experience'] == 'R') {
            array_push($all_experiences, 0);
        }
        else {
            array_push($all_experiences, $row['experience']);
        }

        if ($row['draft_year'] != NULL) {
          array_push($all_draft_years, $row['draft_year']);
        }
	}

    $query = $db->prepare('SELECT season FROM SEASON_STATS');
    $result = $query->execute();
    while ($row = $result->fetchArray()) {
        array_push($all_seasons, $row['season']);
    }

	$teams = array_unique($all_teams);
	sort($teams);
    foreach ($teams as $t) {
        $new_teams[$t] = $t;
    }
    $teams = $new_teams;

    $seasons = array_unique($all_seasons);
    sort($seasons);
	$colleges = array_unique($all_colleges);
	sort($colleges);
    $experiences = array_unique($all_experiences);
    sort($experiences);
    $draft_years = array_unique($all_draft_years);
    sort($draft_years);

    $heights = array('5-9', '5-10', '5-11', '6-0', '6-1', '6-2', '6-3', '6-4',
                '6-5', '6-6', '6-7', '6-8', '6-9', '6-10', '6-11',
                '7-0', '7-1', '7-2', '7-3');
    $seasons = array_reverse($seasons);
    $draft_years = array_reverse($draft_years);
    $stats = array("_"=>"", "year_exp"=>"year(s) of experience",
            "points"=>"points per game", "assists"=>"assists per game", "o_rebounds"=>"off. rebounds per game",
            "d_rebounds"=>"def. rebounds per game", "rebounds"=>"rebounds per game", "steals"=>"steals per game",
            "blocks"=>"blocks per game", "turnovers"=>"turnovers per game", "fouls"=>"fouls per game", "minutes"=>"minutes per game",
            "games_played"=>"games played", "games_started"=>"games started", "fgm"=>"FGM",
            "fga"=>"FGA", "fgp"=>"FG%", "tpm"=>"3PM", "tpa"=>"3PA", "tpp"=>"3P%", "fta"=>"FTA",
            "ftm"=>"FTM", "ftp"=>"FT%", "efg_pct"=>"eFG%", "tsp"=>"TS%", "ftr"=>"Free Throw Rate",
            "trb_pct"=>"Total Rebounding %", "ast_pct"=>"Assist %", "stl_pct"=>"Steal %",
            "blk_pct"=>"Block %", "tov_pct"=>"Turnover %", "usg_pct"=>"Usage %",
            "WS"=>"Win Shares", "WS_per"=>"Win Shares per 48",
            "BPM"=>"BPM", "VORP"=>"VORP", "PER"=>"PER");

    $line_x_axis_choices = array("season"=>"season", "year_exp"=>"years of experience");
    $line_y_axis_choices = array("points"=>"points per game", "assists"=>"assists per game", "o_rebounds"=>"off. rebounds per game",
            "d_rebounds"=>"def. rebounds per game", "rebounds"=>"rebounds per game", "steals"=>"steals per game",
            "blocks"=>"blocks per game", "turnovers"=>"turnovers per game", "fouls"=>"fouls per game", "minutes"=>"minutes per game",
            "games_played"=>"games played", "games_started"=>"games started", "fgm"=>"FGM",
            "fga"=>"FGA", "fgp"=>"FG%", "tpm"=>"3PM", "tpa"=>"3PA", "tpp"=>"3P%", "fta"=>"FTA",
            "ftm"=>"FTM", "ftp"=>"FT%", "efg_pct"=>"eFG%", "tsp"=>"TS%", "ftr"=>"Free Throw Rate",
            "trb_pct"=>"Total Rebounding %", "ast_pct"=>"Assist %", "stl_pct"=>"Steal %",
            "blk_pct"=>"Block %", "tov_pct"=>"Turnover %", "usg_pct"=>"Usage %",
            "WS"=>"Win Shares", "WS_per"=>"Win Shares per 48",
            "BPM"=>"BPM", "VORP"=>"VORP", "PER"=>"PER");

    $bar_y_axis_choices = array("points"=>"points per game", "assists"=>"assists per game", "o_rebounds"=>"off. rebounds per game",
            "d_rebounds"=>"def. rebounds", "rebounds"=>"rebounds", "steals"=>"steals",
            "blocks"=>"blocks per game", "turnovers"=>"turnovers per game", "fouls"=>"fouls per game", "minutes"=>"minutes per game",
            "games_played"=>"games played", "games_started"=>"games started", "fgm"=>"FGM",
            "fga"=>"FGA", "fgp"=>"FG%", "tpm"=>"3PM", "tpa"=>"3PA", "tpp"=>"3P%", "fta"=>"FTA",
            "ftm"=>"FTM", "ftp"=>"FT%", "efg_pct"=>"eFG%", "tsp"=>"TS%", "ftr"=>"Free Throw Rate",
            "trb_pct"=>"Total Rebounding %", "ast_pct"=>"Assist %", "stl_pct"=>"Steal %",
            "blk_pct"=>"Block %", "tov_pct"=>"Turnover %", "usg_pct"=>"Usage %",
            "WS"=>"Win Shares", "WS_per"=>"Win Shares per 48",
            "BPM"=>"BPM", "VORP"=>"VORP", "PER"=>"PER");
    $bar_y_axis_choices_optional = array("_"=>"", "points"=>"points per game", "assists"=>"assists per game", "o_rebounds"=>"off. rebounds per game",
            "d_rebounds"=>"def. rebounds per game", "rebounds"=>"rebounds per game", "steals"=>"steals per game",
            "blocks"=>"blocks per game", "turnovers"=>"turnovers per game", "fouls"=>"fouls per game", "minutes"=>"minutes per game",
            "games_played"=>"games played", "games_started"=>"games started", "fgm"=>"FGM",
            "fga"=>"FGA", "fgp"=>"FG%", "tpm"=>"3PM", "tpa"=>"3PA", "tpp"=>"3P%", "fta"=>"FTA",
            "ftm"=>"FTM", "ftp"=>"FT%", "efg_pct"=>"eFG%", "tsp"=>"TS%", "ftr"=>"Free Throw Rate",
            "trb_pct"=>"Total Rebounding %", "ast_pct"=>"Assist %", "stl_pct"=>"Steal %",
            "blk_pct"=>"Block %", "tov_pct"=>"Turnover %", "usg_pct"=>"Usage %",
            "WS"=>"Win Shares", "WS_per"=>"Win Shares per 48",
            "BPM"=>"BPM", "VORP"=>"VORP", "PER"=>"PER");

    $player_info_choices = array("position"=>"position", "age"=>"age", "experience"=>"experience",
                                    "draft_pos"=>"draft position", "draft_year"=>"draft year",
                                    "height"=>"height", "weight"=>"weight", "current_team"=>"team",
                                    "college"=>"college", "salary"=>"salary");
?>