<?php if (!$no_header_footer) : ?>
    <footer>

        <hr/>

        <div class="pull-left">
            Powered by <a href="http://steampowered.com/">Steam</a>,
            created by <a href="http://tsukanov.me">me</a>.
        </div>

        <div class="pull-right" style="font-size: 12px;">
            <?php
            if (empty($_SESSION['id'])) {
                ?>
                <a href="/auth/"><img src="/static/img/steam_login.png"/></a>
            <?php } else { ?>
                <em><?php echo str_replace('http://steamcommunity.com/openid/id/', '', $_SESSION['id']); ?></em>
                | <a href="/control/">Control</a> | <a href="/auth/logout/">Logout</a>
            <?php } ?>
        </div>

    </footer>
<?php endif; ?>

</div>
</body>

<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-9427181-6']);
    _gaq.push(['_setDomainName', 'steaminfo.net']);
    _gaq.push(['_trackPageview']);

    (function () {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
</script>

</html>