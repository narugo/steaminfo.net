<?php
$profile = $this->profile;
?>
<div class="page-header">
    <h1>Users
        <small><?php echo $profile->nickname; ?></small>
        <img class="avatar" src="<?php echo $profile->avatar_url; ?>"/>
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
        if (!empty($profile->real_name))
            echo '<h3 id="real-name">' . $profile->real_name . '</h3>';

        if (!empty($profile->tag))
            echo '<p><span class="label label-important">' . $profile->tag . '</span></p>';

        /**
         * Status
         */
        echo '<p>';
        echo '<strong>';
        switch ($profile->status) {
            case '1':
                echo 'Online';
                break;
            case '2':
                echo 'Busy';
                break;
            case '3':
                echo 'Away';
                break;
            case '4':
                echo 'Snooze';
                break;
            case '5':
                echo 'Looking to trade';
                break;
            case '6':
                echo 'Looking to play';
                break;
            case '0':
            default:
                echo 'Offline';
                break;
        }
        echo '</strong>';
        if (isset($profile->current_game_id)) {
            $app_page = '/apps/' . $profile->current_game_id;
            if (!empty($profile->current_app_name)) {
                echo '<strong>, in <a href="' . $app_page . '">' . $profile->current_app_name . '</a></strong>';
            } else {
                echo '<strong>, in app #<a href="' . $app_page . '">' . $profile->current_game_id . '</a></strong>';
            }
            if (!empty($profile->current_server_ip)) {
                $connection_url = 'steam://connect/' . $profile->current_server_ip;
                echo '<br />Server: <a href="' . $connection_url . '">' . $profile->current_server_ip . '</a>';
            }
        }
        if (!empty($profile->last_login_time)) {
            echo '<br />Last log in: ' . date(DATE_RFC850, $profile->last_login_time);
        }
        echo '</p>';

        $steam = new Locomotive(STEAM_API_KEY);
        $steam_id = $steam->tools->user->communityIdToSteamId($profile->community_id);
        echo '<p><strong>Steam ID:</strong> ' . $steam_id;
        echo '<br /><strong>Community ID:</strong> ' . $profile->community_id . '</p>';

        if (!empty($profile->creation_time)) {
            echo '<p>Steam user since ' . $profile->creation_time . '</p>';
        }

        if (!empty($profile->location_country_code)) {
            echo '<p>Location: <img src="/assets/img/flags/' . strtoupper($profile->location_country_code) . '.png" /> ';
            echo $profile->location_country_code;
            if (isset($this->location_state_code)) echo ', ' . $profile->location_state_code;
            if (isset($this->location_city_id)) echo ', ' . $profile->location_city_id;
            echo '</p>';
        }

        if (!empty($profile->primary_group_id)) {
            echo '<p>Primary group: ' . $profile->primary_group_id . '</p>';
        }

        /*
         * Bans info
         */
        echo '<p>';
        if ($profile->is_vac_banned) {
            echo '<span class="label label-important">VAC banned</span>';
        } else {
            echo '<span class="label label-success">Not VAC banned</span>';
        }
        echo '&nbsp;';
        if ($profile->is_community_banned) {
            echo '<span class="label label-important">Account is limited</span>';
        } else {
            echo '<span class="label label-success">Account is not limited</span>';
        }
        echo '<br /><b>Trade ban state:</b> ';
        switch ($profile->economy_ban_state) {
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
                echo '<span class="label">Unknown (' . $profile->economy_ban_state . ')</span>';
        }
        echo '</p>';

        ?>

        <a href="http://steamcommunity.com/profiles/<?php echo $profile->community_id; ?>">
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
                    $("#apps-tab").load("/users/apps/<?php echo $profile->community_id; ?>");
                }
                break;
            case "#friends-tab":
                if (!friendsTabLoaded) {
                    friendsTabLoaded = true;
                    $("#friends-tab").load("/users/friends/<?php echo $profile->community_id; ?>");
                }
                break;
        }
    });

    $(document).ready(function () {
        // Activating first tab
        $('#navigation a:first').tab('show');
    });
</script>