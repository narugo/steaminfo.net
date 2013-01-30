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
    <?php var_dump($this->live_matches); ?>
</div>
<?php endif; ?>

<strong>Leagues:</strong>
<div class="well well-small">
    <?php
    if (empty($this->league)) {
        echo 'No leagues.';
    } else {
        echo '<ol>';
        foreach ($this->league as $league) {
            echo '<li><a href="' . $league->tournament_url . '">' . $league->name . '</a></li>';
        }
        echo '</ol>';
    }
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#search-submit').click(function () {
            window.location = ("/dota/match/" + $('#query').val());
        });
    });
</script>