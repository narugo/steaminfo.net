<div class="page-header">
    <h1>Dota 2
        <small>Teams</small>
    </h1>
</div>

<form id="team-search" class="search form-inline input-append">
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true" placeholder="Team ID">
    <button class="btn btn-primary" type="button">Search</button>
</form>

<p>
    Try <strong><a href="/dota/teams/111474">111474</a></strong>.
</p>

<script type="text/javascript">
    $(document).ready(function () {
        $("#team-search").submit(function (e) {
            window.location = ("/dota/teams/" + $('#query').val());
            return false;
        });
    });
</script>