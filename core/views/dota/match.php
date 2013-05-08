<?php


/** @var \SteamInfo\Models\Entities\DotaMatch $match */
$match = $this->match;

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
        <small>Match #<?php echo $match->getId(); ?></small>
    </h1>
</div>

Game mode: <?php echo $match->getGameMode(); ?>

<p>
    Start time: <?php echo $match->getStartTime()->format(DATE_RFC850); ?>
    <br/>Duration: <?php printTime($match->getDuration()); ?>
    <br/>Season: <?php echo $match->getSeason(); ?>
</p>

<h4 id="winner">
    <?php
    if ($match->getIsRadiantWin())
        echo "Radiant victory!";
    else
        echo "Dire victory!";
    ?>
</h4>

<span class="team">
    <?php
    if (!is_null($match->getRadiantTeam())) {
        echo '<img class="team-logo" src="/static/img/dota/' . $match->getRadiantTeam()->getLogo() . '"/>';
        echo '<a href="/dota/teams/' . $match->getRadiantTeam()->getId() . '/">' . $match->getRadiantTeam()->getName() . '</a> (The Radiant)';
    } else {
        echo '<img class="team-icon" src="/static/img/dota/pip_radiant.png"/> The Radiant';
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
     if (!is_null($match->getDireTeam())) {
         echo '<img class="team-logo" src="/static/img/dota/' . $match->getDireTeam()->getLogo() . '"/>';
         echo '<a href="/dota/teams/' . $match->getDireTeam()->getId() . '/">' . $match->getDireTeam()->getName() . '</a> (The Dire)';
     } else {
         echo '<img class="team-icon" src="/static/img/dota/pip_dire.png"/> The Dire';
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
    First blood time: <?php printTime($match->getFirstBloodTime()); ?>
    <br/>Human players: <?php echo $match->getHumanPlayers(); ?>
    <br/>Lobby type: <?php echo $match->getLobbyType(); ?>
    <?php if (!is_null($match->getLeague())) : ?>
        <br/>League: <?php echo $match->getLeague()->getName() . ' (' . $match->getLeague()->getId() . ')'; ?>
    <?php endif; ?>
    <br/>Cluster: <?php echo $match->getCluster(); ?>
</p>

<p>
    Positive votes: <?php echo $match->getPositiveVotes(); ?>
    <br/>Negative votes: <?php echo $match->getNegativeVotes(); ?>
</p>
<p>
    Radiant tower status: <?php echo $match->getRadiantTowerStatus(); ?>
    <br/>Dire tower status: <?php echo $match->getDireTowerStatus(); ?>
    <br/>Radiant barracks status: <?php echo $match->getRadiantBarracksStatus(); ?>
    <br/>Dire barracks status: <?php echo $match->getDireBarracksStatus(); ?>
</p>
<p>
    <?php if (!is_null($match->getRadiantTeamComplete())) : ?>
        <br/>Is Radiant team complete:
        <?php
        if ($match->getRadiantTeamComplete()) echo "YES";
        else echo "NO";
        ?>
    <?php endif; ?>
    <?php if (!is_null($match->getDireTeamComplete())) : ?>
        <br/>Is Dire team complete:
        <?php
        if ($match->getDireTeamComplete()) echo "YES";
        else echo "NO";
        ?>
    <?php endif; ?>
</p>