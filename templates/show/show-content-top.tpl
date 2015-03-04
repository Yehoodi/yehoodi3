{* MP3 Player from http://flash-mp3-player.net/ *}

<h1>{$showTitle}</h1>

<div id="div_mediaPlayer" class="grid_8 alpha">
    <img src="{imagefilename id=$currentShow->image->getId() w=488 h=314}" alt="placeholder" /><br />
    {if $smart_device}
        <audio src="{$currentShow->extended->media_url}" controls="controls"></audio>
    {else}
        <object type="application/x-shockwave-flash" data="/flash/player_mp3_maxi_0.6.0.swf" width="450" height="40">
        <param name="movie" value="/flash/player_mp3_maxi_0.6.0.swf" />
        <param name="bgcolor" value="#CBBB95" />
        <param name="FlashVars" value="mp3={$currentShow->extended->media_url}&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;showloading=always&amp;loadingcolor=ffff3d&amp;bgcolor=CBBB95" />
        </object>
    {/if}
</div>

<div id="div_currentShow" class="grid_8 omega text">
{if $showCode == 'HMJ'}
    {include file='show/show-description-heymisterjesse.tpl'}
{elseif $showCode == 'TRK'}
    {include file='show/show-description-thetrack.tpl'}
{elseif $showCode == 'YTS'}
    {include file='show/show-description-yehooditalkshow.tpl'}
{elseif $showCode == 'LIN'}
    {include file='show/show-description-lindyman.tpl'}
{elseif $showCode == 'SFBL'}
    {include file='show/show-description-sausagebeaver.tpl'}
{else}
    {include file='show/show-description-noshow.tpl'}
{/if}

{if $feed}
    <p><a href="{$feed}" class="iconText iconFeedLarge">Subscribe</a></p>
{/if}
</div>

<div class="clear">&nbsp;</div>

