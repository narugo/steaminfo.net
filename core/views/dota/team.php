<?php
/**
 * @param \SteamInfo\Models\Entities\User $player
 */
function printPlayer($player)
{
    echo "<li>";
    echo '<a href="/users/profile/' . $player->getId() . '">' . $player->getNickname() . '</a>';
    echo "</li>";
}

/** @var \SteamInfo\Models\Entities\DotaTeam $team */
$team = $this->team;
?>

<div class="page-header">
    <h1>Dota 2
        <small>Team "<strong><?php echo $team->getName(); ?></strong>"</small>
    </h1>
</div>

<div>

    <?php if (!is_null($team->getLogo())) : ?>
        <img class="pull-right" src="/static/img/dota/<?php echo $team->getLogo(); ?>"/>
    <?php endif; ?>

    <p>
        <strong>ID</strong>: <?php echo $team->getId(); ?>
        <br/><strong>Name</strong>: <?php echo $team->getName(); ?>
        <br/><strong>Tag</strong>: <?php echo $team->getTag(); ?>
        <br/><strong>Creation time</strong>: <?php echo $team->getCreationTime()->format(DATE_RFC850); ?>
        <br/><strong>Rating</strong>: <?php echo $team->getRating(); ?>

        <?php if (!is_null($team->getCountryCode())) : ?>
            <br/><strong>Country</strong>:
            <img src="/static/img/flags/<?php echo $team->getCountryCode(); ?>.png"/>
            <?php echo $team->getCountryCode(); ?>
        <?php endif; ?>

        <?php if (!is_null($team->getUrl())) : ?>
            <br/><strong>URL</strong>: <a href="<?php echo $team->getUrl(); ?>" rel="nofollow"><?php echo $team->getUrl(); ?></a>
        <?php endif; ?>

        <br/><strong>Games played with current roster</strong>: <?php echo $team->getGamesPlayedWithCurrentRoster(); ?>

        <?php if (!is_null($team->getLogoSponsor())) : ?>
            <br/><strong>Logo sponsor</strong>: <?php echo $team->getLogoSponsor(); ?>
        <?php endif; ?>
    </p>

    <h3>Players</h3>
    <ol>
        <?php
        if (!is_null($team->getPlayer0())) {
            printPlayer($team->getPlayer0());
            if (!is_null($team->getPlayer1())) {
                printPlayer($team->getPlayer1());
                if (!is_null($team->getPlayer2())) {
                    printPlayer($team->getPlayer2());
                    if (!is_null($team->getPlayer3())) {
                        printPlayer($team->getPlayer3());
                        if (!is_null($team->getPlayer4())) {
                            printPlayer($team->getPlayer4());
                        }
                    }
                }
            }
        }
        ?>
    </ol>

</div>