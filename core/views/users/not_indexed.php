<div class="container">

    <ul class="breadcrumb">
        <li><a href="/users/">Users</a> <span class="divider">/</span></li>
        <li><a href="/groups/">Groups</a> <span class="divider">/</span></li>
        <li><a href="/stats/">Stats</a> <span class="divider">/</span></li>
        <li><a href="/about/">About</a></li>
    </ul>

    <div class="alert alert-error">
        <strong>This profile is not indexed yet!</strong> You can <a id="update-info" href="#">force update</a>.
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#update-info").click(function (event) {
            $.ajax({
                url: "/users/update/",
                dataType: "json",
                data: {
                    id: "<?php echo $this->id; ?>"
                },
                success: function (data) {
                    alert("Success! Please, refresh the page.");
                }
            });
        });
    });
</script>