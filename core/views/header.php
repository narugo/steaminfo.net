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

<?php if (!$no_header_footer) : ?>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="/">Steam Info</a>
                <ul class="nav">
                    <form id="header-search" class="navbar-form pull-left">
                        <input id="header-search-input" type="text" class="span2" autocomplete="off" autofocus="true"
                               placeholder="Search"/>
                    </form>
                    <li><a href="/users/">Users</a></li>
                    <li><a href="/apps/">Apps</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dota<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/dota/matches/">Matches</a></li>
                            <li><a href="/dota/leagues/">Leagues</a></li>
                            <li><a href="/dota/teams/">Teams</a></li>
                        </ul>
                    </li>
                    <li><a href="/api/">API</a></li>
                </ul>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img
                                src="/static/img/steam_logo.svg"/>
                            <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="http://store.steampowered.com/">Store</a></li>
                            <li><a href="http://store.steampowered.com/stats/">Stats</a></li>
                            <li class="divider"></li>
                            <li class="nav-header">Community</li>
                            <li><a href="http://steamcommunity.com/apps/">Game hubs</a></li>
                            <li><a href="http://steamcommunity.com/discussions/">Discussions</a></li>
                            <li><a href="http://steamcommunity.com/workshop/">Workshop</a></li>
                            <li><a href="http://steamcommunity.com/greenlight/">Greenlight</a></li>
                            <li><a href="http://steamcommunity.com/market/">Market</a></li>
                        </ul>
                    </li>
                    <li><a href="/about/">About</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#header-search").submit(function (e) {
                return false;
            });
            $("#header-search-input").autocomplete({
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
                                        label: "<img src=\"" + item.avatar_url + "\" class=\"search-user-avatar\" /> " + item.name
                                            + " <span class=\"label label-success\">" + item.type + "</span>",
                                        value: "/users/profile/" + item.id
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

<?php endif; ?>

<div id="main-container" class="container">
