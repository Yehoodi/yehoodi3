<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
     <title>Edit history</title>
     <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
     <link rel="icon" type="image/png" href="/images/favicon.ico" />
     <link href="/css/yehoodi.css" media="all" rel="Stylesheet" type="text/css" />
     <style type="text/css">
        html,body {ldelim} background-color: #F0ECD7; background-image: none; {rdelim}
        body {ldelim} padding: .25em; {rdelim}
     </style> 
</head>
<body>
{ if $history }
	
	<div class="results commentLarge" style="clear: left;">
    {foreach from=$history item=com}
        {cycle values=',alt' assign='class'}
        <div class="result {$class}">
            <div class="div_commentAuthor">
                <div class="div_userAvatar">&nbsp;</div>
                <strong>{$com.user_name}</strong>
            </div>
            <div class="div_commentBody" style="width: 400px;">
                <ul class="meta"><li class="final">{$com.date_edited}</li></ul>
                <div class="div_commentText">{$com.comment}</div>
            </div>
        </div>
    {/foreach}
    </div>

{ else }
    <p><em>No comment history available.</em></p>
{/if}
</body>
</html>
