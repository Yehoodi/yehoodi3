<div class="controlBox{if $location == 'other'} controlBoxOtherLocation{/if}">
	<form id="form_calendarLocations" class="calForm" action="">
		<h2>
			Events {if $location == 'anywhere'}Anywhere{else}by Location{/if}
		</h2>
		<div id='loading' style='display:none'>Loading events... <img src="/images/graphics/ajax-loader.gif" alt="Loading" /></div>	
		<span class="span_eventTypes">
			Show events of type:
			<a class="a_eventType {if $categoryUrl == "competitions"}currentSelection{/if}" name="competitions" href="/calendar/{$calType}/competitions/?location={$location}" title="">Competition / Workshop</a>
			<a class="a_eventType {if $categoryUrl == "camps-workshops"}currentSelection{/if}" name="camps-workshops" href="/calendar/{$calType}/camps-workshops/?location={$location}" title="">Camp / Workshop</a>
			<a class="a_eventType {if $categoryUrl == "exchange"}currentSelection{/if}" name="exchange" href="/calendar/{$calType}/exchange/?location={$location}" title="">Exchange</a>
			{if $location == "other"}
				<a class="a_eventType {if $categoryUrl == "swing-dance"}currentSelection{/if}" name="swing-dance" href="/calendar/{$calType}/swing-dance/?location={$location}" title="">Swing Dance</a>
				<a class="a_eventType {if $categoryUrl == "performance-special-event"}currentSelection{/if}" name="performance-special-event" href="/calendar/{$calType}/performance-special-event/?location={$location}" title="">Performance &amp; Special Event</a>
			{/if}
			<a class="a_eventType {if $categoryUrl == "all"}currentSelection{/if}" name="all" href="/calendar/{$calType}/all/?location={$location}" title="">All</a>
			{if $location == "other"}
				<span id="span_eventLocation">	
					Location: 
					<input type="text" name="text_filterLocation" value="{$address}" id="form_location" />
					<input type="button" class="button_submit" id="button_filter" value="Show" />
					<span id="span_homeLocation">
					{if $authenticated}
						{if $identity->location == null}
							<strong>You have not <a href="{geturl controller='account' action='settings'}">set a home location</a>.</strong>
						{ else }                      
							{if $address == $identity->location}
								Home Location:
							{else}
								<a id="a_homeLocation" href="/calendar/{$calType}/{$categoryUrl}/?location={$location}&lon={$identity->longitude}&lat={$identity->latitude}&loc={$identity->location}">Reset</a> to
							{/if}
							<strong><a id="a_homeLocation" href="/calendar/{$calType}/{$categoryUrl}/?location={$location}&lon={$identity->longitude}&lat={$identity->latitude}&loc={$identity->location}">{$identity->location|escape}</a></strong>
							[<a href="{geturl controller='account' action='settings'}">Change</a>]
						{/if} 
					{else}
						<strong><a href="{geturl controller='account' action='login'}?redirect=/calendar">Log in</a> to set a home location for your calendar of events.</strong>
					{/if}
					</span>
				</span>
			{/if}         
		</span>
		{include file='modules/module.add-link.tpl'}
	</form>
</div>
<div class="clear">&nbsp;</div>