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