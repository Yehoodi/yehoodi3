Yehoodi Notification for {if $user->profile->first_name == ''}{$user->user_name}{else}{$user->profile->first_name}{/if} 
Dear {$user->user_name},

You are receiving this because you have requested to be notified by email when the topic "{$resourceTitle}" has new comments. This topic has received a comment since your last visit. You can use the following link to view the comments made.

No more notifications will be sent until you log in and visit the topic.

{$link}

You can turn off this notification by going to your account settings at {$siteURL}account/summary/watched and turning off the email notification.

Thanks,
Yehoodi staff