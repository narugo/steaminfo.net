<div id="submenu">
    <h1 id="title">Users</h1>
    <form id="search">
        <input type="text" id="query" autocomplete="off" autofocus="true" placeholder="Vanity URL, Steam ID, or Community ID" />
        <input type="submit" class="btn" value="Go" />
    </form>
</div>
<div id="content">

    <div id="help">
        <p><strong>Here you can find Steam users by their Community ID, Steam ID, or Vanity URL.</strong></p>
        <h2>Examples</h2>
        <p>
            <b>Community ID:</b> 76561197968575517
            <br /><b>Steam ID:</b> STEAM_0:1:4154894 (or simply 0:1:4154894)
            <br /><b>Vanity URL:</b> ChetFaliszek
        </p>
        <p>
            You can get Community ID form URL to user's page on Steam Community website. It looks something like this:
            <em>http://steamcommunity.com/profiles/<strong title="You need to input this part of the URL">76561197968575517</strong>/</em><br />
            If you are looking for a user who already created himself so called vanity URL, you can use it too. Here's how it
            looks like: <em>http://steamcommunity.com/id/<strong title="You need to input this part of the URL">ChetFaliszek</strong>/</em>
        </p>
    </div>

    <div id="loading" style="display: none">
        Searching. Please wait.

    </div>

    <div id="result" style="display: none">
        <div id="error"><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/m04cuu-bfQw?rel=0" frameborder="0" allowfullscreen></iframe></div>
        <div id="profile-summary" style="display: none"></div>
    </div>

</div>

<script type="text/javascript">
    var is_help_hidden = false;
    $('#search').submit(function() {
        if (!is_help_hidden) {
            $('#help').hide("fast");
            is_help_hidden = true;
        }
        $('#result').hide("fast");
        $('#loading').show("fast");
        $.getJSON('/users/search/?q=' + $('#query').val(), function(data) {
            $('#error').hide();
            $('#profile-summary').hide();
            if (data == false) {
                $('#error').show();
            } else {
                // TODO: Check what data has been received (array of search results or profile summary)
                $('#profile-summary').empty();
                jQuery('<img/>', {
                    id: 'avatar',
                    src: data['avatar_url']
                }).appendTo('#profile-summary');
                $('#profile-summary').append(data['nickname'] + ' ').show();
                if (data['tag'] != null) {
                    jQuery('<span/>', {
                        id: 'badge',
                        class: 'badge badge-info'
                    }).appendTo('#profile-summary');
                    $('#badge').append(data['tag']).show();
                }
                jQuery('<br>').appendTo('#profile-summary');
                switch (data['status']) {
                    case '1': $('#profile-summary').append('Online'); break;
                    case '2': $('#profile-summary').append('Busy'); break;
                    case '3': $('#profile-summary').append('Away'); break;
                    case '4': $('#profile-summary').append('Snooze'); break;
                    case '5': $('#profile-summary').append('Looking to trade'); break;
                    case '6': $('#profile-summary').append('Looking to play'); break;
                    case '0':
                    default:
                        $('#profile-summary').append('Offline'); break;
                }
                jQuery('<br>').appendTo('#profile-summary');
                jQuery('<a/>', {
                    href: '/users/profile/' + data['community_id'],
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

    // Loading animation
    var opts = {
        lines: 11, // The number of lines to draw
        length: 0, // The length of each line
        width: 4, // The line thickness
        radius: 10, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        color: '#fff', // #rgb or #rrggbb
        speed: 1.8, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'loading-animation', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: 'auto', // Top position relative to parent in px
        left: 'auto' // Left position relative to parent in px
    };
    var spinner = new Spinner(opts).spin();
    document.getElementById('loading').appendChild(spinner.el);
</script>