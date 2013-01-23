<h2>Yehoodi Featured Topics</h2>
{if !empty($featuredResults)}
	<div class="results resourceTiny">
	{foreach from=$featuredResults item=rsrc}
	{cycle values=',alt' assign='class'}
		<div class="result {$class}">
               <a class="title" href="/comment/{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}" title="{$rsrc->title}">{$rsrc->title|truncate:30:"&hellip;"}</a>             
               <ul class="meta">
               	<li id="comments{$rsrc->meta->_id}" class="iconText iconBalloonSmall">{$rsrc->meta->numOfCommnets}</li>
               	<li id="votes{$rsrc->meta->_id}" class="iconText iconThumbSmall">{$rsrc->meta->voteNum}</li>
				<li id="views{$rsrc->meta->_id}" class="iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}</li>
               </ul>
		</div>
	{/foreach}
	</div>
{ else }
	<p><em>No featured topics found.</em></p>           
{/if}