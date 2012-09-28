<div id="submenu">
    <h1 id="title">Users</h1>
    <form id="search" action="/users/profile" method="get">
        <input type="text" name="query" placeholder="Vanity URL, Steam ID, or Community ID" value="<?php echo $_GET["query"] ?>" />
    </form>
</div>