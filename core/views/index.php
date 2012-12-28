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
    var MIN_LENGTH_TO_SUGGEST = 4;

    $('#search').bind('keypress', function () {
       var query = $('#search').val();
        if (query.length >= MIN_LENGTH_TO_SUGGEST) {
            getSuggestions(query);
        }
    });

    function getSuggestions(query) {
        $.getJSON('/index/searchSuggest/?q=' + query, function (data) {
            console.log(data);
        });
    }


    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#container" );
            $( "#container" ).scrollTop( 0 );
        }

        $( "#search" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "http://ws.geonames.org/searchJSON",
                    dataType: "jsonp",
                    data: {
                        featureClass: "P",
                        style: "full",
                        maxRows: 12,
                        name_startsWith: request.term
                    },
                    success: function( data ) {
                        response( $.map( data.geonames, function( item ) {
                            return {
                                label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
                                value: item.name
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function( event, ui ) {
                log( ui.item ?
                    "Selected: " + ui.item.label :
                    "Nothing selected, input was " + this.value);
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });
</script>