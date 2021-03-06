<div class="page-header">
    <h1>Users</h1>
</div>

<form id="user-search" class="search form-inline input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Vanity URL, Steam ID, or Community ID">
    <button class="btn btn-primary" type="button">Search</button>
</form>

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

<?php if (!empty($this->top)) : ?>
    <br/>
    <strong>Most popular users today:</strong>
    <ol id="top-10">
        <?php foreach ($this->top as $user) {
            echo '<li>';
            echo '<img src="' . $user->avatar_url . '"/>';
            echo '<a href="/users/profile/' . $user->community_id . '">';
            if (empty($user->nickname)) {
                echo $user->id;
            } else {
                echo $user->nickname;
            }
            echo '</a>';
            if (!empty($user->tag)) {
                echo ' <span class="label label-important">' . $user->tag . '</span>';
            }
            echo '</li>';
        }
        ?>
    </ol>
<?php endif; ?>

<script type="text/javascript">
    var is_help_hidden = false;

    $(document).ready(function () {
        $("#user-search").submit(function (e) {
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
                    $('#profile-summary').append(data['status']);
                    if (data['current_app_id'] != null) {
                        $('#profile-summary').append(', in ' + data['current_app_name']).show();
                    }
                    jQuery('<br>').appendTo('#profile-summary');
                    jQuery('<a/>', {
                        href: '/users/profile/' + data['id'],
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