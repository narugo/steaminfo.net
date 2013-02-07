<?php $group = $this->group; ?>

<div class="page-header">
    <h1>Groups
        <small><?php echo $group->name; ?></small>
        <img src="<?php echo $group->avatar_icon_url; ?>"/>
    </h1>
</div>

<strong>Summary:</strong>
<div class="well well-small">
    <?php echo $group->summary; ?>
</div>