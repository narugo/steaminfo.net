<div class="page-header">
    <h1>Dota 2
        <small>Teams</small>
    </h1>
</div>

<div id="search" class="input-append">
    <label>Search teams</label>
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Team ID">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</div>

<p>
    Try <strong><a href="/dota/teams/111474">111474</a></strong>.
</p>

<script type="text/javascript">
    $(document).ready(function () {
        $('#search-submit').click(function () {
            window.location = ("/dota/teams/" + $('#query').val());
        });
    });
</script>