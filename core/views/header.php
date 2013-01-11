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

    <ul class="breadcrumb">
        <li><a href="/">Home</a> <span class="divider">/</span></li>
        <li><a href="/users/">Users</a> <span class="divider">/</span></li>
        <li><a href="/groups/">Groups</a> <span class="divider">/</span></li>
        <li><a href="/stats/">Stats</a> <span class="divider">/</span></li>
        <li><a href="/about/">About</a></li>
    </ul>