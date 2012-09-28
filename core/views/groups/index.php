<?php
$page_id = "groups";
$title = "Groups";
include $_SERVER['DOCUMENT_ROOT']."/includes/header.php";
?>
<div id="content">
    <?php
    $query = trim($_GET["query"]);
    if (! (is_null($query) || empty($query)))
    {
    }
    else
    {
        ?>
        Here you can get info about groups on Steam. Just type in group ID.
        <h2>Example</h2>
        <strong>103582791429521412</strong> - Valve's group ID
        <?php
    }
    ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/footer.php"; ?>