<div class="page-header">
    <h1>Apps</h1>
</div>

<form class="search form-inline input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true" placeholder="App ID">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</form>

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
    $(document).ready(function () {
        $('#search-submit').click(function () {
            $('#result').hide("fast");
            $('#loading').show("fast");
            $.getJSON('/apps/search/?q=' + $('#query').val(), function (data) {
                $('#error').hide();
                $('#info').hide();
                if (data == false) {
                    $('#error').show();
                } else {
                    // TODO: Check what kind of data has been received
                    $('#info').empty();
                    {
                        $('#info').append('<a href="/apps/' + data['id'] + '"><strong>' + data['name'] + '</strong></a>').show();
                    }
                    $('#info').show();
                }
                $('#loading').hide("fast");
                $('#result').show("fast");
            });
            return false;
        });
    });
</script>