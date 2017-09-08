<?php
    $db = new MyDB();

    $players = array();
    $names = array();

    // First, get every player name
    $stmt = $db->prepare('SELECT name, player_id FROM PLAYER');
    $result = $stmt->execute();

    while ($row = $result->fetchArray()) {
        $p = array($row['name'], $row['player_id']);
        array_push($players, $p);
    }

    // Then get players' points for the last season if they played in it (used for sorting)
    foreach ($players as $p) {
        $stmt = $db->prepare('SELECT points FROM SEASON_STATS as s INNER JOIN PLAYER as p ON s.player_id = :id AND season="2016-17" AND
            season_type="regular_season"');
        $stmt->bindValue(':id', $p[1], SQLITE3_INTEGER);
        $result = $stmt->execute();

        $foundSeason = false;
        while ($row = $result->fetchArray()) {
            $temp = array($p[0], $row['points']);
            array_push($names, $temp);
            $foundSeason = true;
            break;
        }

        if ($foundSeason == false) {
            $temp = array($p[0], 0);
            array_push($names, $temp); // Default to 0 points if they didn't play
        }
    }

    function cmp($a, $b) { // Sort the players by points scored
        return $b[1] - $a[1];
    }
    usort($names, "cmp");

    $only_names = array();
    foreach ($names as $v) {
        array_push($only_names, $v[0]);
    }
    $names = $only_names;

    // Echo the array of players so that the javascript script can access it
    echo '<script>';
    echo 'names = ' . json_encode($names) . ';';
    echo 'sessionStorage.setItem("names", JSON.stringify(names));';
    echo '</script>';
?>