{include file='header.tpl' section='account' maps=true}

<h1>Manage your Yehoodi.com account</h1>

<div class="grid_3 alpha">{include file='account/tabs.tpl'}</div>	
<div id="div_accountDetails" class="grid_13 omega text">
    <h2>Site Settings</h2>
    <form method="post" action="{geturl action='settings'}" id="form_settings">
    
        {if $fp->hasError()}
        <div class="error">
        An error has occurred in the form below. Please check
        the highlighted fields and re-submit the form.
        </div>
        {/if}

        <div class="fieldset" id="div_fieldsGeneral">
            <h3>General Site Settings</h3>
            <p>Here you can tailor Yehoodi to a configuration that is perfect for you.</p>
            
            <div class="row" id="row_notify_by_email">
                <label>Email notifications:</label>               
                <input type="checkbox" id="form_notify_by_email"
                name="notify_by_email" value="true" {if $fp->notify_by_email|escape == "true"}checked="checked"{/if} />
                {include file='lib/error.tpl' error=$fp->getError('notify_by_email')}            
                    Checking this box does two things: 
                    <div class="inlineHelp">
                    <ol>
                        <li>It sends an email to your <strong>{$fp->email_address|escape}</strong> address when someone on
                        Yehoodi sends you a Yehoodi <a href="{geturl controller='mail'}">Message</a>.</li>
                        <li>It sets the &quot;Email me...&quot; checkbox to default to ON for any topic you submit.</li>
                    </ol>
                </div>
            </div>
            
            <div class="row" id="row_filter_dirty">
                <label>Remove dirty word filter:</label>
                <input type="checkbox" id="form_filter_dirty" name="filter_dirty" value="true" {if $fp->filter_dirty|escape == "off"}checked="checked"{/if} />
                {include file='lib/error.tpl' error=$fp->getError('filter_dirty')}
                Check this box to remove the dirty word filter (no more [bleep!]).
            </div>	   
            
            <div class="row" id="row_user_invisible">
                <label>Make me invisible:</label>
                <input type="checkbox" id="form_user_invisible" name="user_invisible" value="true" {if $fp->user_invisible|escape == "on"}checked="checked"{/if} />
                {include file='lib/error.tpl' error=$fp->getError('user_invisible')}
                Don't show my online status on the home page, profile, comments or anywhere to other users (you will appear offline).
            </div>
        </div>
        
        <div class="fieldset" id="div_fieldsLocal">
            <h3>Local Event Settings</h3>
            <p>These settings will allow Yehoodi to find events that are local to you!</p>
            
            <div class="row" id="row_location">
                <label for="form_location">Home Location:</label>
                <input class="input_text" type="text" id="form_location"
                name="location" value="{$fp->location|escape}" /><br />
                {include file='lib/error.tpl' error=$fp->getError('location')}
                <p class="inlineHelp">
                    Enter at least a city and state to tell Yehoodi what events you would like to see.<br />
                    For example, enter &quot;412 Eighth Avenue, New York, NY&quot; or you can just enter &quot;Brooklyn, NY&quot;.<br />
                    Leave this blank to disable this feature.
                </p>
            </div>
            
            <div class="row" id="">
                <label for="form_unit">Unit:</label>	   
                <select name="unit" id="form_unit">
                <option value="mi" {if $fp->unit|escape == 'mi'}selected="selected"{/if}>Miles</option>
                <option value="km" {if $fp->unit|escape == 'km'}selected="selected"{/if}>Kilometers</option>
                </select>
                {include file='lib/error.tpl' error=$fp->getError('unit')}
                <p class="inlineHelp">Events will show an estimated distance from this location to the event location in this format.</p>
            </div>
            
            <div class="row" id="">
                <label for="form_distance">Distance:</label>	   
                <select name="distance" id="form_distance">
                <option value="short" {if $fp->distance|escape == 'short'}selected="selected"{/if}>Short (about a 90 minute drive)</option>
                <option value="medium" {if $fp->distance|escape == 'medium'}selected="selected"{/if}>Medium (up to about a 2-3 hour drive)</option>
                <option value="long" {if $fp->distance|escape == 'long'}selected="selected"{/if}>Long (up to about a 5 hour drive)</option>
                </select>
                {include file='lib/error.tpl' error=$fp->getError('unit')}
                <p class="inlineHelp">How far would you like Yehoodi to determine that an event is "local".</p>
            </div>
        </div>
        
        <div class="fieldset" id="div_fieldsButtons">
            <input type="submit" class="button_submit" id="button_settings" value="Save Settings" />
        </div>
    </form>	
</div>	
<div class="clear">&nbsp;</div>

	<script type="text/javascript" src="/js/account/Location.class.{$jsExt}{$version}"></script>
	<script type="text/javascript">
		new Location('form_settings');
	</script>

{include file='footer.tpl'}