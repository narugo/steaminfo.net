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

            echo '<p style="clear:both;">';
            echo '<strong>' . $profile->getStatus() . '</strong>';

            if (! empty($profile->current_game_name))
                echo '<strong>, playing <a href="http://store.steampowered.com/app/'.$profile->current_game_id.'">'.$profile->current_game_name.'</a></strong>';
            if (! empty($profile->current_game_server_ip))
                echo '<br />Server: <a href="steam://connect/'.$profile->current_game_server_ip.'" title="Join game">'.$profile->current_game_server_ip.'</a>';
            if (! empty($profile->last_time_online))
                echo '<br />Last log in: '.date(DATE_RFC850, $profile->last_time_online);
            echo '</p>';

            /**
             * IDs
             */
            echo '<p><strong>Steam ID:</strong> ' . $profile->getSteamId() . '<br /><strong>Community ID:</strong> ' . $profile->getCommunityId() . '</p>';

            // Profile creation time
            $creation_time = $profile->getCreationTime();
            if (! empty($creation_time)) echo 'Steam user since ' . $creation_time;

            // Location
            $location = $profile->getLocation();
            if (! empty($location)) echo '<br />Location: ' . $location;

            $primary_group_id = $profile->getPrimaryGroupId();
            if (! empty($primary_group_id))
                echo '<br />Primary group ID: ' . $primary_group_id;

            /*
             * Bans info
             */
            echo '<p><h4>Bans info:</h4>';
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
            // TODO: Fix badges
            // Badges
            //$badges_html = $users->getBadges($profile->community_id);
            if (! empty($badges_html)) {
                echo '<div id="badges">'.$badges_html.'</div>';
            } ?>
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-7607595783444002";
            /* 125x125, создано 16.09.10 */
            google_ad_slot = "7977631930";
            google_ad_width = 125;
            google_ad_height = 125;
            //-->
            </script>
            <script type="text/javascript"
                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
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