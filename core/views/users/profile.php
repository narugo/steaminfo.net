<?php
include "includes/search.php";

$profile = $this->profile;
$loading_img = '<img class="loading" src="/img/loading.gif" />';
?>

<h2 id="name">
    <img id="avatar" src="<?php echo $profile->getAvatarUrl(); ?>" /><?php echo $profile->getNickname(); ?></a>
</h2>

<ul class="nav nav-tabs" id="navigation">
    <li><a href="#groups-tab" data-toggle="tab">Groups</a></li>
    <li><a href="#friends-tab" data-toggle="tab">Friends</a></li>
    <li><a href="#apps-tab" data-toggle="tab">Apps</a></li>
    <li><a href="#info-tab" data-toggle="tab">Info</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane" id="info-tab">
        <div id="main">
            <?php
            $real_name = $profile->getRealName();
            if (! empty($real_name))
                echo '<h3 id="real-name">' . $real_name . '</h3>';

            $tag = $profile->getTag();
            if (! empty($tag))
                echo '<span class="badge badge-info">' . $tag . '</span>';

            /**
             * Status
             */
            echo '<p style="clear:both;">';
            echo '<strong>' . $profile->getStatus() . '</strong>';
            $current_app_name = $profile->getCurrentAppName();
            if (! empty($current_app_name))
                echo '<strong>, in <a href="' .$profile->getCurrentAppStorePageURL() . '">' . $current_app_name . '</a></strong>';
            $current_server_ip = $profile->getCurrentGameServerIp();
            if (! empty($current_server_ip))
                echo '<br />Server: <a href="' . $profile->getConnectionUrl() . '" title="Join game">' . $current_server_ip . '</a>';
            $last_login_time = $profile->getLastLoginTime();
            if (! empty($last_login_time))
                echo '<br />Last log in: ' . $last_login_time;
            echo '</p>';

            /**
             * IDs
             */
            echo '<p><strong>Steam ID:</strong> ' . $profile->getSteamId();
            echo '<br /><strong>Community ID:</strong> ' . $profile->getCommunityId() . '</p>';

            // Profile creation time
            $creation_time = $profile->getCreationTime();
            if (! empty($creation_time)) echo '<p>Steam user since ' . $creation_time . '</p>';

            // Location
            $location = $profile->getLocation();
            if (! empty($location)) echo '<p>Location: ' . $location . '</p>';

            $primary_group_id = $profile->getPrimaryGroupId();
            if (! empty($primary_group_id))
                echo '<p>Primary group ID: ' . $primary_group_id . '</p>';

            /*
             * Bans info
             */
            echo '<p>';
            // VAC ban
            switch ($profile->isVacBanned())
            {
                case '0': echo '<span class="label label-success">Not VAC banned</span>'; break;
                case '1': echo '<span class="label label-important">VAC banned</span>'; break;
                default: echo '<span class="label">VAC ban state is unknown</span>';
            }
            // Account limitations
            echo '&nbsp;';
            switch ($profile->isInGame())
            {
                case '0': echo '<span class="label label-success">Account is not limited</span>'; break;
                case '1': echo '<span class="label label-important">Account is limited</span>'; break;
                default: echo '<span class="label">Account limitations info is unknown</span>';
            }
            // Trade ban
            echo '<br /><b>Trade ban state:</b> ';
            switch ($profile->getTradeBanState())
            {
                case 'None':
                    echo '<span class="label label-success">';
                    break;
                case 'Banned':
                    echo '<span class="label label-important">';
                    break;
                case 'Probation':
                    echo '<span class="label label-warning">';
                    break;
                default:
                    echo '<span class="label">';
            }
            echo $profile->getTradeBanState();
            echo '</span>';
            echo '</p>';

            ?>
        </div>

        <div id="sidebar">
            <?php
            // Badges
            $badges_html = $profile->getBadgesHTML();
            if (! empty($badges_html)) {
                echo '<div id="badges">'.$badges_html.'</div>';
            } ?>
        </div>

        <hr style="clear:both;" />
        <a href="http://steamcommunity.com/profiles/<?php echo $profile->getCommunityId(); ?>">View profile on Steam Community website</a>
    </div>
    <div class="tab-pane" id="apps-tab"><?php echo $loading_img; ?></div>
    <div class="tab-pane" id="friends-tab"><?php echo $loading_img; ?></div>
    <div class="tab-pane" id="groups-tab"><?php echo $loading_img; ?></div>
</div>

<script type="text/javascript">
    var appsTabLoaded = false;
    var friendsTabLoaded = false;
    var groupsTabLoaded = false;
    $('a[data-toggle="tab"]').on('shown', function (e) {
        switch(e.target.hash) {
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

    // Activating last (actually first) tab
    $(function() {
        $('#navigation a:last').tab('show');
    });
</script>