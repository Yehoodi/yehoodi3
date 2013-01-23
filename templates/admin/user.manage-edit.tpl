	<div class="controlBox" style="height: 107px !important;">
        <h2>User: {$fp->user->user_name}</h2>
        <ul>
            <li style="padding-left: 0;">ID: {$fp->user->getId()}</li>
            <li>First visit date: {$fp->user->joinedDate}</li>
            <li>Last date logged in: {$fp->user->date_last_active}</li>
            <li style="border-right: none;">Last IP address: {$fp->user->getIPAddress()}</li>
        </ul>
        <ul id="ul_userActivity">
            <li style="padding-left: 0;"><strong>Topics:</strong> {$submits} [<a href="{geturl controller='search'}?type=resource&user={$fp->user->user_name|escape}">See All</a>]</li>
            <li style="border-right: none;"><strong>Comments:</strong> {$comments} [<a href="{geturl controller='search'}?type=comment&user={$fp->user->user_name|escape}">See All</a>]</li>
        </ul>
    </div>
		<form method="post" action="{geturl action='users'}?section=manage_edit&user_id={$fp->user->getId()}" id="form_manage">
          <fieldset>
               { if $fp->hasError() }
               <div class="error">
               	An error has occurred in the form below. Please check
               	the highlighted fields and re-submit the form.
               </div>
               { /if }

               	<legend>Admin Only User Fields:</legend>
				<div class="row" id="">
                    <label for="form_manage_user" class="grid_3">User Name:</label>
                    <input class="input_text" type="text" id="input_userName" name="user_name" value="{$fp->user->user_name}" readonly="readonly" />
                    { include file='lib/error.tpl' error=$fp->getError('user_name') }
               </div>

				<div class="row" id="">
                    <label for="form_manage_user" class="grid_3">Change User Name to:</label>
                    <input class="input_text" type="text" id="input_userNameChange" name="user_name_change" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('user_name_change') }
               </div>

               <div class="row" id="">
                    <label for="form_manage_user" class="grid_3">User Level:</label>
					<select name="select_userLevel">
						<option value="1" {if $fp->user->user_type == 1}selected{/if}>Administrator</option>
						<option value="2" {if $fp->user->user_type == 2}selected{/if}>Member</option>
						<option value="3" {if $fp->user->user_type == 3}selected{/if}>Guest (Not used)</option>
					</select>
                    { include file='lib/error.tpl' error=$fp->getError('select_userLevel') }
               </div>

               <div class="row" id="">
                    <label for="form_manage_user" class="grid_3">Moderator:</label>
					<select name="select_mod">
						<option value="true" {if $fp->user->profile->mod == "true"}selected{/if}>Yes</option>
						<option value="" {if !$fp->user->profile->mod}selected{/if}>No</option>
					</select>
                    { include file='lib/error.tpl' error=$fp->getError('select_mod') }
               </div>

               <div class="row" id="">
                    <label for="form_manage_user" class="grid_3">Set new password:</label>
                    <input class="input_text" type="text" id="input_userPassword" name="admin_password" value="" />
                    { include file='lib/error.tpl' error=$fp->getError('admin_password') }
               </div>

               <div class="row" style="margin-bottom: 2em;" id="">
                    <label for="form_manage_user" class="grid_3">User is Active:</label>
					<select name="select_userActive">
						<option value="0" {if $fp->user->is_active == 0}selected{/if}>NO (User Deactivated)</option>
						<option value="1" {if $fp->user->is_active == 1}selected{/if}>YES (Active user)</option>
					</select>
                    { include file='lib/error.tpl' error=$fp->getError('select_userActive') }
               </div>

<!--BASIC-->
               	<legend>Basic User Fields:</legend>

               	<div class="row" id="row_email_address">
                    <label class="grid_3">E-mail Address:</label>
                    <input class="input_text" type="text" id="input_emailAddress" name="email_address" value="{$fp->user->email_address}" readonly="readonly"/>
                    {include file='lib/error.tpl' error=$fp->getError('email_address')}<br />
                    <input type="checkbox" id="form_utilize_email"
                    	name="utilize_email" value="1" {if $fp->utilize_email|escape == 1}checked="checked"{/if} />
                    {include file='lib/error.tpl' error=$fp->getError('utilize_email')}
                    Visible to others?
               </div>	   

               <div class="row" id="row_email_address">
                    <label class="grid_3">Change E-mail Address to:</label>
                    <input class="input_text" type="text" id="input_emailAddressChange" name="email_address_change" value="" />
                    {include file='lib/error.tpl' error=$fp->getError('email_address_change')}
               </div>	   
          
               <div class="row" id="">
                    <label for="form_first_name" class="grid_3">First Name:</label>
                    <input class="input_text" type="text" id="form_first_name"
                    	name="first_name" value="{$fp->first_name|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('first_name')}
               </div>
          
               <div class="row" id="">
                    <label for="form_last_name" class="grid_3">Last Name:</label>
                    <input class="input_text" type="text" id="form_last_name"
                    	name="last_name" value="{$fp->last_name|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('last_name')}
               </div>
                    
               <div class="row" id="">
                    <label for="form_hometown" class="grid_3">Hometown:</label>
                    <input class="input_text" type="text" id="form_hometown"
                    	name="hometown" value="{$fp->hometown|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('hometown')}
               </div>
          
               <div class="row" id="">
                    <label for="form_first_name" class="grid_3">Website:</label>
                    <input class="input_text" type="text" id="form_website"
                    	name="website" value="{$fp->website|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('website')}
               </div>
          
               <div class="row" id="">
               {if $fp->birthdate > 0}
               		{assign var='userBirthdate' value=$fp->birthdate}
               {else}
               		{assign var='userBirthdate' value='0000-00-00'}
               {/if}
                    <label for="form_birthdate" class="grid_3">Birthdate:</label>
                    {html_select_date 
						prefix =			'birthDate'
						day_extra =			'id=select_birthDateDay'
						month_extra =		'id=select_birthDateMonth'
						year_extra =		'id=select_birthDateYear'
						day_value_format =	%02d
						time =				$userBirthdate
						reverse_years =		true
						start_year =		1920
						end_year =			2009
						year_empty =		'----'
						month_empty =		'--'
						day_empty =			'--'
                    }
                    {include file='lib/error.tpl' error=$fp->getError('birthDate')}
               </div>
          
               <div class="row" id="">
                    <label for="form_gender" class="grid_3">Gender:</label>	   
                    <select name="gender" id="form_gender">
                         <option value="1" {if $fp->gender|escape == 1}selected="selected"{/if}>Male</option>
                         <option value="0" {if $fp->gender|escape == 0}selected="selected"{/if}>Female</option>
                    </select>
                    {include file='lib/error.tpl' error=$fp->getError('gender')}
               </div>
          
               <div class="row" id="">
                    <label for="form_occupation" class="grid_3">Occupation:</label>
                    <input class="input_text" type="text" id="form_occupation"
					name="occupation" value="{$fp->occupation|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('occupation')}
               </div>
          
               <div class="row" id="">
                    <label for="form_interests" class="grid_3">Interests:</label>
                    <input class="input_text" type="text" id="form_interests"
                    	name="interests" value="{$fp->interests|escape}" />
                    {include file='lib/error.tpl' error=$fp->getError('interests')}
               </div>
     	    
               <div class="row" id="">
                    <label for="form_sig" class="grid_3">Signature:</label>
                    <textarea class="input_text" id="form_sig" name="sig">{$fp->sigRaw}</textarea>
                    <div>Preview:{$fp->signature}</div>
                    {include file='lib/error.tpl' error=$fp->getError('sig')}
               </div>
		    
               <div class="row" style="margin-bottom: 2em;" id="">
                    <label for="form_bio" class="grid_3">Bio:</label>
                    <textarea class="input_text" id="form_bio" name="bio">{$fp->bio|escape}</textarea>
                    {include file='lib/error.tpl' error=$fp->getError('bio')}
               </div>
<!--END BASIC-->               
<!--SETTINGS-->
               <legend>Site Settings</legend>

               <div class="row" id="row_notify_by_email">
	               <label class="grid_3">Email notifications:</label>               
	               <input type="checkbox" id="form_notify_by_email" name="notify_by_email" value="true" {if $fp->notify_by_email|escape == "true"}checked="checked"{/if} />
	               	{include file='lib/error.tpl' error=$fp->getError('notify_by_email')}
               </div>
     	         
               <div class="row" id="row_filter_dirty" style="margin-bottom: 2em;">
                    <label class="grid_3">Remove dirty word filter:</label>
                    <input type="checkbox" id="form_filter_dirty"
                    	name="filter_dirty" value="true" {if $fp->filter_dirty|escape == "off"}checked="checked"{/if} />
                    {include file='lib/error.tpl' error=$fp->getError('filter_dirty')}
               </div>	   
          
               <legend>Local Event Settings</legend>
               
               <div class="row" id="">
                    <label for="form_location" class="grid_3">Location:</label>
                    (Not changeable directly)<br />
                    <input class="input_text" readonly="readonly" type="text" id="form_location" name="location" value="{$fp->location|escape}" /><br />
                    {include file='lib/error.tpl' error=$fp->getError('location')}
               </div>
			
               <div class="row" id="">
                    <label for="form_unit" class="grid_3">Unit:</label>	   
                    <select name="unit" id="form_unit">
                         <option value="mi" {if $fp->unit|escape == 'mi'}selected="selected"{/if}>Miles</option>
                         <option value="km" {if $fp->unit|escape == 'km'}selected="selected"{/if}>Kilometers</option>
                    </select>
                    {include file='lib/error.tpl' error=$fp->getError('unit')}
               </div>
			
               <div class="row" style="margin-bottom: 2em;" id="">
                    <label for="form_distance" class="grid_3">Distance:</label>	   
                    <select name="distance" id="form_distance">
                         <option value="short" {if $fp->distance|escape == 'short'}selected="selected"{/if}>Short (about a 15 minute drive)</option>
                         <option value="medium" {if $fp->distance|escape == 'medium'}selected="selected"{/if}>Medium (about a 30 minute drive)</option>
                         <option value="long" {if $fp->distance|escape == 'long'}selected="selected"{/if}>Long (about an hour drive)</option>
                    </select>
                    {include file='lib/error.tpl' error=$fp->getError('unit')}
               </div>
			

<!--END SETTINGS-->

               <div class="row">
                    <label class="grid_3">&nbsp;</label>
                    <input type="submit" id="button_submit" value="Edit User" />
               </div>
          </fieldset>
          </form>
