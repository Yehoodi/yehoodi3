{if $user->profile->first_name == ''}{$user->user_name}{else}{$user->profile->first_name}{/if}, Your Account Password
Dear {$user->user_name},

You recently requested a password reset as you had forgotten your password.

Your new password is listed below. To activate this password, click this link:

    Activate Password: {$siteUrl}account/fetchpassword?action=confirm&id={$user->getId()}&key={$user->profile->new_password_key}
    Username: {$user->user_name}
    New Password: {$user->_newPassword}

If you didn't request a password reset, please ignore this message and your password
will remain unchanged.

Sincerely,

Yehoodi staff