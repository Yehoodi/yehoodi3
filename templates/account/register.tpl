{include file='header.tpl' section='register' maps=false}

<h1>Register for a new Yehoodi.com Account</h1>

<div class="grid_9 text alpha">
    <form method="post" action="{geturl action='register'}" id="form_registration">    
        {if $fp->hasError()}
           <div class="error" id="div_error">
                An error has occurred in the form below. Please check
                the highlighted fields and re-submit the form.
           </div>
        {/if}
    	 <div class="fieldset" id="div_fieldsUserInfo">
            <div class="row" id="form_user_name_container">
                <label for="form_user_name">Choose a user name:</label>
                <input class="input_text" type="text" id="form_user_name" name="user_name" value="{$fp->user_name|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('user_name')}
            </div>
    
            <div class="row" id="form_email_container">
                <label for="form_email_address">Enter your E-mail Address:</label>
                <input class="input_text" type="text" id="form_email_address"
                       name="email_address" value="{$fp->email_address|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('email_address')}
            </div>
    
            <div class="row" id="form_first_name_container">
                <label for="form_first_name">First Name:</label>
                <input class="input_text" type="text" id="form_first_name"
                       name="first_name" value="{$fp->first_name|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('first_name')}
            </div>
    
            <div class="row" id="form_last_name_container">
                <label for="form_last_name">Last Name:</label>
                <input class="input_text" type="text" id="form_last_name"
                       name="last_name" value="{$fp->last_name|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('last_name')}
            </div>
    	</div>
        <div class="fieldset"> 
        	<h3>Are you human?</h3>
            <p>Sorry, we gotta ask...</p>
            <img id="img_captcha" src="/utility/captcha" alt="CAPTCHA image" />
    
            <div class="row" id="form_captcha_container">
                <label for="form_captcha"> Enter the word above:</label>
                <input class="input_text" type="text" id="form_captcha"
                       name="captcha" value="{$fp->captcha|escape}" />
                {include file='lib/error.tpl' error=$fp->getError('captcha')}
            </div>
    
            <div class="row" id="form_question_container">
                <label for="form_question">Finish this song title:</label>
                <em>&quot;It Don't Mean A Thing if it Ain't Got That...&quot;</em>
                <input class="input_text" type="text" id="form_question"
                       name="question" value="{$fp->question}" />
                {include file='lib/error.tpl' error=$fp->getError('question')}
            </div>
        </div>
        <div class="fieldset">
    		<h3>Yehoodi Membership Terms of Agreement</h3>
            <div class="row">
                <div id="terms">{include file='help/module.terms-of-agreement.tpl'}</div>            
            </div>
    	</div>
        <div class="fieldset" id="div_fieldsButtons">
            <input type="submit" class="button_submit" value="I accept. Create my account." id="button_register" />
            <input type="hidden" name="hidden_token" id="hidden_token" value="{$hidden_token}" />
        </div>
    </form>
</div>
<div class="grid_1">&nbsp;</div>
<div class="grid_6 text omega">
	{include file='help/help.register.tpl'}
</div>

<div class="clear">&nbsp;</div>

<script type="text/javascript" src="/js/account/UserRegistrationForm.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new UserRegistrationForm('registration-form');
</script>

{include file='footer.tpl'}