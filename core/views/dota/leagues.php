<div class="page-header">
    <h1>Dota 2
        <small>Leagues</small>
    </h1>
</div>

<ol>
    <?php
    /** @var \SteamInfo\Models\Entities\DotaLeague[] $leagues */
    $leagues = $this->leagues;
    foreach ($leagues as $league) {
        echo '<li>';
        echo '(#' . $league->getId() . ') ';
        if (!is_null($league->getTournamentUrl())) {
            echo '<a href="' . $league->getTournamentUrl() . '" rel="nofollow">' . $league->getName() . '</a>';
        } else {
            echo $league->getName();
        }
        echo '</li>';
    }
    ?>
</ol>