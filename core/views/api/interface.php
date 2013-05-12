<?php
/** @var \SteamInfo\Models\Entities\ApiInterface $interface */
$interface = $this->interface;
?>

<div class="page-header">
    <h1>WebAPI
        <small><?php echo $interface->getName(); ?></small>
    </h1>
</div>

<h3>Methods</h3>
<ol>
    <?php
    foreach ($interface->getMethods() as $method) {
        echo '<li><a href="/api/' . $interface->getName() . '/' . $method->getName() . '/">' . $method->getName() . '</a></li>';
    }
    ?>
</ol>