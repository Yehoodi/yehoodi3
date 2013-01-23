<h1>{$showTitle}</h1>

<div id="div_mediaPlayer" class="grid_9 alpha">
 {if $smart_device}
    <video src="/shows/{$currentShow->extended->media_url}" controls="controls" width=488 height=314></video>
 {else}
    <object width="488" height="314" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0">
        <param name="salign" value="lt">
        <param name="quality" value="high">
        <param name="scale" value="noscale">
        <param name="wmode" value="transparent">
        <param name="movie" value="{$currentShow->extended->flash_url}.swf">
        <param name="FlashVars" value="&streamName={$currentShow->extended->flash_url}.flv&skinName=http://geekfile.googlepages.com/flvskin&autoPlay=false&autoRewind=true">
        <embed width="488" height="314" flashvars="&streamName={$currentShow->extended->flash_url}.flv&autoPlay=false&autoRewind=true&skinName=http://geekfile.googlepages.com/flvskin" quality="high" scale="noscale" salign="LT" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" src="{$currentShow->extended->flash_url}.swf" wmode="transparent"></embed>
    </object>
    {/if}
</div>

<div id="div_currentShow" class="grid_7 omega text">
{if $showCode == 'YTV'}
    <img class="showLogo" src="/images/graphics/YTV-Logo.png" alt="The Yehoodi Video Show" title="Yehoodi Talk Show: Video Edition" />
    <h2 class="hidden">Yehoodi Talk Show</h2>
    <h3>Host: Rik "Rikomatic" Panganiban</h3><h3>Host: Manu "Spuds" Smith</h3>
    <p>The Yehoodi Talk Show brings the latest swing news, interviews and general silliness to your desktop. Join the Yehoodi crew as they chat about anything that does and doesn't swing.</p>
    <p><a href="{$feed}" class="iconText iconFeedLarge">Subscribe</a></p>
{else}
{/if}
</div>

<div class="clear">&nbsp;</div>