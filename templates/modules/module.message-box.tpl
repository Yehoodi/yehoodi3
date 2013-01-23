{* Should apply a type of message to class of div, e.g. class="warning" or class="error" or class="notify" *}

{if $messages|@count > 0}
	{* We have messages! Display them! *}
	
	{foreach from=$messages item=messageArray}
	     
	     {foreach from=$messageArray key=messageType item=message}
         
            {if $messageType == 'notify'} 
                {assign var=messageIcon value='iconInfoBlue'}
            {elseif $messageType == 'warning'}
                {assign var=messageIcon value='iconExclamationRed'}
            {elseif $messageType ==  'error'}
                {assign var=messageIcon value='iconX'}
            {else}
                {assign var=messageIcon value='iconInfoWhite'}
            {/if}
	     
	     <div id="messages" class="{$messageType}">
	     {if $message|count == 1}
	     	<strong class="iconText {$messageIcon}">{$messageType|capitalize} Message:</strong> {$message.0|escape}
	     {else}
			<strong class="iconText {$messageIcon}">{$messageType|capitalize} Messages:</strong>
	     	<ul>     		
	     	{foreach from=$message item=row}
	     		<li>{$row}</li>
	     	{/foreach}
	     	</ul>
	     {/if}
	     </div>
	     
	     {/foreach}
	     
	{/foreach}

{/if}
