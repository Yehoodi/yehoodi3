<h3>{$title|escape}</h3>

<p>
{if $locationCity}
	{$locationCity}, {$locationState}, {$locationCountry}
{else}
	{$fullLocation}
{/if}
	<br /><em>{$startDate}{if $startDate != $endDate} &ndash; {$endDate}{/if}</em>
</p>

{* <p>{$description|escape|resourcefilter|strip|truncateclosetags:255}</p> *}

<ul class="meta">
	<li>{$categoryName|capitalize}</li>
	<li class="comments"><span class="iconText iconBalloonSmall">{$numComments}</span><span class="hidden"> comments</span></li>
	<li class="votes"><span class="iconText iconThumbSmall">{$numVotes}</span><span class="hidden"> votes</span></li>
	<li class="views"><span class="iconText iconEyeSmall">{$numViews}</span><span class="hidden"> views</span></li>
</ul>