{include file='header.tpl' section='account' maps=true}

<h1>Manage your Yehoodi.com account</h1>

<div class="grid_3 alpha">{include file='account/tabs.tpl'}</div>	
<div id="div_accountDetails" class="grid_13 omega text">
    <h2>Update Your Details</h2>
    <form method="post" action="{geturl action='details'}" id="form_details">
       
        <p>
            Here you can keep Yehoodi informed about who you are and
            what you would like us to know about you.
        </p>
            
         <div class="fieldset">
            <p>
                To change your account password, enter a new password below.
                If you leave this field blank your password will remain
                unchanged.
            </p>        
            
            {if $fp->hasError()}
            <div class="error">
            An error has occurred in the form below. Please check
            the highlighted fields and re-submit the form.
            </div>
            {/if}
        
            <div class="row" id="row_email_address">
                <label>E-mail Address:</label>
                <span>{$fp->email_address|escape}</span>
                {include file='lib/error.tpl' error=$fp->getError('email')}
                <br /><input type="checkbox" id="form_utilize_email"
                name="utilize_email" value="1" {if $fp->utilize_email|escape == 1}checked="checked"{/if} />
                {include file='lib/error.tpl' error=$fp->getError('utilize_email')}
                Visible to others?
            </div>	   
            
            <div class="row" id="">
                <label for="form_first_name">First Name:</label>
                <input class="input_text" type="text" id="form_first_name"
                name="first_name" value="{$fp->first_name|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('first_name')}
            </div>
            
            <div class="row" id="">
                <label for="form_last_name">Last Name:</label>
                <input class="input_text" type="text" id="form_last_name"
                name="last_name" value="{$fp->last_name|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('last_name')}
            </div>
            
            <div class="row" id="">
            <label for="form_new_password">Password:</label>
            <input class="input_text" type="password" id="form_new_password"
            name="password" value="{$fp->password|escape}" />
            {include file='lib/error.tpl' error=$fp->getError('password')}
            </div>
            
            <div class="row" id="">
            <label for="form_new_password_confirm">Retype Password:</label>
            <input class="input_text" type="password" id="form_new_password_confirm"
            name="password_confirm" value="{$fp->password_confirm|escape}" />
            {include file='lib/error.tpl' error=$fp->getError('password_confirm')}
            </div>
            
            <div class="row" id="">
            <label for="form_hometown">Hometown:</label>
            <input class="input_text" type="text" id="form_hometown"
            name="hometown" value="{$fp->hometown|escape}" />
            {include file='lib/error.tpl' error=$fp->getError('hometown')}
            </div>
            
            <div class="row" id="">
            <label for="form_first_name">Website:</label>
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
            <label for="form_birthdate">Birthdate:</label>
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
                <label for="form_gender">Lindy Role:</label>	   
                <select name="gender" id="form_gender">
                 <option value="0" {if $fp->gender|escape == 0}selected="selected"{/if}>Not Specified</option>
                 <option value="1" {if $fp->gender|escape == 1}selected="selected"{/if}>Lead</option>
                 <option value="2" {if $fp->gender|escape == 2}selected="selected"{/if}>Follow</option>
                </select>
                {include file='lib/error.tpl' error=$fp->getError('gender')}
            </div>
            
            <div class="row" id="">
                <label for="form_occupation">Occupation:</label>
                <input class="input_text" type="text" id="form_occupation" name="occupation" value="{$fp->occupation|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('occupation')}
            </div>
            
            <div class="row" id="">
                <label for="form_interests">Interests:</label>
                <input class="input_text" type="text" id="form_interests"
                name="interests" value="{$fp->interests|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('interests')}
            </div>
            
            <div class="row" id="">
                <label for="form_sig">Signature:</label>
                <textarea class="input_text" id="form_sig" name="sig">{$fp->signature}</textarea>
                {include file='lib/error.tpl' error=$fp->getError('sig')}
                <br />Currently the following HTML code tags work in signatures: <code>&lt;a&gt;</code>, <code>&lt;b&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code> and <code>&lt;i&gt;</code>.  At this time, we do not support images in your signature.
            </div>
            
            <div class="row" id="row_signaturePreview">
                <label>Preview:</label>
                <span class="span_signature">{$fp->signature}</span>
            </div>
            
            <div class="row" id="">
                <label for="form_bio">Bio:</label>
                <textarea class="input_text" id="form_bio" name="bio">{$fp->bio|escape}</textarea>
                {include file='lib/error.tpl' error=$fp->getError('bio')}
            </div>
        </div>
        
        <div class="fieldset" id="div_fieldsButtons">
             <input type="submit" id="button_account" class="button_submit" value="Save Details" />
        </div>
    </form>		
</div>	
<div class="clear">&nbsp;</div>

{include file='footer.tpl'}