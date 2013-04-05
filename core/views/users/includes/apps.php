<!DOCTYPE html>
<html>
<body>
<div class="alert alert-info">
    Click on the names of columns to sort this table.
</div>
<table id="apps-table" class="table table-condensed tablesorter">
    <thead>
    <tr>
        <th class="id">ID</th>
        <th class="name">Name</th>
        <th class="used-2w">Used in last 2 weeks (hours)</th>
        <th class="used-total">Used total (hours)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->apps as $app) : ?>
        <tr>
            <td class="id"><?php echo $app->id; ?></td>
            <td class="name"><a
                    href="/apps/<?php echo $app->id; ?>"><?php echo $app->name; ?></a></td>
            <td class="used-2w"><?php echo round($app->used_last_2_weeks / 60, 1); ?></td>
            <td class="used-total"><?php echo round($app->used_total / 60, 1); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script type="text/javascript">
    $(document).ready(function () {
        $("#apps-table").tablesorter();
    });
</script>
</body>
</html>