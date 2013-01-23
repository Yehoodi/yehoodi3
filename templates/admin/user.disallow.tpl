	<div class="controlBox" style="height: 97px !important;">
        <h2>User name Disallow</h2>
         <p>
                    Here you can control usernames which will not be allowed to be used. 
                    Disallowed usernames are allowed to contain a wildcard character of *. 
                    Please note that you will not be allowed to specify any username that has already been registered. 
                    You must first delete that name then disallow it.
               </p>
    </div>
    
		<form method="post" action="{geturl action='users'}?section=disallow" id="form_disallow">
          <fieldset>
          
               { if $fp->hasError() }
               <div class="error">
               	An error has occurred in the form below. Please check
               	the highlighted fields and re-submit the form.
               </div>
               { /if }
          
               <div class="row" id="">
                    <label for="form_ban_user" class="grid_3">Disallow a user name:</label>
                    <input class="input_text" type="text" id="input_disallowUser" name="ban_user_name" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('ban_user_name') }
               </div>

               <div class="row" id="">
                    <label for="form_ban_user" class="grid_3">Remove a disallowed username:</label>
					<select name="allow_user_name[]" multiple="multiple" size="10">
						{foreach from=$userName item=usr}
							<option value="{$usr.disallow_id}">{$usr.user_name}</option>
						{/foreach}
					</select>
               </div>

               <div class="row">
                    <label class="grid_3">&nbsp;</label>
                    <input type="submit" id="button_submit" value="Submit" />
               </div>
          </fieldset>
          </form>
