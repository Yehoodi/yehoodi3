{include file='header.tpl' section='comment' maps=true}

{* Removing this for 3.1 demo *}
{*if ($linkPrevURL||$linkNextURL)}
    <div id="div_resourcePagination" class="grid_16">
        {if $linkPrevURL}
            <span class="prev"><a href="{$linkPrevURL}">{$linkPrevTitle|truncate:32:"&hellip;"}</a></span>
        {/if}
        
        <span>{$linkDescription}</span>
        
        {if $linkNextURL}
            <span class="next"><a href="{$linkNextURL}">{$linkNextTitle|truncate:32:"&hellip;"}</a></span>
        {/if}
    </div>
    <div class="clear">&nbsp;</div>
{/if*}

{foreach from=$resources item=rsrc name=resources}
    {if $pageNumber == 1}
    
        <h1 {if $rsrc->isClosed()} class="iconText iconLockLarge"{/if}>
            {$rsrc->title}
            {if !$rsrc->isLive()}<em>[INACTIVE]</em>{/if}
            {* If this variable exists, the user is coming from the Calendar page *}
            {if $rsrc->meta->resourceName == 'event'}{if $calendarLink}
                <a class="buttonLink iconText iconCalendarSearchLarge" href="{geturl controller='calendar'}{$calendarLink}">Back to Calendar</a>
            {/if}{/if}      
        </h1>
        <div class="grid_2 alpha">           
            <div class="div_topicAuthor">			
    	        {include file='modules/module.user-avatar-meta.tpl' userMeta=$rsrc->userMeta mailMeta=$rsrc->meta}
            </div>
        </div>
        <div id="div_details" class="grid_9">            
     	    {include file='comment/detail.tpl'}                	
        </div>
        <div id="div_sidebar" class="grid_5 omega">
            {include file='modules/module.resource-actions.tpl'}
            <div id="div_shareLinks">
                <div class="div_shareLink"><!-- Twitter --> <a href="http://twitter.com/share" class="twitter-share-button" data-url="{$siteURL}{$controller}/{$rsrc->getId()}/{$rsrcUrl}/1" data-count="horizontal" data-via="yehoodi" data-text="{$rsrc->title|escape}">Tweet</a></div>
                <div class="div_shareLink"><!-- Facebook --> <a name="fb_share" type="button_count" share_url="{$siteURL}{$controller}/{$rsrc->getId()}/{$rsrcUrl}/" href="http://www.facebook.com/sharer.php">Share</a></div>
                <div class="clear">&nbsp;</div>
            </div>
            {if $locationCheck && $pageNumber <= 1}
                {include file='comment/map.tpl'}
            {else}
                {if !empty($featuredResults)}{include file='modules/module.featured-content.tpl'}{/if}
            {/if}            
            {* for the map's reference to the resource_id *}
            <input type="hidden" name="rsrc_id" id="hidden_resourceId" value="{$rsrc->getId()}" />
            {if !empty($relatedResults)}{include file='modules/module.related-content.tpl'}{/if}            
        </div>
        <div class="clear">&nbsp;</div>
    { else }
        {include file='modules/module.results-resource-large.tpl'}
    {/if}
{/foreach}     

{include file='comment/comment.tpl'}

<div class="clear">&nbsp;</div>

{include file='footer.tpl'}

<script type="text/javascript" src="/js/comment/commentReply.{$jsExt}{$version}"></script>
<script type="text/javascript" src="/js/comment/CommentSubmitForm.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new CommentSubmitForm('form_comment');
</script>

{if $pageNumber == 1}
    <script type="text/javascript" src="/js/lib/ResourceDetailActions.class.{$jsExt}{$version}"></script>
    <script type="text/javascript">
    	new ResourceDetailActions();
    </script>
    <script type="text/javascript" src="/js/lightbox/lightbox.{$jsExt}{$version}"></script>
{else}
    <script type="text/javascript" src="/js/lib/ResourceActions.class.{$jsExt}{$version}"></script>
    <script type="text/javascript">
    	new ResourceActions();
    </script>
{/if}

{* Only load it if we need it *}
{if $locationCheck && $pageNumber <= 1}
	<script type="text/javascript" src="/js/google/MapDisplay.class.{$jsExt}{$version}"></script>
	<script type="text/javascript">
		new MapDisplay('div_containerGoogleMap');
	</script>
{/if}

{* Only load it if we need it *}
{if $identity->mod}
	<script type="text/javascript" src="/js/lib/ModeratorActions.class.{$jsExt}{$version}"></script>
	<script type="text/javascript">
		new ModeratorActions();
	</script>
{/if}

<script type="text/javascript" src="/js/lib/wmd.{$jsExt}{$version}"></script>
{if !$smart_device}
    <script type="text/javascript" src="/js/lib/showdown.{$jsExt}{$version}"></script>
{/if}

<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="http://static.ak.fbcdn.net/connect.php/js/FB.Share"></script>