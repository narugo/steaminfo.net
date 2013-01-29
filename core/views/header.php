<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php
    // Loading styles
    foreach ($css as $current_css) {
        echo '<link href="' . $current_css . '" rel="stylesheet" type="text/css" />';
    }
    // Loading scripts
    foreach ($js as $current_js) {
        echo '<script src="' . $current_js . '" type="text/javascript"></script>';
    }
    ?>
</head>
<body>
<div class="container">

    <div class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="/">Steam Info</a>
            <ul class="nav">
                <li><a href="/users/">Users</a></li>
                <li><a href="/groups/">Groups</a></li>
                <li><a href="/dota/">Dota</a></li>
                <li><a href="/stats/">Stats</a></li>
                <li><a href="/api/">API</a></li>
                <li><a href="/about/">About</a></li>
            </ul>
        </div>
    </div>