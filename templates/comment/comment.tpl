{ if $totalResults > 0 }

    <div class="div_pageControl div_pageControlTop grid_16">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'} 
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->
    
    <div class="results commentLarge" style="clear: left;">
        {foreach from=$comments item=com name=comments}
     		{cycle values=',alt' assign='class'}
            
     		{if $com->ignored == true}
         		<div id="div_ignoredComment_{$com->comment_num}" class="result div_ignoredComment">
                    <div class="div_commentAuthor">
         			    <strong><a href="{geturl controller = 'profile'}{$com->meta->postedBy}">{$com->meta->postedBy}</a></strong>
                    </div>
                    <div class="div_commentBody">
         			    You are ignoring this user. <em><a class="comment_ignore" id="{$com->comment_num}">Read this comment (Post #{$com->comment_num})</a></em>
                    </div>
         		</div>
     		{/if}               
            
	        <div class="result {$class}" id="comment_{$com->comment_num}" {if $com->ignored == true }style="display: none;"{/if}>
	        
                <div class="div_commentAuthor">
        	        {include file='modules/module.user-avatar-meta.tpl' userMeta=$com->userMeta mailMeta=$com->meta}
                </div>
	        
	            <div id="div_{$com->getId()}_comment" class="div_commentBody">
                    <ul class="meta">
                        <li class="iconText iconChainSmall">
                            <a href="{$location}{$controller}/{$id}/{$rsrcUrl}/{$pageNumber}/#comment_{$com->comment_num}">
                            Post #{$com->comment_num}
                            </a>
                        </li>								
                        {* This shows original posting date or date of last edit *}
                        <li class="{if $com->date_edited == '0000-00-00 00:00:00'}final {/if}iconText iconBalloonSmall">Originally posted {$com->meta->neatPostedDate} ({$com->meta->relativeDate})</li>
                        {if $com->date_edited > '0000-00-00 00:00:00'}
                            <li class="iconText iconBalloonPencilSmall">Edited on {$com->getEditDate()}</li>
                        {/if}
                    </ul>
                    {include file='modules/module.comment-actions.tpl'}
                     <div class="comment_summary summary">
                          {if $com->reply_to_id > 0}
                          	<div class="quote-source"><em>Response to {$com->replyToUser} in post #{$com->replyToPostNum}</em> <a class="tags parent_comment_reference" onclick="showCommentReply(this,{$com->replyToLink}); return false;" href="{geturl controller='comment'}{$com->rsrc_id}/{$rsrcUrl}/{$pageNumber}#comment_{$com->comment_num}">Show</a></div>
                          {/if}
                     </div>
                    <div class="thread"> </div>
                    <div class="div_commentText">{$com->comment|markdown}</div>                              
                    {if $com->userMeta->signature}<span class="span_signature"><p>{$com->userMeta->signature}</p></span>{/if}
                </div>
            </div>             
        
        {/foreach}
    </div>
    <div class="div_pageControl grid_16">
        {include file='modules/module.pagination.tpl'}
        {include file='modules/module.add-link.tpl'}
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->
{ else }

    <div class="div_pageControl grid_16">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'} 
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->
    
{/if}

{include file='comment/comment-reply-box.tpl'}