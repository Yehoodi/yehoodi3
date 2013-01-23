<table id="table_mail">
    {foreach from=$mail item=mail name=mail}
    {cycle values=',alt' assign='class'}
        <tr class="{$class}">
            <td class="td_selectMessage"><input type="checkbox" class="{if $mail->mailStatus == 2 || $mail->mailStatus == 4 }checkbox_messageSelect_new{else}checkbox_messageSelect_read{/if}" id="{$mail->thread_id}"></td>
            <td class="td_mailAuthorAvatar">
                <a href="{geturl controller = 'profile'}{$mail->withUser}"><img src="{avatarfilename id=$mail->userMeta->avatar->getId()}" alt="{$mail->withUser}" class="avatar-tiny" /></a>
            </td>
            <td class="td_mailAuthor">               	
                <a href="{geturl controller = 'profile'}{$mail->withUser}">{$mail->withUser}</a><br />
                <span class="meta">{$mail->neatMailDateTime}</span>
            </td>
            <td class="td_message">
                <a class="iconText {if $mail->mailStatus == 2 || $mail->mailStatus == 4 }iconMailLarge mail_new{else}iconMailOpenLarge{/if}" id="a_{$mail->thread_id}" href="{geturl controller='mail' action='message'}/{$mail->thread_id}">{$mail->mail_subject}</a>
                {if $mail->mailThreadCount > 1}({$mail->mailThreadCount}){/if}
            </td>  
        </tr>
    {/foreach}
</table>
