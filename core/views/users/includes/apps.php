<!DOCTYPE html>
<html>
<body>
<?php $apps = $this->apps; ?>
<table id="apps-table" class="table table-condensed">
    <thead>
    <tr>
        <th class="id">#</th>
        <th class="logo"></th>
        <th class="name">Name</th>
        <th class="used-2w">Used in last 2 weeks</th>
        <th class="used-total">Used total</th>
    </tr>
    </thead>
    <tbody>
    <?php $index = 1; // Game index
    foreach ($apps as $app) : ?>
    <tr>
        <td class="id"><?php echo $index; ?></td>
        <td class="logo"><img src="<?php echo $app->logo_url; ?>" /></td>
        <td class="name"><a href="http://store.steampowered.com/app/<?php echo $app->id; ?>"><?php echo $app->name; ?></a></td>
        <td class="used-2w"><?php echo $app->used_last_2_weeks.' hours'; ?></td>
        <td class="used-total"><?php echo $app->used_total.' hours'; ?></td>
    </tr>
        <?php
        $index++;
    endforeach;
    ?>
    </tbody>
</table>
</body>
</html>