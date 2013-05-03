<!DOCTYPE html>
<html>
<body>
<div class="alert alert-info">
    Click on the names of columns to sort this table.
</div>
<table id="friends-table" class="table table-condensed tablesorter">
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
    /** @var SteamInfo\Models\Entities\Friends[] $friends */
    $friends = $this->friends;
    foreach ($friends as $friend) :
        ?>
        <tr>
            <td class="id"><?php echo $index; ?></td>
            <td class="avatar">
                <img src="<?php
                if (is_null($friend->getFriend()->getAvatarUrl())) echo "/assets/img/no_avatar.png";
                else echo $friend->getFriend()->getAvatarUrl();
                ?>"/>
            </td>
            <td class="name">
                <a href="/users/profile/<?php echo $friend->getFriend()->getId(); ?>">
                    <?php
                    if (is_null($friend->getFriend()->getNickname())) echo $friend->getFriend()->getId();
                    else echo $friend->getFriend()->getNickname();
                    ?>
                </a>
                <?php if (!is_null($friend->getFriend()->getTag())) : ?>
                    <span class="label label-important"><?php echo $friend->getFriend()->getTag(); ?></span>
                <?php endif; ?>
            </td>
            <td class="since">
                <?php
                if (is_null($friend->getSince())) echo 'Unknown';
                else echo $friend->getSince()->format(DATE_RFC850);
                ?>
            </td>
        </tr>
        <?php
        $index++;
    endforeach;
    ?>
    </tbody>
</table>
<script type="text/javascript">
    $(document).ready(function () {
            $("#friends-table").tablesorter();
        }
    );
</script>
</body>
</html>