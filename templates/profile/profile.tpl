 <h1 class="iconText iconUser">{$member->user_name|escape}
	{if $authenticated == true} 
		{if $identity->user_id != $member->getId()}
		<ul class="actions">
     		{*
			<li id="li_friend_">
          		<a href="#" class="user-friend" id="" style="background-position: -144px 0;" title="Add to Friends"></a>
          	</li>
			*}
     		<li id="li_ignore_">
				{* Ignored? *}
				{if $member->ignore}
               		<a href="javascript:void(0);" class="user-ignore" id="a_ignore_{$member->getId()}" style="background-position: -120px -24px" title="Stop ignoring {$member->user_name}'s Comments"></a>
               	{else}
               		<a href="javascript:void(0);" class="user-ignore" id="a_ignore_{$member->getId()}" style="background-position: -120px 0;" title="Ignore {$member->user_name}'s Comments"></a>
               	{/if}
          	</li>
			<li id="li_mail_">
          		<a href="{geturl controller='mail' action='message'}?recipient={$member->user_name}" class="user-mail" id="" style="background-position: -96px -48px;" title="Send mail to {$member->user_name}"></a>
          	</li>
     	</ul>
        {/if}
	{/if}
</h1>

<div class="grid_2 div_userAvatar alpha">
	<img class="avatar-large" src="{avatarfilename id=$userMeta->avatar->getId()}" alt="{$member->user_name}" />
</div>
<div class="grid_14 omega text">
	{if $member->signature}
		{$member->signature}
	{/if}
	{if $member->profile->bio}
     	<p id="p_bio">
               <strong>Bio:</strong>
               {$member->profile->bio}
          </p>
	{/if}
     <div class="grid_7 alpha">
		{if $member->profile->first_name || $member->profile->hometown || $member->profile->gender || $birthdate || $member->profile->occupation || $member->profile->interests }
     	<h2>Basic Information</h2>		
     	<table class="table_profileInfo">
            {if $userMeta->online}
                <tr><th>Status:</th><td class="iconText iconStatusGreen">Online</td></tr>
		    {/if}
     		{if $member->profile->first_name}
     			<tr><th>Real Name:</th><td>{$member->profile->first_name|escape} {$member->profile->last_name|escape}</td></tr>
     		{/if}
     		{if $member->profile->hometown}
     			<tr><th>Hometown:</th><td>{$member->profile->hometown|escape}</td></tr>
     		{/if}
     		{if $member->profile->gender}
     			<tr>
     				<th>Lindy Role:</th>
     				<td>
     					{if $member->profile->gender == 1} 
          					Leader
     		          	{elseif $member->profile->gender == 2} 
     						Follower
     		          	{/if}
     				</td>
     			</tr>
     		{/if}
     		{if $birthdate}
     			<tr><th>Birthdate:</th><td>{$birthdate|escape}</td></tr>
     		{/if}
     		{if $member->profile->occupation}
     			<tr><th>Occupation:</th><td>{$member->profile->occupation|escape}</td></tr>
     		{/if}
     		{if $member->profile->interests}
     			<tr><th>Interests:</th><td>{$member->profile->interests|escape}</td></tr>
     		{/if}
     	</table>
		{/if}
		{if $member->profile->website || $member->profile->utilize_email}
		<h2>Contact Information</h2>
		<table class="table_profileInfo">
			{if $member->profile->website}
				<tr><th>Website:</th><td><a class="iconText iconPopUpSmall" href="{$member->profile->website}">{$member->profile->website|escape|replace:'http://':''|truncate:40:"&hellip;"}</a></td></tr>
			{/if}
			{if $member->profile->utilize_email}
				<tr>
     				<th>Email:</th>
     				<td>
						{if $member->email_address}<a class="iconText iconMailSmall" href="mailto:{$member->email_address}">{$member->email_address|escape}</a>
						{else}<em>N/A</em>
		     			{/if}
     				</td>
				</tr>
			{/if}
		</table>
		{/if}
     </div>
     <div class="grid_7 omega">
     	<h2>Yehoodi Stats</h2>
		<table class="table_profileInfo">
			<tr><th>Yehoodite since:</th><td>{$member->joinedDate|escape}</td></tr>
			<tr><th>Last visited:</th><td>{$lastVisit|escape}</td></tr>
			<tr><th>Topics:</th><td>{$topics} {if $topics > 0}[<a href="{geturl controller='search'}?q={$member->user_name|escape}&amp;type=resources&amp;user=true">See All</a>]{/if}</td></tr>
			<tr><th>Comments:</th><td>{$comments} {if $comments > 0}[<a href="{geturl controller='search'}?q={$member->user_name|escape}&amp;type=comments&amp;user=true">See All</a>]{/if}</td></tr>
			<tr><th>Events:</th><td>{$events} {if $events > 0}[<a href="{geturl controller='search'}?q={$member->user_name|escape}&amp;type=events&amp;user=true">See All</a>]{/if}</td></tr>
			<tr><th>Items Voted Up:</th><td>{$votes}</td></tr>
		</table>
     </div>
</div>
<div class="clear">&nbsp;</div>