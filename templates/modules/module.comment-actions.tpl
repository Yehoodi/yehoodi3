{* This is a logged in user, then we show this stuff... *}
{ if $authenticated }
<ul class="actions">
     {if $identity->user_id}
        {if !$rsrc->isClosed()}
          <li class="reply">
			<a class="comment_replyToComment" id="c_{$com->getId()}" href="#div_replyForm" style="background-position: -72px -48px" title="Respond to this"></a>
		  </li>
		{/if}
     	{if $com->user_id == $identity->user_id && !$rsrc->isClosed()}
     		<li> 
				<a class="comment_edit" id="{$com->getId()}" href="{geturl controller='comment'}{$com->rsrc_id}/{$rsrcUrl}/{$pageNumber}?commentId={$com->getId()}#div_replyForm" style="background-position: 0 -48px" title="Edit"></a>
			</li>
     	{ /if }

     	{* Moderator edit *}
     	{if $identity->mod }
     		<li> 
				<a class="comment_mod" id="{$com->getId()}" href="{geturl controller='comment'}{$com->rsrc_id}/{$rsrcUrl}/{$pageNumber}?commentId={$com->getId()}#div_replyForm" style="background-position: -24px -48px" title="Moderate this"></a>
			</li>
     		<li> 
				<a class="comment_ip" id="{$com->getId()}" href="javascript:modIPWindow{$com->getId()}()" style="background-position: -120px -48px" title="I.P. address info"></a>
			</li>
			{literal}
            <script type="text/javascript">
            function modIPWindow{/literal}{$com->getId()}{literal}() {
            	modpopupWindow = window.open("{/literal}{geturl controller='moderator' action='iphistory'}/?commentId={$com->getId()}{literal}",' modpopupWindow','resizable=1,scrollbars=1,width=630,height=500');
            }
            </script>
            {/literal}

            {if $com->date_edited > '0000-00-00 00:00:00'}
     		<li> 
				<a class="comment_hist" id="{$com->getId()}" href="javascript:modRevWindow{$com->getId()}();" style="background-position: -144px -48px" title="View edit history"></a>
			</li>
            
			{literal}
            <script type="text/javascript">
            function modRevWindow{/literal}{$com->getId()}{literal}() {
            	modpopupWindow = window.open("{/literal}{geturl controller='moderator' action='commenthistory'}/?commentId={$com->getId()}{literal}",' modpopupWindow','resizable=1,scrollbars=1,width=630,height=500');
            }
            </script>
            
            {/literal}
			
            {/if}
     	{ /if }
     { /if }
</ul>
{ /if }