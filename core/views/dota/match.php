<?php
$match = $this->match;
$players = $this->players;

$radiant_players = 0;
$dire_players = 0;
foreach ($players as $player) {
    if ($player->player_slot < 100) $radiant_players++;
    else $dire_players++;
}

$table_header = '<thead>
    <tr>
        <th class="player">Player</th>
        <th class="level">Level</th>
        <th class="hero">Hero</th>
        <th class="kills">K</th>
        <th class="deaths">D</th>
        <th class="assists">A</th>
        <th class="items" colspan="6">Items</th>
        <th class="gold">Gold</th>
        <th class="last_hits">Last hits</th>
        <th class="denies">Denies</th>
        <th class="gold_per_min">GPM</th>
        <th class="xp_per_min">XPM</th>
    </tr>
    </thead>';

function printPlayer($player)
{
    echo '<tr>';
    echo '<th class="player">';
    if (empty($player->account_id)) {
        echo "<em>Private</em>";
    } else {
        echo '<a href="/users/' . $player->account_id . '">';
        if (empty($player->nickname)) {
            echo $player->account_id;
        } else {
            echo $player->nickname;
        }
        echo '</a>';
    }
    echo '</th>';
    echo '<th class="level">' . $player->level . '</th>';
    echo '<th class="hero">';
    echo '<img src="/assets/img/dota/heroes/' . substr($player->hero_name, 14) . '.png"/> ';
    if (empty($player->hero_display_name)) {
        echo $player->hero_name;
    } else {
        echo         $player->hero_display_name;
    }
    echo '</th>';
    echo '<th class="kills">' . $player->kills . '</th>'
        . '<th class="deaths">' . $player->deaths . '</th>'
        . '<th class="assists">' . $player->assists . '</th>'
        . '<th class="item_0">' . $player->item_0 . '</th>'
        . '<th class="item_1">' . $player->item_1 . '</th>'
        . '<th class="item_2">' . $player->item_2 . '</th>'
        . '<th class="item_3">' . $player->item_3 . '</th>'
        . '<th class="item_4">' . $player->item_4 . '</th>'
        . '<th class="item_5">' . $player->item_5 . '</th>'
        . '<th class="gold">' . $player->gold . '</th>'
        . '<th class="last_hits">' . $player->last_hits . '</th>'
        . '<th class="denies">' . $player->denies . '</th>'
        . '<th class="gold_per_min">' . $player->gold_per_min . '</th>'
        . '<th class="xp_per_min">' . $player->xp_per_min . '</th>';
    echo '</tr>';
}
?>

<h3>Match #<?php echo $match->id; ?></h3>

Game mode: <?php echo $match->game_mode; ?>

<p>
    Start time: <?php echo date(DATE_RFC850, $match->start_time); ?>
    <br/>Duration:
    <?php
    $duration_min = floor($match->duration / 60);
    $duration_sec = $match->duration - $duration_min * 60;
    echo $duration_min . ':' . $duration_sec;
    ?>
    <br/>Season: <?php echo $match->season; ?>
</p>

<h4 id="winner">
    <?php
    if ($match->radiant_win)
        echo "Radiant victory!";
    else
        echo "Dire victory!";
    ?>
</h4>

<span class="side"><img class="side-icon" src="/assets/img/dota/pip_radiant.png"/>The Radiant</span>
<?php if ($radiant_players > 0) { ?>
    <table class="match-table table table-condensed table-hover">
        <?php echo $table_header; ?>
        <tbody>
        <?php foreach ($players as $player) {
            if ($player->player_slot < 100) {
                printPlayer($player);
            }
        } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="alert">No players on the radiant side.</div>
<?php } ?>

<span class="side"><img class="side-icon" src="/assets/img/dota/pip_dire.png"/>The Dire</span>
<?php if ($dire_players > 0) { ?>
    <table class="match-table table table-condensed table-hover">
        <?php echo $table_header; ?>
        <tbody>
        <?php foreach ($players as $player) {
            if ($player->player_slot > 100) {
                printPlayer($player);
            }
        } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="alert">No players on the dire side.</div>
<?php } ?>

tower_status_radiant: <?php echo $match->tower_status_radiant; ?><br/>
tower_status_dire: <?php echo $match->tower_status_dire; ?><br/>
barracks_status_radiant: <?php echo $match->barracks_status_radiant; ?><br/>
barracks_status_dire: <?php echo $match->barracks_status_dire; ?><br/>
cluster: <?php echo $match->cluster; ?><br/>
first_blood_time: <?php echo $match->first_blood_time; ?><br/>
lobby_type: <?php echo $match->lobby_type; ?><br/>
human_players: <?php echo $match->human_players; ?><br/>
leagueid: <?php echo $match->leagueid; ?><br/>
positive_votes: <?php echo $match->positive_votes; ?><br/>
negative_votes: <?php echo $match->negative_votes; ?><br/>
