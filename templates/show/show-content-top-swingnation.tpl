<h1>{$showTitle}</h1>

<div id="div_mediaPlayer" class="grid_9 alpha">
 <iframe width="488" height="275" src="http://www.youtube.com/embed/{$currentShow->extended->flash_url}" frameborder="0" allowfullscreen></iframe>
</div>

<div id="div_currentShow" class="grid_7 omega text">
    {include file='show/show-description-swingnation.tpl'}

    {if $feed}
        <p><a href="{$feed}" class="iconText iconFeedLarge">Subscribe</a></p>
    {/if}

</div>

<div class="clear">&nbsp;</div>