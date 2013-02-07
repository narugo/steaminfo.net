<div class="page-header">
    <h1>Groups</h1>
</div>

<form class="search form-inline input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Group ID or Vanity URL">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</form>

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

<?php if (!empty($this->top)) : ?>
    <br/>
    <strong>Most popular groups today:</strong>
    <ol id="top-10">
        <?php foreach ($this->top as $group) {
            echo '<li>';
            echo '<img src="' . $group->avatar_icon_url . '"/>';
            echo '<a href="/groups/' . $group->id . '">';
            if (empty($group->name)) {
                echo $group->id;
            } else {
                echo $group->name;
            }
            echo '</a>';
            echo '</li>';
        }
        ?>
    </ol>
<?php endif; ?>

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
                            style: 'float: left; margin-right: 4px;',
                            src: data['avatar_icon_url']
                        }).appendTo('#info');
                        $('#info').append('<a href="/groups/' + data['id'] + '"><strong>' + data['name'] + '</strong></a>').show();
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