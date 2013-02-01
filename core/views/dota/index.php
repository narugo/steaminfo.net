<h2 id="title">Dota 2</h2>

<div id="search" class="input-append">
    <label>Search matches</label>
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Match ID">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</div>

Try <strong><a href="/dota/match/37623177">37623177</a></strong>.

<hr/>

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

<?php if (!empty($this->league)) : ?>
    <strong>Leagues:</strong>
    <div class="well well-small">
        <?php
        echo '<ol>';
        foreach ($this->league as $league) {
            echo '<li>';
            if (!empty($league->tournament_url)) {
                echo '<a href="' . $league->tournament_url . '">' . $league->name . '</a>';
            } else {
                echo $league->name;
            }
            echo '</li>';
        }
        echo '</ol>';
        ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#search-submit').click(function () {
            window.location = ("/dota/match/" + $('#query').val());
        });
    });
</script>