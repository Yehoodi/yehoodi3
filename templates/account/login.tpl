{include file='header.tpl' section='home' maps=false}

<h1>Log In to Your Yehoodi Account</h1>

<div class="grid_8 alpha">
    <form method="post" action="{geturl controller='account' action='login'}" id="form_login"> 
        <div class="fieldset" id="div_fieldsUserInfo">
            <div class="row" id="row_username">
                <label for="form_user_name">Username:</label>
                <input class="input_text" type="text" id="form_user_name"
                name="user_name" value="" />
                {include file='lib/error.tpl' error=$errors.user_name}
            </div>
            
            <div class="row" id="">
                <label for="form_password">Password:</label>
                <input class="input_text" type="password" id="form_password"
                name="password" value="" />
                {include file='lib/error.tpl' error=$errors.password}
                <p class="inlineHelp"><a href="{geturl action='fetchpassword'}">Forgotten your password?</a></p>
            </div>
            
            <div class="row text" id="">
                <label for="form_remember">Remember Me:</label>
                <input type="checkbox" id="form_remember" name="remember" value="1" />
            </div>
        </div>
        
        <div class="fieldset" id="div_fieldsButtons">
            <input type="submit" value="Login" class="button_submit" id="button_login" />
            <input type="hidden" name="redirect" value="{$redirect|escape}" />
        </div>
    </form>
</div>

<div class="grid_8 text omega">
	{include file='help/help.login-generic.tpl'}
</div>


<div class="clear">&nbsp;</div>

<script type="text/javascript" src="/js/account/LoginForm.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new LoginForm();
</script>
<noscript>
	<p>Sorry, Javascript is required for this site.</p>
</noscript>

{include file='footer.tpl'}