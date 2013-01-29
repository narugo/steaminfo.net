<h3>Welcome, <?php echo $_SESSION['id']; ?>!</h3>

What do you want to do today?
<ol>
    <li><a href="/users/<?php echo $_SESSION['id']; ?>">View information about my Steam profile</a></li>
    <li><a href="#">View your Dota 2 matches</a> <span class="label label-important">Not implemented</span></li>
</ol>