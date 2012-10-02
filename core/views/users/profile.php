<?php
include "includes/search.php";

$profile = $this->profile;
$loading_img = '<img class="loading" src="/img/loading.gif" />';
?>

<h2 id="name">
    <img id="avatar" src="<?php echo $profile->avatar_url; ?>" /><?php echo $profile->nickname; ?></a>
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
            // Real name and tag
            if (! empty($profile->real_name))
            {
                echo '<h3 id="real-name">'.$profile->real_name.'</h3>';
                if (! empty($profile->tag))
                    echo '<span class="badge badge-info">'.$profile->tag.'</span>';
            }
            else
            {
                if (! empty($profile->tag))
                    echo '<span class="badge badge-info">'.$profile->tag.'</span>';
            }

            /*
            * Current status
            */
            echo '<p style="clear:both;">';
            if (strlen($profile->status) > 0)
            {
                echo '<strong>';
                switch ($profile->status) {
                    case '0': echo 'Offline'; break;
                    case '1': echo 'Online'; break;
                    case '2': echo 'Busy'; break;
                    case '3': echo 'Away'; break;
                    case '4': echo 'Snooze'; break;
                    case '5': echo 'Looking to trade'; break;
                    case '5': echo 'Looking to play'; break;
                }
                echo '</strong>';
            }
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
            if (! empty($profile->community_id))
            {
                echo '<p>
                <strong>Steam ID:</strong> './/$users->convertToSteamID($profile->community_id).
                    '<br /><strong>Community ID:</strong> '.$profile->community_id.
                    '</p>';
            }

            // Profile creation time
            if (! empty($profile->time_created))
                echo 'Steam user since '.date(DATE_RFC850, $profile->time_created);

            // Location
            if (! empty($profile->loc_country_code)) {
                echo '<br />Location: '.$profile->loc_country_code;
                if (! empty($profile->loc_state_code)) echo ', '.$profile->loc_state_code;
                if (! empty($profile->loc_city_id)) echo ', '.$profile->loc_city_id;
            }

            if (! empty($profile->primary_group_id))
                echo '<br />Primary group ID: '.$profile->primary_group_id;

            /*
            * Bans info
            */
            echo '<p><h4>Bans info:</h4>';
            // VAC ban
            switch ($profile->is_vac_banned)
            {
                case '0': echo '<span class="label label-success">Not VAC banned</span>'; break;
                case '1': echo '<span class="label label-important">VAC banned</span>'; break;
                default: echo '<span class="label">VAC ban state is unknown</span>';
            }
            // Account limitations
            echo '&nbsp;';
            switch ($profile->is_limited_account)
            {
                case '0': echo '<span class="label label-success">Account is not limited</span>'; break;
                case '1': echo '<span class="label label-important">Account is limited</span>'; break;
                default: echo '<span class="label">Account limitations info is unknown</span>';
            }
            // Trade ban
            echo '<br /><b>Trade ban status:</b> ';
            if (! empty($profile->trade_ban_state))
            {
                switch ($profile->trade_ban_state)
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
                echo $profile->trade_ban_state;
                echo '</span>';
            }
            else
                echo '<span class="label">Unknown</span>';
            echo '</p>';
            ?>
        </div>
        <div id="sidebar">
            <?php
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
        <a href="http://steamcommunity.com/profile/<?php echo $profile->community_id; ?>">View profile on Steam Community website</a>
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
                    $("#apps-tab").load("/users/apps/<?php echo $profile->community_id; ?>");
                }
                break;
            case "#friends-tab":
                if (!friendsTabLoaded) {
                    friendsTabLoaded = true;
                    $("#friends-tab").load("/users/friends/<?php echo $profile->community_id; ?>");
                }
                break;
            case "#groups-tab":
                if (!groupsTabLoaded) {
                    groupsTabLoaded = true;
                    $("#groups-tab").load("/users/groups/<?php echo $profile->community_id; ?>");
                }
                break;
        }
    });

    // Activating last (actually first) tab
    $(function() {
        $('#navigation a:last').tab('show');
    });
</script>