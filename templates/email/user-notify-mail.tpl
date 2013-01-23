Yehoodi Mail Notification for {if $recipientFirstName == ''}{$recipientUserName}{else}{$recipientFirstName}{/if} 
Dear {$recipientUserName},

Member {$user->user_name} from Yehoodi.com just sent you a new mail message under subject, "{$subject}" to your account, and you have requested that you be notified on this event.

Here is the text of the message:

{$text|markdown|resourcefilter|strip}

---
Please note that any formatting and images in the message have been stripped for email friendly delivery. You can view the full formatted message by clicking on the following link: 

{$link}

Thanks,
Yehoodi Staff