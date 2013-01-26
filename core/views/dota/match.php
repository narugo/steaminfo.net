<div class="alert alert-error">In development.</div>

<?php $match = $this->match; ?>

<h3>Match #<?php echo $match->match_id; ?></h3>

Game mode: <?php echo $match->game_mode; ?>

<p>
    Start time: <?php echo date(DATE_RFC850, $match->starttime); ?>
    <br/>Duration:
    <?php
    $duration_min = floor($match->duration / 60);
    $duration_sec = $match->duration - $duration_min * 60;
    echo $duration_min . ':' . $duration_sec;
    ?>
    <br/>Season: <?php echo $match->season; ?>
</p>

<h4>
    <?php
    if ($match->radiant_win)
        echo "Radiant victory!";
    else
        echo "Dire victory!";
    ?>
</h4>

<table id="match-table" class="table table-condensed">
    <thead>
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
        <th class="gold_spent">Gold spent</th>
        <th class="hero_damage">Hero damage</th>
        <th class="tower_damage">Tower damage</th>
        <th class="hero_healing">Hero healing</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($match->players as $player) : ?>
        <tr>
            <th class="player">
                <?php
                if ($player->account_id == 4294967295) echo "<em>Private</em>";
                else echo $player->account_id;
                ?>
            </th>
            <th class="level"><?php echo $player->kills; ?></th>
            <th class="hero"><?php echo $player->hero_id; ?></th>
            <th class="kills"><?php echo $player->kills; ?></th>
            <th class="deaths"><?php echo $player->deaths; ?></th>
            <th class="assists"><?php echo $player->assists; ?></th>
            <th class="item_0"><?php echo $player->item_0; ?></th>
            <th class="item_1"><?php echo $player->item_1; ?></th>
            <th class="item_2"><?php echo $player->item_2; ?></th>
            <th class="item_3"><?php echo $player->item_3; ?></th>
            <th class="item_4"><?php echo $player->item_4; ?></th>
            <th class="item_5"><?php echo $player->item_5; ?></th>
            <th class="gold"><?php echo $player->gold; ?></th>
            <th class="last_hits"><?php echo $player->last_hits; ?></th>
            <th class="denies"><?php echo $player->denies; ?></th>
            <th class="gold_per_min"><?php echo $player->gold_per_min; ?></th>
            <th class="xp_per_min"><?php echo $player->xp_per_min; ?></th>
            <th class="gold_spent"><?php echo $player->gold_spent; ?></th>
            <th class="hero_damage"><?php echo $player->hero_damage; ?></th>
            <th class="tower_damage"><?php echo $player->tower_damage; ?></th>
            <th class="hero_healing"><?php echo $player->hero_healing; ?></th>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

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
