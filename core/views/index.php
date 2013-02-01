<div class="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Warning!</strong> This website is in development. Most features aren't working properly or not
    implemented yet. Website may be very slow, because it's hosted on my local PC and connection isn't the best.
    Sorry.
</div>

<input type="text" id="search" autocomplete="off" autofocus="true" placeholder="Search"/>

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
                            if (item.type == "user") {
                                return {
                                    label: "<img src=\"" + item.avatar_url + "\" /> " + item.nickname
                                        + " <span class=\"label label-success\">" + item.type + "</span>",
                                    value: "/users/" + item.community_id
                                }
                            } else if (item.type == "group") {
                                return {
                                    label: "<img src=\"" + item.avatar_icon_url + "\" /> " + item.name
                                        + " <span class=\"label label-info\">" + item.type + "</span>",
                                    value: "/groups/" + item.id
                                }
                            } else if (item.type == "app") {
                                return {
                                    label: item.name + " <span class=\"label label-inverse\">" + item.type + "</span>",
                                    value: "/apps/" + item.id
                                }
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                window.location = (ui.item.value);
            }
        });
    });
</script>