<h2 id="title">Dota 2</h2>

<div id="search" class="input-append">
    <label>Search matches</label>
    <input id="query" class="span2" type="text" autocomplete="off" autofocus="true"
           placeholder="Match ID">
    <button id="search-submit" class="btn btn-primary" type="button">Search</button>
</div>

Try <strong>67100756</strong>.

<script type="text/javascript">
    $(document).ready(function () {
        $('#search-submit').click(function () {
            window.location = ("/dota/match/" + $('#query').val());
        });
    });
</script>