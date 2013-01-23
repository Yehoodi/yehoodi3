<h2>Related Topics</h2>
{if !empty($relatedResults)}
	<div class="results resourceTiny">
	{foreach from=$relatedResults item=rsrc}
	{cycle values=',alt' assign='class'}
		<div class="result {$class}">
               <a class="title" href="/comment/{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}" title="{$rsrc->title}">{$rsrc->title|truncate:32:"&hellip;"}</a>             
               <ul class="meta">
               	<li id="comments{$rsrc->meta->_id}" class="iconText iconBalloonSmall">{$rsrc->meta->numOfCommnets}</li>
               	<li id="votes{$rsrc->meta->_id}" class="iconText iconThumbSmall">{$rsrc->meta->voteNum}</li>
				<li id="views{$rsrc->meta->_id}" class="iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}</li>
               </ul>
		</div>
	{/foreach}
	</div>
{ else }
	<p><em>No related topics found.</em></p>           
{/if}