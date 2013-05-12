<?php
/** @var \SteamInfo\Models\Entities\ApiInterface[] $interfaces */
$interfaces = $this->interfaces;
?>

<div class="page-header">
    <h1>WebAPI</h1>
</div>

<div class="alert alert-info">
    List of supported APIs can be found at
    <a href="https://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/">
        https://api.steampowered.com/ISteamWebAPIUtil/GetSupportedAPIList/v0001/</a>.
</div>

<h3>Interfaces</h3>
<ol>
    <?php
    foreach ($interfaces as $interface) {
        echo '<li><a href="/api/' . $interface->getName() . '/">' . $interface->getName() . '</a></li>';
    }
    ?>
</ol>