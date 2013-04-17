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
        echo '<a href="/users/profile/' . $player->account_id . '">';
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
    echo '<img src="http://media.steampowered.com/apps/dota2/images/heroes/' . substr($player->hero_name, 14) . '_sb.png"/> ';
    if (empty($player->hero_display_name)) {
        echo $player->hero_name;
    } else {
        echo $player->hero_display_name;
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

function printTime($seconds)
{
    $duration_min = floor($seconds / 60);
    $duration_sec = $seconds - $duration_min * 60;
    echo $duration_min . ':' . $duration_sec;
}
?>

<div class="page-header">
    <h1>Dota 2
        <small>Match #<?php echo $match->id; ?></small>
    </h1>
</div>

Game mode: <?php echo $match->game_mode; ?>

<p>
    Start time: <?php echo date(DATE_RFC850, $match->start_time); ?>
    <br/>Duration: <?php printTime($match->duration); ?>
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

<span class="team">
    <?php
    if (!empty($match->radiant_logo)) {
        echo '<img class="team-logo" src="/assets/img/dota/' . $match->radiant_logo . '"/>';
    } else {
        echo '<img class="team-icon" src="/assets/img/dota/pip_radiant.png"/>';
    }
    if (!empty($match->radiant_name)) {
        echo '<a href="/dota/teams/' . $match->radiant_team_id . '/">' . $match->radiant_name . '</a> (The Radiant)';
    } else {
        echo 'The Radiant';
    }
    ?>
</span>
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

<span class="team">
    <?php
    if (!empty($match->dire_logo)) {
        echo '<img class="team-logo" src="/assets/img/dota/' . $match->dire_logo . '"/>';
    } else {
        echo '<img class="team-icon" src="/assets/img/dota/pip_dire.png"/>';
    }
    if (!empty($match->dire_name)) {
        echo '<a href="/dota/teams/' . $match->dire_team_id . '/">' . $match->dire_name . '</a> (The Dire)';
    } else {
        echo 'The Dire';
    }
    ?>
</span>
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

<p>
    First blood time: <?php printTime($match->first_blood_time); ?>
    <br/>Human players: <?php echo $match->human_players; ?>
    <br/>Lobby type: <?php echo $match->lobby_type; ?>
    <br/>League: <?php echo $match->league_id; ?>
    <br/>Cluster: <?php echo $match->cluster; ?>
</p>

<p>
    Positive votes: <?php echo $match->positive_votes; ?>
    <br/>Negative votes: <?php echo $match->negative_votes; ?>
</p>
<p>
    tower_status_radiant: <?php echo $match->tower_status_radiant; ?>
    <br/>tower_status_dire: <?php echo $match->tower_status_dire; ?>
    <br/>barracks_status_radiant: <?php echo $match->barracks_status_radiant; ?>
    <br/>barracks_status_dire: <?php echo $match->barracks_status_dire; ?>
</p>
<p>
    <br/>radiant_team_complete: <?php echo $match->radiant_team_complete; ?>
    <br/>dire_team_complete: <?php echo $match->dire_team_complete; ?>
</p>