	<div class="controlBox" style="height: 127px !important;">
        <h2>Site Ban</h2>
         <p>
					Here you can control the banning of users. You can achieve this by banning either 
					or both of a specific user or an individual or range of IP addresses or hostnames. 
					These methods prevent a user from even reaching the index page of your board. To 
					prevent a user from registering under a different username you can also specify a 
					banned email address. Please note that banning an email address alone will not prevent 
					that user from being able to log on or post to your board. You should use one of 
					the first two methods to achieve this.
               </p>
    </div>
    
		<form method="post" action="{geturl action='users'}?section=ban" id="form_ban">
          <fieldset>
          
               { if $fp->hasError() }
               <div class="error">
               	An error has occurred in the form below. Please check
               	the highlighted fields and re-submit the form.
               </div>
               { /if }
          
              
          
               <div class="row" id="">
                    <label for="form_ban_user" class="grid_3">Ban a specific user:</label>
                    <input class="input_text" type="text" id="input_banUser"
                    	name="user_name" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('user_name') }
               </div>

               <div class="row" id="">
                    <label for="form_ban_user" class="grid_3">Un-ban user(s):</label>
					<select size="10" name="unban_user_name[]" multiple="multiple" >
						{foreach from=$userName item=usr}
							<option value="{$usr.ban_id}">{$usr.user_name}</option>
						{/foreach}
					</select>
               </div>

               <div class="row" id="">
                    <label for="form_email_address" class="grid_3">Ban a specific email address:</label>
                    <input class="input_text" type="text" id="input_banEmail"
                    	name="email_address" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('email_address') }
               </div>

               <div class="row" id="">
                    <label for="form_ban_email_address" class="grid_3">Un-ban email(s):</label>
					<select size="10" name="unban_email_address[]" multiple="multiple" >
						{foreach from=$emailAddress item=email}
							<option value="{$email.ban_id}">{$email.email_address}</option>
						{/foreach}
					</select>
               </div>

               <div class="row" id="">
                    <label for="form_remote_ip" class="grid_3">Ban a specific IP address:</label>
                    <input class="input_text" type="text" id="input_banIP"
                    	name="remote_ip" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('remote_ip') }
               </div>

               <div class="row" id="">
                    <label for="form_remote_ip" class="grid_3">Un-ban IP(s):</label>
					<select size="10" name="unban_remote_ip[]" multiple="multiple" >
						{foreach from=$remoteIP item=ip}
							<option value="{$ip.ban_id}">{$ip.remote_ip}</option>
						{/foreach}
					</select>
               </div>

               <div class="row">
                    <label class="grid_3">&nbsp;</label>
                    <input type="submit" id="button_submit" value="Submit" />
               </div>
          </fieldset>
          </form>
