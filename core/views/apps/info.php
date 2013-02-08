<?php $app = $this->app; ?>

<div class="page-header">
    <h1>Apps
        <small><?php echo $app->name; ?></small>
    </h1>
</div>

<p>
    <img src="<?php echo $app->logo_url; ?>"/>
</p>

<p>
    <a href="http://store.steampowered.com/app/<?php echo $app->id; ?>/">Visit store page</a>
</p>