<dl>
	{foreach from=$resources item=rsrc name=resources}
	{cycle values=',alt' assign='class'}	      
     <dt class="{$class}"><a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->title}</a></dt>				
          { if $rsrc->image->getId() > 0 }
          <dd class="thumbnail">
          		<img src="{resourcethumbnail id=$rsrc->image->getId()}" alt="{$rsrc->title}" id="img{$rsrc->meta->_id}" />
          </dd>						
          { /if }
          <dd class="description {$class}">
               {include file='modules/module.resource-meta.tpl'}
			{if $rsrc->meta->resourceName == 'event'} <h3>{$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}</h3> {/if}
               <p>{$rsrc->descrip|strip|truncate:255:"&hellip;":false}</p>               
			{include file='modules/module.resource-actions.tpl'}
          </dd>
	{/foreach}
</dl>
