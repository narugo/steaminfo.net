<div class="page-header">
    <h1>Dota 2
        <small>Leagues</small>
    </h1>
</div>

<?php
echo '<ol>';
foreach ($this->league as $league) {
    echo '<li>';
    echo '(#' . $league->id . ') ';
    if (!empty($league->tournament_url)) {
        echo '<a href="' . $league->tournament_url . '">' . $league->name . '</a>';
    } else {
        echo $league->name;
    }
    echo '</li>';
}
echo '</ol>';
?>