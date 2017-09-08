<?php
    $info_headers = array("position"=>"POS",
                        "experience"=>"EXP",
                        "age"=>"AGE",
                        "weight"=>"WT",
                        "height"=>"HT",
                        "college"=>"COL",
                        "draft_pos"=>"DP",
                        "draft_year"=>"DY",
                        "salary"=>"SAL",
                        "season"=>"SEASON",
                        "season_type"=>"TYPE",
                        "year_exp"=>"EXP",
                        "games_played"=>"GP",
                        "games_started"=>"GS",
                        "minutes"=>"MIN",
                        "points"=>"PTS",
                        "fgm"=>"FGM",
                        "fga"=>"FGA",
                        "fgp"=>"FG%",
                        "tpm"=>"3PM",
                        "tpa"=>"3PA",
                        "tpp"=>"3P%",
                        "ftm"=>"FTM",
                        "fta"=>"FTA",
                        "ftp"=>"FT%",
                        "o_rebounds"=>"OREB",
                        "d_rebounds"=>"DREB",
                        "rebounds"=>"REB",
                        "assists"=>"AST",
                        "steals"=>"STL",
                        "blocks"=>"BLK",
                        "turnovers"=>"TOV",
                        "fouls"=>"FLS",
                        "efg_pct"=>"eFG%",
                        "tsp"=>"TS%",
                        "ftr"=>"FTR",
                        "trb_pct"=>"TRB%",
                        "ast_pct"=>"AST%",
                        "stl_pct"=>"STL%",
                        "blk_pct"=>"BLK%",
                        "tov_pct"=>"TOV%",
                        "usg_pct"=>"USG%",
                        "WS"=>"WS",
                        "WS_per"=>"WS/48",
                        "BPM"=>"BPM",
                        "VORP"=>"VORP",
                        "PER"=>"PER");
    $info_tips = array("position"=>"Player's position",
                        "experience"=>"Player's years of experience",
                        "age"=>"Player's current age",
                        "weight"=>"Player's weight",
                        "height"=>"Player's height",
                        "college"=>"Player's college",
                        "draft_pos"=>"Overall draft pick at which the player was selected",
                        "draft_year"=>"Year in which the player was drafted",
                        "salary"=>"Player's 2017-18 salary",
                        "season"=>"Season",
                        "season_type"=>"Regular season or playoffs",
                        "year_exp"=>"Years of experience before that season",
                        "games_played"=>"Total games in the season in which this player played",
                        "games_started"=>"Total games in the season in which this player started",
                        "minutes"=>"Minutes played per game",
                        "points"=>"Points scored per game",
                        "fgm"=>"Field goals made per game",
                        "fga"=>"Field goals attempted per game",
                        "fgp"=>"Field goal percentage",
                        "tpm"=>"Three pointers made per game",
                        "tpa"=>"Three pointers attempted per game",
                        "tpp"=>"Three point field goal percentage",
                        "ftm"=>"Free throws made per game",
                        "fta"=>"Free throws attempted per game",
                        "ftp"=>"Free throw percentage",
                        "o_rebounds"=>"Offensive rebounds per game",
                        "d_rebounds"=>"Defensive rebounds per game",
                        "rebounds"=>"Total rebounds per game",
                        "assists"=>"Assists made per game",
                        "steals"=>"Steals made per game",
                        "blocks"=>"Blocks made per game",
                        "turnovers"=>"Turnovers committed per game",
                        "fouls"=>"Fouls committed per game",
                        "efg_pct"=>"Effective Field Goal percentage",
                        "tsp"=>"True Shooting percentage",
                        "ftr"=>"Free throw rate",
                        "trb_pct"=>"Total rebounding percentage",
                        "ast_pct"=>"Assist percentage",
                        "stl_pct"=>"Steal percentage",
                        "blk_pct"=>"Block percentage",
                        "tov_pct"=>"Turnover percentage",
                        "usg_pct"=>"Usage percentage",
                        "WS"=>"Win Shares",
                        "WS_per"=>"Win Shares per 48",
                        "BPM"=>"Box plus/minus",
                        "VORP"=>"Value over replacement player",
                        "PER"=>"Player Effiency Rating");
    $team_maps = getTeamMap();

    $all_keys = array_merge($player_info_keys, $season_stat_keys);
    $results_rows = array();
    $select = 'SELECT DISTINCT p.name, ';

    if (isset($_POST["filter_breakdown_drop"])) {
        $val = $_POST["filter_breakdown_drop"];
        if (in_array($val, $player_info_keys) === false) {
            array_push($player_info_keys, $val);
            array_push($all_keys, $val);
        }
    }

    foreach ($player_info_keys as $k) {
        $select = $select . 'p.' . $k . ', ';
    }
    foreach ($season_stat_keys as $k) {
        $select = $select . 's.' . $k . ', ';
    }

    if (count($season_stat_keys) == 0) {
        $select = $select . ' p.current_team FROM PLAYER as p WHERE p.player_id = :id ';
        $id_array = $filtered_playerIDs;
    }
    else {
        $select = $select . ' s.team FROM PLAYER as p INNER JOIN SEASON_STATS as s
            ON p.player_id = s.player_id AND s.season_id = :id ';
        $id_array = $filtered_seasonIDs;
    }

    foreach ($id_array as $id) { // add every player's results to the row array
        $query = $db->prepare($select);
        $query->bindValue(':id', $id);
        $result = $query->execute();

        while ($row = $result->fetchArray()) {
            $stop = false;
            foreach ($all_keys as $k) {
                // Skip any players for whom any filter returned true despite a NULL value
                if ($row[$k] !== 0 && ($row[$k] === NULL || $row[$k] == 'NULL')) {
                    $stop = true;
                    break;
                }
            }
            if ($stop == false) {
                array_push($results_rows, $row);
            }
        }
    }

    usort($results_rows, function ($row1, $row2) {
        return strtolower($row1['name']) <=> strtolower($row2['name']);
    });

	include 'make_table.php';
?>