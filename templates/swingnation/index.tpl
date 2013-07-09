{include file='header.tpl' section='swingnation' maps=false}

<div class="grid_10 alpha">
    <img src="/images/featured-content/SN-002-Flash-Feature.png" alt="SwingNation Video Podcast" style="margin-bottom: 12px; width: 100%;" />
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
    <iframe frameborder="0" scrolling="no" id="chat_embed" src="http://www.justin.tv/chat/embed?channel=yehoodi&amp;default_chat=jtv&amp;popout_chat=true#r=-rid-&amp;s=em" height="580" width="340"></iframe>
</div>
<div class="clear">&nbsp;</div>
<div class="grid_10 alpha faq">
    <h2>Broadcast Schedule</h2>
    <p>NOTE: All times are Pacific Standard Time. Please use <a href="http://www.timeanddate.com">TimeandDate.com</a> to convert to your local time zone.</p>
    <table>
        <tr><th colspan="2">Monday, September 9</th></tr>
        <tr><td>7:30pm</td><td>First show for Seaeon #3! Broadcast Begins</td></tr>
    </table>
    <table>
        <tr><th colspan="2">Monday, September 23</th></tr>
        <tr><td>7:30pm</td><td>Broadcast Begins</td></tr>
    </table>
    <table>
        <tr><th colspan="2">Monday October 7</th></tr>
        <tr><td>7:30pm</td><td>Broadcast Begins</td></tr>
    </table>
</div>
<div class="grid_6 omega"></div>
<div class="clear">&nbsp;</div>

{include file='footer.tpl'}