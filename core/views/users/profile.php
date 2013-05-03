<?php
/** @var SteamInfo\Models\Entities\User $profile */
$profile = $this->user;
?>
<div class="page-header">
    <h1>Users
        <small><?php echo $profile->getNickname(); ?></small>
        <img class="avatar" src="<?php echo $profile->getAvatarUrl(); ?>"/>
    </h1>
</div>

<ul id="navigation" class="nav nav-tabs">
    <li><a href="#summary-tab" data-toggle="tab">Summary</a></li>
    <li><a href="#apps-tab" data-toggle="tab">Apps</a></li>
    <li><a href="#friends-tab" data-toggle="tab">Friends</a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane" id="summary-tab">
        <?php
        if (!is_null($profile->getRealName()))
            echo '<h3 id="real-name">' . $profile->getRealName() . '</h3>';

        if (!is_null($profile->getTag()))
            echo '<p><span class="label label-important">' . $profile->getTag() . '</span></p>';

        /**
         * Status
         */
        echo '<p>';
        echo '<strong>' . $profile->getStatus() . '</strong>';
        if (!is_null($profile->getCurrentAppId())) {
            $app_page = '/apps/' . $profile->getCurrentAppId();
            if (!is_null($profile->getCurrentAppName())) {
                echo '<strong>, in <a href="' . $app_page . '">' . $profile->getCurrentAppName() . '</a></strong>';
            } else {
                echo '<strong>, in app #<a href="' . $app_page . '">' . $profile->getCurrentAppId() . '</a></strong>';
            }
            if (!is_null($profile->getCurrentServerIp())) {
                $connection_url = 'steam://connect/' . $profile->getCurrentServerIp();
                echo '<br />Server: <a href="' . $connection_url . '">' . $profile->getCurrentServerIp() . '</a>';
            }
        }
        if (!is_null($profile->getLastLoginTime())) {
            echo '<br />Last log in: ' . $profile->getLastLoginTime()->format(DATE_RFC850);
        }
        echo '</p>';

        $steam = new Locomotive(STEAM_API_KEY);
        $steam_id = $steam->tools->user->communityIdToSteamId($profile->getId());
        echo '<p><strong>Steam ID:</strong> ' . $steam_id;
        echo '<br /><strong>Community ID:</strong> ' . $profile->getId() . '</p>';

        if (!is_null($profile->getCreationTime())) {
            echo '<p>Steam user since ' . $profile->getCreationTime()->format(DATE_RFC850) . '</p>';
        }

        if (!is_null($profile->getLocationCountryCode())) {
            echo '<p>Location: <img src="/assets/img/flags/' . strtoupper($profile->getLocationCountryCode()) . '.png" /> ';
            echo $profile->getLocationCountryCode();
            if (isset($this->location_state_code)) echo ', ' . $profile->getLocationStateCode();
            if (isset($this->location_city_id)) echo ', ' . $profile->getLocationCityId();
            echo '</p>';
        }

        if (!is_null($profile->getPrimaryGroupId())) {
            echo '<p>Primary group: ' . $profile->getPrimaryGroupId() . '</p>';
        }

        /*
         * Bans info
         */
        echo '<p>';
        if ($profile->getIsVacBanned()) {
            echo '<span class="label label-important">VAC banned</span>';
        } else {
            echo '<span class="label label-success">Not VAC banned</span>';
        }
        echo '&nbsp;';
        if ($profile->getIsCommunityBanned()) {
            echo '<span class="label label-important">Account is limited</span>';
        } else {
            echo '<span class="label label-success">Account is not limited</span>';
        }
        echo '<br /><b>Trade ban state:</b> ';
        switch ($profile->getEconomyBanState()) {
            case 'none':
                echo '<span class="label label-success">None</span>';
                break;
            case 'banned':
                echo '<span class="label label-important">Banned</span>';
                break;
            case 'probation':
                echo '<span class="label label-warning">Probation</span>';
                break;
            default:
                echo '<span class="label">Unknown (' . $profile->getEconomyBanState() . ')</span>';
        }
        echo '</p>';

        ?>

        <a href="http://steamcommunity.com/profiles/<?php echo $profile->getId(); ?>">
            View profile on Steam Community website
        </a>
    </div>

    <div class="tab-pane" id="apps-tab">
        <i class="icon-spinner icon-spin"></i>
        Getting list of apps. Please wait.
    </div>

    <div class="tab-pane" id="friends-tab">
        <i class="icon-spinner icon-spin"></i>
        Getting list of friends. Please wait.
    </div>

</div>

<script type="text/javascript">
    var appsTabLoaded = false;
    var friendsTabLoaded = false;

    $('a[data-toggle="tab"]').on('shown', function (e) {
        switch (e.target.hash) {
            case "#apps-tab":
                if (!appsTabLoaded) {
                    appsTabLoaded = true;
                    $("#apps-tab").load("/users/apps/<?php echo $profile->getId(); ?>");
                }
                break;
            case "#friends-tab":
                if (!friendsTabLoaded) {
                    friendsTabLoaded = true;
                    $("#friends-tab").load("/users/friends/<?php echo $profile->getId(); ?>");
                }
                break;
        }
    });

    $(document).ready(function () {
        // Activating first tab
        $('#navigation a:first').tab('show');
    });
</script>