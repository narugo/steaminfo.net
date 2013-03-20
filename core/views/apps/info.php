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
        <?php if (!empty($app->id)) echo "<strong>App ID:</strong> $app->id"; ?>
        <?php if (!empty($app->release_date)) echo "<br/><strong>Release date:</strong> $app->release_date"; ?>
        <?php if (!empty($app->recommendations)) echo "<br/><strong>Recommendations:</strong> $app->recommendations"; ?>
    </p>

    <p>
        <a href="http://store.steampowered.com/app/<?php echo $app->id; ?>/">Visit store page</a>
        <?php if (!empty($app->website)) echo "<br/><a rel=\"nofollow\" href=\"$app->website\">Visit website</a>"; ?>
    </p>

    <?php if (!empty($app->detailed_description)) : ?>
        <div id="description" class="well well-small"><?php echo $app->detailed_description; ?></div>
    <?php endif; ?>

    <?php if (!empty($app->legal_notice)) : ?>
        <p class="muted">
            <?php echo $app->legal_notice; ?>
        </p>
    <?php endif; ?>

</div>