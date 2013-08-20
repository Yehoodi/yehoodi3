{include file='header.tpl' section='ilhc' maps=false}

<div class="grid_10 alpha">
    <img id="ilhcBanner" src="/images/graphics/ilhc5.png" alt="Live Broadcast from ILHC 2013, August 23-25" />
    <p id="techSupport">
        <strong><em>Having technical issues?</em></strong>
        Post to our <a href="/comment/174773/yehoodi-ilhc-broadcast-tech-su/">tech support thread</a> or send us an email to <a href="mailto:support@yehoodi.com">support@yehoodi.com</a>.
    </p>
    {if $smart_device}
        Sorry, viewing the live stream is not available on your device. If you are using an iPad or an iPhone, you may need to download the Justin.TV app. Please go to the <a href="http://community.justin.tv/mediawiki/index.php/Faq">Justin.TV help website</a> for instructions on viewing on your device.
    {else}
        <object type="application/x-shockwave-flash"
                data="http://www.justin.tv/widgets/live_embed_player.swf?channel=yehoodi"
                id="live_embed_player_flash"
                height="327"
                width="580"
                bgcolor="#000000">
            <param name="allowFullScreen" value="true" />
            <param name="allowScriptAccess" value="always" />
            <param name="allowNetworking" value="all" />
            <param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" />
            <param name="flashvars" value="hostname=www.justin.tv&channel=yehoodi&auto_play=true&start_volume=25" />
        </object>
    {/if}
    <p id="watchLiveLink">
        <a href="http://www.justin.tv/yehoodi#r=-rid-&amp;s=em" class="trk">Watch live video from yehoodi on www.justin.tv</a>
    </p>
</div>
<div class="grid_6 omega" id="chatFrame">
    <iframe frameborder="0" scrolling="no" id="chat_embed" src="http://www.justin.tv/chat/embed?channel=yehoodi&amp;default_chat=jtv&amp;popout_chat=true#r=-rid-&amp;s=em" height="840" width="340"></iframe>
</div>
<div class="clear">&nbsp;</div>
<div class="grid_10 alpha faq">
    <h2>Broadcast Schedule</h2>
    <p>NOTE: All times are Eastern Standard Time. Please use <a href="http://www.timeanddate.com">TimeandDate.com</a> to convert to your local time zone.
    Schedule subject to change. See <a href="http://www.ilhc.com" target="_blank">ILHC Official website</a> for more information. </p>
    <table>
        <tr><th colspan="2">Friday, August 23</th></tr>
        <tr><td>9:00pm</td><td>Broadcast Begins</td></tr>
        <tr><td>9:15pm</td><td><strong>Slow Dance</strong></td></tr>
        <tr><td>10:00pm</td><td><strong>Showcase Pro</strong></td></tr>
        <tr><td>11:40pm</td><td><strong>Strictly Advanced Finals</strong></td></tr>
        <tr><td>12:55pm</td><td><strong>Solo Charleston Finals</strong></td></tr>
        <tr><td>1:30am</td><td><strong>All Star Strictly Finals</strong></td></tr>
        <tr><td>2:00am</td><td>Wrap-up & Broadcast ends</td></tr>
    </table>
    <table>
        <tr><th colspan="2">Saturday, August 24</th></tr>
        <tr><td>9:00pm</td><td>Broadcast Begins</td></tr>
        <tr><td>9:15pm</td><td><strong>Strictly Balboa Finals</strong></td></tr>
        <tr><td>9:30pm</td><td><strong>Honoree Presentation</strong></td></tr>
        <tr><td>10:00pm</td><td><strong>Classic Pro</strong></td></tr>
        <tr><td>11:40pm</td><td><strong>Strictly Open Finals</strong></td></tr>
        <tr><td>12:55pm</td><td><strong>Invitational Strictly<strong></td></tr>
        <tr><td>1:30am</td><td>Wrap-up & broadcast ends</td></tr>
    </table>
    <table>
        <tr><th colspan="2">Sunday, August 25</th></tr>
        <tr><td>4:00pm</td><td>Broadcast Begins</td></tr>
        <tr><td>4:20pm</td><td><strong>Jack & Jill All Star Finals</strong></td></tr>
        <tr><td>5:30pm</td><td><strong>Juniors</strong></td></tr>
        <tr><td>8:15pm</td><td><strong>Jack and Jill Invitational</strong></td></tr>
        <tr><td>9:00pm</td><td><strong>Awards</strong></td></tr>
        <tr><td>9:30pm</td><td>Wrap-up & broadcast ends. See you next year!</td></tr>
    </table>
</div>
<div class="grid_6 omega">
    <script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
    {literal}
        <script>
            new TWTR.Widget({
                version: 2,
                type: 'search',
                search: '#ilhc',
                interval: 30000,
                /*title: 'ILHC Tweet Stream',*/
                subject: 'People are tweeting about ILHC',
                width: 254,
                height: 400,
                theme: {
                    shell: {
                        background: '#E1D9B9',
                        color: '#4E4E4E'
                    },
                    tweets: {
                        background: '#FBFAEE',
                        color: '#444444',
                        links: '#8A2A06'
                    }
                },
                features: {
                    scrollbar: false,
                    loop: true,
                    live: true,
                    behavior: 'default'
                }
            }).render().start();
        </script>
    {/literal}
</div>
<div class="clear">&nbsp;</div>

{include file='footer.tpl'}