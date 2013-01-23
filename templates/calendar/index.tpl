{include file='header.tpl' section='calendar' maps=true}
<h1 class="hidden">{$title|escape}</h1>

<div class="grid_16">
	{include file='calendar/calendar-control-box-tabs.tpl'}
	{include file='calendar/calendar-control-box.tpl'}
	{include file='calendar/calendar.tpl'}
</div>

<div class="clear">&nbsp;</div>

{include file='footer.tpl'}

{* This is for linking the browse with FullCalendar *}
<script type="text/javascript" src="/js/fullcalendar/CalBrowseLink.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new CalBrowseLink('form_calendarLocations');
</script>
