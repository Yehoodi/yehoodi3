<table id="tableMail">
	<thead>
		<tr><th></th><th>Author/Recipient</th><th>Subject/Time</th></tr>
	</thead>		
	<tbody>			
		{foreach from=$mail item=mail name=mail}
		{cycle values=',alt' assign='class'}
		<tr class="{$class}">
			<td class="selectMessage"><input type="checkbox"></td>
               <td class="message">
				<p><a class="mail-new" href="">{$mail->mail_subject}</a> <span class="mostRecent">{$mail->mail_date}</span></p>
			</td>        
        </tr>
			<p></p>
		{/foreach}
	</tbody>
</table>
