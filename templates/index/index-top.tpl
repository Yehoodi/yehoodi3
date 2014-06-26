<div id="div_flashFeaturedCaption">
{if $liveShow}
    <div id="div_flashFeatured">
     	<span id="flashFeatured">
            <iframe src="http://new.livestream.com/accounts/774982/events/2974280/player?width=640&height=360&autoPlay=true&mute=false" width="640" height="360" frameborder="0" scrolling="no"> </iframe>
        </span>
    </div>
    <div id="div_yehoodiCaption">
        <h2><strong>Yehoodi</strong> is a global community of <em>Lindy Hoppers.</em></h2>
        <p>Lindy, jitterbug, swing dance&hellip; Whatever way you say it, we welcome anyone that loves this music and this dance.</p>
        <!--<p>If you are new here, we've created <a href="javascript:vidWindow();">this little video</a> for you to check out.</p>-->
        <p>Check out our <a href="{geturl controller='discussion' action='featured'}">featured content</a>, see <a href="{geturl controller='calendar'}">upcoming swing events</a>, and chat in our <a href="{geturl controller='discussion' action='lounge'}">Lounge</a>.</p>
    </div>
{else}
    <div id="div_flashFeatured">
     	<span id="flashFeatured">
            <object id="flashCarousel" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="640" height="200">
                <param name="FlashVars" value="xmlfile=/assets20140626/featured-content.xml" />
                <param name="movie" value="/assets20140626/featured-content2.swf" />
                <param name="quality" value="high" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="/assets20140626/featured-content2.swf" width="640" height="200">
                    <!--<![endif]-->
                    {include file='index/module.featured-noflash.tpl'}
                    <!--[if !IE]>-->
                </object>
                <!--<![endif]-->
            </object>
        </span>
    </div>
    <div id="div_yehoodiCaption">
        <h2><strong>Yehoodi</strong> is a global community of <em>Lindy Hoppers.</em></h2>
        <p>Lindy, jitterbug, swing dance&hellip; Whatever way you say it, we welcome anyone that loves this music and this dance.</p>
        <!--<p>If you are new here, we've created <a href="javascript:vidWindow();">this little video</a> for you to check out.</p>-->
        <p>Check out our <a href="{geturl controller='discussion' action='featured'}">featured content</a>, see <a href="{geturl controller='calendar'}">upcoming swing events</a>, and chat in our <a href="{geturl controller='discussion' action='lounge'}">Lounge</a>.</p>
    </div>
{/if}
</div>