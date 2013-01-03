<div class="container">

    <ul class="breadcrumb">
        <li><a href="/">Home</a> <span class="divider">/</span></li>
        <li><a href="/users/">Users</a> <span class="divider">/</span></li>
        <li><a href="/groups/">Groups</a> <span class="divider">/</span></li>
        <li><a href="/stats/">Stats</a> <span class="divider">/</span></li>
        <li><a href="/about/">About</a></li>
    </ul>

    <div class="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Warning!</strong> This website is in development. Most features aren't working properly or not
        implemented yet.
    </div>

    <h1>Steam Info</h1>

    <input type="text" id="search" autocomplete="off" autofocus="true" placeholder="Search"/>



</div>

<script type="text/javascript">
    $(function () {
        function log(message) {
            $("<div>").text(message).prependTo("#container");
            $("#container").scrollTop(0);
        }

        $("#search").autocomplete({
            html: true,
            source: function (request, response) {
                $.ajax({
                    url: "/index/searchSuggest/",
                    dataType: "json",
                    data: {
                        query: request.term
                    },
                    success: function (data) {
                        console.log(data);
                        response($.map(data, function (item) {
                            return {
                                label: "<img src=\"" + item.avatar_url + "\" /> " + item.nickname
                                    + " <span class=\"label label-info\">" + item.type + "</span>",
                                value: item.nickname
                            }
                        }));
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                alert(this.value);
                log(ui.item ?
                    "Selected: " + ui.item.label :
                    "Nothing selected, input was " + this.value);
            }
        });
    });
</script>