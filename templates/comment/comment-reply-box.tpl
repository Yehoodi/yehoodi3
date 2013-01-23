<div id="div_replyForm" {if !$fp->getError('comment')} style="display: none;"{/if}>
{if $identity}
	<form action="{geturl controller='comment'}{$id}/{$rsrcUrl}/{$pageNumber}" method="post" id="form_comment" name="formComment">
	<h2 id="h_commentHeader">Add a new comment</h2>
	<div class="fieldset" id="div_fieldsInfo">
		{*
		<div class="row" id="div_postNumberRow">
          	<label>Post Number:</label> 
			{$this->comment_num}
		</div>
		*}
		<div class="row" id="div_authorRow">
	        <label>Original Author: </label> 
			<span class="iconText iconUserSmall"><strong><span id="link_originalUserName"></span></strong></span>
		</div>
		<div class="row" id="div_excerptRow">
	          <label>Excerpt:</label> 
			<p id="p_commentText" class="post-excerpt"></p>
		</div>
		<div class="clear">&nbsp;</div>
	</div>

	<div class="row" id="div_emptyErrorRow">
	   <input type="hidden" name="commentError" />
	   {include file='lib/error.tpl' error=$fp->getError('noComment')}
	</div>
{/if}
	{include file='modules/module.wmdeditor.tpl' content=$fp->comment textarea_name = 'comment'}

{if $identity}
    {if !$smart_device}
    	<div class="fieldset results commentLarge" id="div_fieldsPreview">
            <h3 id="previewSectionTitle">Preview Your Post</h3>
    		<div class="row result" id="div_previewRow">
                <div class="div_commentAuthor">
                	<img src="{avatarfilename id=$fp->user->userMeta->avatar->getId()}" alt="" class="avatar" /><br />
                    <strong><a href="#">{$identity->user_name}</a></strong><br />
                </div>
                <div class="div_commentBody">
                    <ul class="meta">
                        <li class="iconText iconChainSmall">
                            <strong>Post ####</strong>
                        </li>		
                        <li class="iconText iconBalloonSmall">Current date</li>
                    </ul>
                    {if !$smart_device}
                        <div class="div_commentText">{include file='modules/module.wmdpreview.tpl'}</div>
                    {/if}
                </div>
    		</div>
    	</div>
    {/if}
{/if}
	<div class="fieldset" id="div_fieldsButtons">
		{if $fp->resourceComment->isSaved()}
			{assign var="label" value="Save Changes"}
		{else}
			{assign var="label" value="Post"}
		{/if}
		
		<input type="submit" value="{$label|escape}" name="formAction" class="button_submit" id="button_submit" />		

		<input type="hidden" name="id" value="{$id}" />
		<input type="hidden" name="comment_num" value="{$commentNum}" />
		<input type="hidden" id="hidden_commentId" name="commentId" value="{$commentId}" />	
		<input type="hidden" id="hidden_replyToId" name="replyToId" value="" />	
		<input type="hidden" id="hidden_pageNumber" name="pageNumber" value="{$pageNumber}" />	
		<input type="hidden" id="hidden_userId" name="userId" value="{$identity->user_id}" />
	</div>
{if $identity}
	</form>
{/if}
</div>
