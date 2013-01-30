<?php $group = $this->group; ?>

<h3><?php echo $group->name; ?></h3>

<img class="pull-right" src="<?php echo $group->avatar_medium_url; ?>"/>

<strong>Summary:</strong>
<div class="well well-small">
    <?php echo $group->summary; ?>
</div>