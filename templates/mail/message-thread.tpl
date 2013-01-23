{if $threadId} 
    <h1>
        <a class="buttonLink" href="{geturl controller='mail'}">&laquo; Back to Mailbox</a>
        <span class="iconText iconMailOpenLarge">{$mailSubject}</span> 
    </h1>
    {include file='modules/module.pagination.tpl'}
    {$mailMessage->mailBody->mail_subject}
{else}
    <p><a class="buttonLink" href="{geturl controller='mail'}">&laquo; Back to Mailbox</a></p>
    <div class="clear">&nbsp;</div>
{/if}

<div class="results commentLarge" style="clear: left;">
    {foreach from=$mailMessage item=mail name=mail}
	{cycle values=',alt' assign='class'} 
        <div class="result {$class}" id="message_{$mail->getId()}">
            <div class="div_commentAuthor">
    	        {include file='modules/module.user-avatar-meta.tpl' userMeta=$mail->userMeta mailMeta=$mail->meta author=$mail->author}
            </div>
            <div class="div_commentBody">
                <ul class="meta">
                    <li class="iconText iconBalloonSmall">Posted {$mail->neatMailDateTime}</li>
                </ul>
                <div class="div_commentText">{$mail->mailBody->mail_body|markdown}</div> 
            </div>
        </div>
    {/foreach}
</div>
{if $threadId} 
    {include file='modules/module.pagination.tpl'}
{/if}
{include file='mail/mail-reply-box.tpl'}
