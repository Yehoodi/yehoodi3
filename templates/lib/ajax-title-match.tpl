<a href="javascript:$('div_containerTitleMatch').hide();void(0);" id="a_closeTitleMatch"><img src="/images/graphics/closelabel.gif" alt="Close" /> Close</a></span>
<p><strong class="iconText iconExclamationRed">Existing, possibly related topics:</strong></p>
<ul>
{foreach from=$resources item=rsrc name=resources}
	<li title="{$rsrc->title|escape}">
		<a href="/comment/{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}" onclick="return changeTabPrompt();">{$rsrc->title|escape}</a>
	</li>
{/foreach}
</ul>
