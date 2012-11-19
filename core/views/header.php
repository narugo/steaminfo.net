<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $page_title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php
    // Loading styles
    foreach ($css as $current_css) {
        echo '<link href="/assets/css/' . $current_css . '.css" rel="stylesheet" type="text/css" />';
    }
    // Loading scripts
    foreach ($js as $current_js) {
        echo '<script src="/assets/js/' . $current_js . '.js" type="text/javascript"></script>';
    }
    ?>
</head>
<body>