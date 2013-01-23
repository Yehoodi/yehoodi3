<div id="div_replyForm">
	<h2 id="h_messageHeader">Enter a Message</h2>
	
	<div class="fieldset" id="div_fieldsCommon">
		<div class="row" id="div_recipientRow">
               <label>To:</label>               
               {* If there is a $threadId we are in a reply mode, disable editing the recipient.*}
	           <input type="text" class="input_text" name="recipient" id="input_recipient" value="{if $fp->recipient}{$fp->recipient}{else}{$recipient}{/if}" {if $threadId}readonly="readonly"{/if} />
               {include file='lib/error.tpl' error=$fp->getError('mail')}
		</div>
		<div class="row" id="div_subjectRow">
               <label>Subject:</label>  
               <input type="text" class="input_text" name="mail_subject" id="input_subject" value="{$fp->mail_subject|escape}" />
               <div class="inlineHelp">Changing the subject will start a new message entry</div>
               {include file='lib/error.tpl' error=$fp->getError('mail_subject')}
		</div>
	</div>	

	{include file='modules/module.wmdeditor.tpl' content=$fp->mail_body textarea_name = 'mail_body'}

{if !$smart_device}
	<div class="fieldset results commentLarge" id="div_fieldsPreview">
        <h3 id="previewSectionTitle">Preview Your Message</h3>
		<div class="row result" id="div_previewRow">
			<div class="div_commentAuthor">
            	<img src="{avatarfilename id=$fp->user->userMeta->avatar->getId()}" alt="" class="avatar" /><br />
                <strong><a href="#">{$identity->user_name}</a></strong><br />
            </div>
            <div class="div_commentBody">
                <ul class="meta">	
                    <li class="final iconText iconBalloonSmall">Current time</li>
                </ul>
                {if !$smart_device}
                    <div class="div_commentText">{include file='modules/module.wmdpreview.tpl'}</div>
                {/if}
            </div>
		</div>
	</div>
{/if}
	<div class="fieldset" id="div_fieldsButtons">
		{assign var="label" value="Send Message"}
		
		<input type="submit" value="{$label|escape}" class="button_submit" name="formAction" id="button_submit" />		

		<input type="hidden" name="threadId" value="{$threadId}" />
		<input type="hidden" id="hidden_recipient" name="recipientId" value="{$recipientId}" />
	</div>
</div>