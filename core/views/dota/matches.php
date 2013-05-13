<div class="page-header">
    <h1>Dota 2
        <small>Matches</small>
    </h1>
</div>

<form id="match-search" class="search form-inline input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true" placeholder="Match ID">
    <button class="btn btn-primary" type="button">Search</button>
</form>

<p>
    Try <strong><a href="/dota/matches/37623177">37623177</a></strong>.
</p>

<?php if (!empty($this->live_matches)) : ?>
    <strong>Live league matches:</strong>
    <div class="well well-small">
        <?php
        echo '<ol>';
        foreach ($this->live_matches as $match) {
            echo '<li>';
            echo $match->radiant_team->team_name . ' vs ' . $match->dire_team->team_name;
            echo '</li>';
        }
        echo '</ol>';
        ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#match-search").submit(function (e) {
            window.location = ("/dota/matches/" + $('#query').val());
            return false;
        });
    });
</script>