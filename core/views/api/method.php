<?php
/** @var \SteamInfo\Models\Entities\ApiInterface $interface */
$interface = $this->interface;

/** @var \SteamInfo\Models\Entities\ApiMethod $method */
$method = $this->method;
?>

<div class="page-header">
    <h1>WebAPI
        <small><?php echo $interface->getName(); ?>/<?php echo $method->getName(); ?></small>
    </h1>
</div>

<h3>Parameters</h3>
<ol>
    <?php
    foreach ($method->getParameters() as $parameter) {
        echo "<li>" . $parameter->getName() . "</li>";
    }
    ?>
</ol>