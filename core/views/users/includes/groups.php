<!DOCTYPE html>
<html>
<body>
<div class="alert alert-info">
    Click on the names of columns to sort this table.
</div>
<table id="groups-table" class="table table-condensed tablesorter">
    <thead>
    <tr>
        <th class="id">#</th>
        <th class="avatar"></th>
        <th class="name">Group</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $index = 1; // Group index
    foreach ($this->groups as $group) :
        ?>
        <tr>
            <td class="id"><?php echo $index; ?></td>
            <td class="avatar">
                <img src="<?php
                if (is_null($group->avatar_icon_url)) echo "/assets/img/no_avatar.png";
                else echo $group->avatar_icon_url;
                ?>"/>
            </td>
            <td class="name">
                <a href="/groups/<?php echo (103582791429521408 + $group->id); ?>">
                    <?php
                    if (is_null($group->name)) echo $group->id;
                    else echo $group->name;
                    ?>
                </a>
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
            $("#groups-table").tablesorter();
        }
    );
</script>
</body>
</html>