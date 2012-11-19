<div class="container">

    <ul class="breadcrumb">
        <li><a href="/users/">Users</a> <span class="divider">/</span></li>
        <li><a href="/groups/">Groups</a> <span class="divider">/</span></li>
        <li><a href="/stats/">Stats</a> <span class="divider">/</span></li>
        <li><a href="/about/">About</a></li>
    </ul>

    <div id="submenu">
        <h1 id="title">Groups</h1>
        <form id="search">
            <input type="text" id="query" autocomplete="off" autofocus="true" placeholder="Group ID or Vanity URL" />
            <input type="submit" class="btn" value="Go" />
        </form>
    </div>

    <div id="content">

        <div id="help">
            <h2>Examples</h2>
            <p>
                <b>Group ID:</b> 103582791429521412
                <br /><b>Vanity URL:</b> Valve
            </p>
        </div>

        <div id="loading" style="display: none">
            Searching. Please wait.
        </div>

        <div id="result" style="display: none">
            <div id="error"><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/m04cuu-bfQw?rel=0" frameborder="0" allowfullscreen></iframe></div>
            <div id="info" style="display: none"></div>
        </div>

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
        $.getJSON('/groups/search/?q=' + $('#query').val(), function(data) {
            $('#error').hide();
            $('#info').hide();
            if (data == false) {
                $('#error').show();
            } else {
                // TODO: Check what kind of data has been received
                $('#info').empty();
                {
                    jQuery('<img/>', {
                        style: 'float: right;',
                        src: data['avatar_full_url']
                    }).appendTo('#info');
                    $('#info').append('<strong>' + data['name'] + '</strong><br />').show();
                    $('#info').append('<strong>ID:</strong> '+ data['id'] + '<br />').show();
                    $('#info').append('<strong>Headline:</strong> '+ data['headline'] + '<br />').show();
                    $('#info').append('<strong>Summary:</strong> '+ data['summary'] + '<br />').show();
                    $('#info').append('<strong>Vanity URL:</strong> '+ data['url'] + '<br />').show();
                }
                $('#info').show();
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
        color: '#000', // #rgb or #rrggbb
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