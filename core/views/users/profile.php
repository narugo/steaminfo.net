<?php
$profile = $this->profile;
?>
<div class="page-header">
    <h1>Users
        <small><?php echo $profile->getNickname(); ?></small>
        <img class="avatar" src="<?php echo $profile->getAvatarUrl(); ?>"/>
    </h1>
</div>

<ul id="navigation" class="nav nav-tabs">
    <li><a href="#info-tab" data-toggle="tab">Info</a></li>
    <li><a href="#apps-tab" data-toggle="tab">Apps</a></li>
    <li><a href="#groups-tab" data-toggle="tab">Groups</a></li>
    <li><a href="#friends-tab" data-toggle="tab">Friends</a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane" id="info-tab">
        <?php
        $real_name = $profile->getRealName();
        if (!empty($real_name))
            echo '<h3 id="real-name">' . $real_name . '</h3>';

        $tag = $profile->getTag();
        if (!empty($tag))
            echo '<p><span class="label label-important">' . $tag . '</span></p>';

        /**
         * Status
         */
        echo '<p style="clear:both;">';
        echo '<strong>' . $profile->getStatus() . '</strong>';
        $current_app_name = $profile->getCurrentAppName();
        if (!empty($current_app_name))
            echo '<strong>, in <a href="' . $profile->getCurrentAppStorePageURL() . '">' . $current_app_name . '</a></strong>';
        $current_server_ip = $profile->getCurrentGameServerIp();
        if (!empty($current_server_ip))
            echo '<br />Server: <a href="' . $profile->getConnectionUrl() . '" title="Join game">' . $current_server_ip . '</a>';
        $last_login_time = $profile->getLastLoginTime();
        if (!empty($last_login_time))
            echo '<br />Last log in: ' . $last_login_time;
        echo '</p>';

        /**
         * IDs
         */
        echo '<p><strong>Steam ID:</strong> ' . $profile->getSteamId();
        echo '<br /><strong>Community ID:</strong> ' . $profile->getCommunityId() . '</p>';

        // Profile creation time
        $creation_time = $profile->getCreationTime();
        if (!empty($creation_time)) echo '<p>Steam user since ' . $creation_time . '</p>';

        // Location
        $location = $profile->getLocation();
        if (!empty($location)) {
            echo '<p>Location: <img src="/assets/img/flags/' . $profile->getLocationCountryCode() . '.png" /> ' . $location . '</p>';
        }

        $primary_group_id = $profile->getPrimaryGroupId();
        if (!empty($primary_group_id))
            echo '<p>Primary group: <a href="/groups/' . $primary_group_id . '/">' . $primary_group_id . '</a></p>';

        /*
         * Bans info
         */
        echo '<p>';
        if (!$profile->is_vac_banned) {
            echo '<span class="label label-important">VAC banned</span>';
        } else {
            echo '<span class="label label-success">Not VAC banned</span>';
        }
        echo '&nbsp;';
        if (!$profile->is_community_banned) {
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

        <a href="http://steamcommunity.com/profiles/<?php echo $profile->getCommunityId(); ?>">View profile on Steam
            Community website</a>
    </div>

    <div class="tab-pane" id="apps-tab">
        <i class="icon-spinner icon-spin"></i>
        Getting list of apps. Please wait.
    </div>

    <div class="tab-pane" id="friends-tab">
        <i class="icon-spinner icon-spin"></i>
        Getting list of friends. Please wait.
    </div>

    <div class="tab-pane" id="groups-tab">
        <i class="icon-spinner icon-spin"></i>
        Getting list of groups. Please wait.
    </div>

</div>

<script type="text/javascript">
    var appsTabLoaded = false;
    var friendsTabLoaded = false;
    var groupsTabLoaded = false;

    $('a[data-toggle="tab"]').on('shown', function (e) {
        switch (e.target.hash) {
            case "#apps-tab":
                if (!appsTabLoaded) {
                    appsTabLoaded = true;
                    $("#apps-tab").load("/users/apps/<?php echo $profile->getCommunityId(); ?>");
                }
                break;
            case "#friends-tab":
                if (!friendsTabLoaded) {
                    friendsTabLoaded = true;
                    $("#friends-tab").load("/users/friends/<?php echo $profile->getCommunityId(); ?>");
                }
                break;
            case "#groups-tab":
                if (!groupsTabLoaded) {
                    groupsTabLoaded = true;
                    $("#groups-tab").load("/users/groups/<?php echo $profile->getCommunityId(); ?>");
                }
                break;
        }
    });

    $(document).ready(function () {
        // Activating first tab
        $('#navigation a:first').tab('show');
    });
</script>