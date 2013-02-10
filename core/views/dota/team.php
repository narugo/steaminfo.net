<?php $team = $this->team; ?>

<div class="page-header">
    <h1>Dota 2
        <small>Team "<strong><?php echo $team->tag; ?></strong>"</small>
    </h1>
</div>

<div>

    <img class="pull-right" src="/assets/img/dota/<?php echo $team->logo; ?>"/>

    <p>
        <strong>ID</strong>: <?php echo $team->id; ?>
        <br/><strong>Name</strong>: <?php echo $team->name; ?>
        <br/><strong>Tag</strong>: <?php echo $team->tag; ?>
        <br/><strong>Creation time</strong>: <?php echo date(DATE_RFC850, $team->creation_time); ?>
        <br/><strong>Rating</strong>: <?php echo $team->rating; ?>

        <?php if (!empty($team->country_code)) {
            $team->country_code = strtoupper($team->country_code); ?>
            <br/><strong>Country</strong>:
            <img src="/assets/img/flags/<?php echo $team->country_code; ?>.png"/>
            <?php echo $team->country_code; ?>
        <?php } ?>

        <br/><strong>URL</strong>: <?php echo $team->url; ?>
        <br/><strong>Games played with current roster</strong>: <?php echo $team->games_played_with_current_roster; ?>
        <br/><strong>logo_sponsor</strong>: <?php echo $team->logo_sponsor; ?>
    </p>

    <div class="well"><?php var_dump($this->team); ?></div>

</div>