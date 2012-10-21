<!DOCTYPE html>
<html>
<body>
<?php $friends = $this->friends; ?>
<table id="friends-table" class="table table-condensed">
    <thead>
    <tr>
        <th class="id">#</th>
        <th class="avatar"></th>
        <th class="name">User</th>
        <th class="since">Friends since</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $index = 1; // Friend index
    date_default_timezone_set('UTC');
    foreach ($friends as $friend) :
        ?>
    <tr>
        <td class="id"><?php echo $index; ?></td>
        <td class="avatar">
            <img src="<?php
                if (is_null($friend->avatar_url)) echo "/img/no_avatar.png";
                else echo $friend->avatar_url;
                ?>" />
        </td>
        <td class="name">
            <a href="/users/profile/<?php echo $friend->community_id; ?>">
                <?php
                if (is_null($friend->nickname)) echo $friend->community_id;
                else echo $friend->nickname;
                ?>
            </a>
            <?php if (! is_null($friend->tag)) : ?>
            <span class="badge badge-success"><?php echo $friend->tag; ?></span>
            <?php endif; ?>
        </td>
        <td class="since"><?php echo $friend->since; ?></td>
    </tr>
        <?php
        $index++;
    endforeach;
    ?>
    </tbody>
</table>
</body>
</html>