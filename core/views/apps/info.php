<?php $app = $this->app; ?>

<div class="page-header">
    <h1>Apps
        <small><?php echo $app->name; ?></small>
    </h1>
</div>

<div id="app-info">

    <img id="header-image" class="pull-right" src="<?php echo $app->header_image_url; ?>"/>

    <div id="platforms">
        <?php
        if ($app->is_win) {
            echo '<img src="/assets/img/platforms/win.png"/>';
        }
        if ($app->is_mac) {
            echo '<img src="/assets/img/platforms/mac.png"/>';
        }
        if ($app->is_linux) {
            echo '<img src="/assets/img/platforms/linux.png"/>';
        }
        ?>
    </div>

    <p>
        <strong>App ID:</strong> <?php echo $app->id; ?>
        <br/><strong>Release date:</strong> <?php echo $app->release_date; ?>
        <br/><strong>Recommendations:</strong> <?php echo $app->recommendations; ?>
    </p>

    <p>
        <a href="http://store.steampowered.com/app/<?php echo $app->id; ?>/">Visit store page</a>
        <br/><a rel="nofollow" href="<?php echo $app->website; ?>">Visit website</a>
    </p>

    <div id="description" class="well well-small"><?php echo $app->detailed_description; ?></div>

    <?php if (!empty($app->legal_notice)) : ?>
        <p class="muted">
            <?php echo $app->legal_notice; ?>
        </p>
    <?php endif; ?>

</div>