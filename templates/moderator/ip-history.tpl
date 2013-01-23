<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
     <title>IP History for comment #{$comment->meta->_id}</title>
     <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
     <link rel="icon" type="image/png" href="/images/favicon.ico" />
     <link href="/css/yehoodi.css" media="all" rel="Stylesheet" type="text/css" />
     <style type="text/css">
        html,body {ldelim} background-color: #F0ECD7; background-image: none; {rdelim}
        body {ldelim} padding: 1em; {rdelim}
     </style>
</head>
<body>
{if $postNum}
    <h1>IP Address for comment #{$comment->meta->_id}</h1>
    <h2>{$ip} [ {$postNum} Post{if $postNum > 1}s{/if} ]</h2>
    <hr />
    <p>Users Posting from this IP address:</p>
    <ul>
    {foreach from=$userNames item=users}
        <li>{$users.user_name}</li>
    {/foreach}
    </ul>
    
    <p>Other IP addresses this user has posted from:</p>
    <ul>
    {foreach from=$postedIPs item=ip}
        <li>{$ip}</li>
    {/foreach}
    </ul>
{else}
    <p><em>No IP history available.</em></p>
{/if}
</body>
</html>