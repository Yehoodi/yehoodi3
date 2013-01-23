{include file='header.tpl' section='register' maps=false}

<h1>Register for a new Yehoodi.com Account</h1>

<div class="grid_8 text">
<p>
    Thank you {$user->profile->first_name|escape},
    your registration is now complete.
</p>

<ul>
    <li>Go and <strong>check your email</strong> for your new Yehoodi password. Your password has been e-mailed to you at {$user->email_address|escape}.</li>
    <li>Once you have logged in to Yehoodi for the first time, you can go to your Account Settings and change the password.</li>
    <li>You can check out our <a href="{geturl controller='help'}">FAQ</a> for lots of information for new users.</li>
    <li>Don't forget to stop by the <a href="{geturl controller='discussion' action='lounge'}/welcome-wagon/">Welcome Wagon</a> and say hello.</li>
</ul>

<p><a href="{geturl controller='account' action='login'}">Click here to continue to the Login screen.</a></p>
</div>
<div class="clear">&nbsp;</div>
{include file='footer.tpl'}