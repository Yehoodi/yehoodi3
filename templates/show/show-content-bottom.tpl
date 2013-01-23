	
<div id="div_channelInfoDetails">
    <h2>{$currentShow->extended->show_name}</h2>
    {$currentShow->descrip|markdown}
    <ul class="links">
        {if $currentShow->extended->shownotes}<li class="hiddenController" id="div_showNotesController"><a href="javascript:void(0);" id="div_notesHider" class="a_expanded">Read Show Notes</a></li>{/if}
        {if $currentShow->extended->media_url}<li><a class="iconText iconDownload" href="{$currentShow->extended->media_url}">Download</a></li>{/if}
        <li><a class="iconText iconBalloonSmall" href="{geturl controller='comment'}{$currentShow->getId()}/{$currentShow->resourceSeoUrlString}"> Read Comments</a></li>        
    </ul>
    {if $currentShow->extended->shownotes}
    <div class="hiddenController text" id="div_showNotes" style="display: none;">
        {$currentShow->extended->shownotes}
    </div>
    {/if}
</div>
	
<div id="div_channelArchive" class="grid_10 alpha">
    <h2>Archive</h2>
    {include file='show/show-archive.tpl'}
</div>
    
<div id="div_channelText" class="grid_5 omega text">
    <h2>Help:</h2>
    <ul>    
        <li>Use the play button on the above player or simply download the mp3 files on this page if available. {if $feed} You can also subscribe to it using your Podcast reader (<a href="http://www.itunes.com/">iTunes</a>, <a href="http://juicereceiver.sourceforge.net/">Juice</a>, etc.){/if}.</li>
        {if $feed}<li>If you have iTunes, search for the podcast and subscribe.</li>
        <li>Subscribe to the show's <a href="{$feed}">Podcast Feed!</a></li>{/if}
    </ul>
</div>

<div class="clear">&nbsp;</div>
