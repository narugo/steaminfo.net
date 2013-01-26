<h2>Users</h2>

<div class="alert alert-error">In development.</div>

<div id="search" class="input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Vanity URL, Steam ID, or Community ID">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</div>

<div id="help">
    <h4>Examples</h4>
    <strong>Community ID:</strong> 76561197960435530
    <br/><strong>Steam ID:</strong> STEAM_0:0:84901 (or 0:0:84901)
    <br/><strong>Vanity URL:</strong> robinwalker
</div>

<div id="loading" style="display: none">
    <i class="icon-spinner icon-spin"></i>
    Searching. Please wait.
</div>

<div id="result" style="display: none">
    <div id="error">
        Not found!
    </div>
    <div id="profile-summary" style="display: none"></div>
</div>

<div class="well well-small pull-left">
    <strong>Most popular users today</strong>
    <ol>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/c5/c5d56249ee5d28a07db4ac9f7f60af961fab5426.jpg"/>
            <a href="/users/76561197960287930">Rabscuttle</a>
            <span class="label label-important">Valve Employee</span>
        </li>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/8c/8c8143c2053ea204a999584979c86503c6bd8e14.jpg"/>
            <a href="/users/76561197981595364">Auren</a>
        </li>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/22/222baca34903d5c313c09420e0fba2f0d29ddefd.jpg"/>
            <a href="/users/76561197960860649">Zoid</a>
            <span class="label label-important">Valve Employee</span>
        </li>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/77/77ce1bc8f9203269f327321b399812fb98719d5c.jpg"/>
            <a href="/users/76561197994938134">Dark Deer</a>
        </li>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/2f/2f511e5a6dde6e62f1548c80f64511b01bf394d6.jpg"/>
            <a href="/users/76561197960269668">Tim</a>
        </li>
        <li>
            <img
                src="http://media.steampowered.com/steamcommunity/public/images/avatars/b4/b424c95e4008b02074cb530b35a24256b7943af3.jpg"/>
            <a href="/users/76561198014254115">IceFrog</a>
            <span class="label label-important">Valve Employee</span>
        </li>
    </ol>
</div>

<div class="well well-small pull-right">
    <strong>Number of indexed users</strong>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {packages: ["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Year', 'Indexed users'],
                ['2008', 1976],
                ['2009', 2681],
                ['2010', 4124],
                ['2011', 6292],
                ['2012', 8374],
                ['2013', 10000]
            ]);

            var options = {
                isHtml: true,
                backgroundColor: '#f5f5f5',
                legend: 'none'
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
    <div id="chart_div"></div>
</div>

<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        var is_help_hidden = false;
        $('#search-submit').click(function () {
            if (!is_help_hidden) {
                $('#help').hide("fast");
                is_help_hidden = true;
            }
            $('#result').hide("fast");
            $('#loading').show("fast");
            $.getJSON('/users/search/?q=' + $('#query').val(), function (data) {
                $('#error').hide();
                $('#profile-summary').hide();
                if (data == false) {
                    $('#error').show();
                } else {
                    // TODO: Check what kind of data has been received (array of search results or profile summary)
                    $('#profile-summary').empty();
                    jQuery('<img/>', {
                        class: 'avatar',
                        src: data['avatar_url']
                    }).appendTo('#profile-summary');
                    $('#profile-summary').append('<strong>' + data['nickname'] + '</strong> ').show();
                    if (data['tag'] != null) {
                        jQuery('<span/>', {
                            id: 'badge',
                            class: 'label label-important'
                        }).appendTo('#profile-summary');
                        $('#badge').append(data['tag']).show();
                    }
                    jQuery('<br>').appendTo('#profile-summary');
                    switch (data['status']) {
                        case '1':
                            $('#profile-summary').append('Online');
                            break;
                        case '2':
                            $('#profile-summary').append('Busy');
                            break;
                        case '3':
                            $('#profile-summary').append('Away');
                            break;
                        case '4':
                            $('#profile-summary').append('Snooze');
                            break;
                        case '5':
                            $('#profile-summary').append('Looking to trade');
                            break;
                        case '6':
                            $('#profile-summary').append('Looking to play');
                            break;
                        case '0':
                        default:
                            $('#profile-summary').append('Offline');
                            break;
                    }
                    if (data['current_game_id'] != null) {
                        $('#profile-summary').append(', in ' + data['current_game_name']).show();
                    }
                    jQuery('<br>').appendTo('#profile-summary');
                    jQuery('<a/>', {
                        href: '/users/' + data['community_id'],
                        id: 'more-link'
                    }).appendTo('#profile-summary');
                    $('#more-link').append('View more info about this user...').show();
                    $('#profile-summary').show();
                }
                $('#loading').hide("fast");
                $('#result').show("fast");
            });
            return false;
        });
    });
</script>