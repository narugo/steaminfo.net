<div class="page-header">
    <h1>Groups</h1>
</div>

<div class="alert alert-error">In development.</div>

<div id="search" class="input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Group ID or Vanity URL">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</div>

<div id="help">
    <h4>Examples</h4>
    <strong>Group ID:</strong> 103582791429521412
    <br/><strong>Vanity URL:</strong> Valve
</div>

<div id="loading" style="display: none">
    <i class="icon-spinner icon-spin"></i>
    Searching. Please wait.
</div>

<div id="result" style="display: none">
    <div id="error">
        Error!
    </div>
    <div id="info" style="display: none"></div>
</div>

<script type="text/javascript">
    var is_help_hidden = false;
    $('#search').submit(function () {
        if (!is_help_hidden) {
            $('#help').hide("fast");
            is_help_hidden = true;
        }
        $('#result').hide("fast");
        $('#loading').show("fast");
        $.getJSON('/groups/search/?q=' + $('#query').val(), function (data) {
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
                    $('#info').append('<strong>ID:</strong> ' + data['id'] + '<br />').show();
                    $('#info').append('<strong>Headline:</strong> ' + data['headline'] + '<br />').show();
                    $('#info').append('<strong>Summary:</strong> ' + data['summary'] + '<br />').show();
                    $('#info').append('<strong>Vanity URL:</strong> ' + data['url'] + '<br />').show();
                }
                $('#info').show();
            }
            $('#loading').hide("fast");
            $('#result').show("fast");
        });
        return false;
    });
</script>