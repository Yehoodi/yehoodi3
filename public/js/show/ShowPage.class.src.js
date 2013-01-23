ShowPage = Class.create();

ShowPage.prototype = {

	initialize : function()
	{
		// Show notes link
		if($('div_notesHider')) {
			$('div_notesHider').observe('click', this.showShowNotesDiv.bindAsEventListener(this));
		}
	},

	//
	// Show show notes div
	//
	showShowNotesDiv : function()
	{
		if (!$('div_showNotes').visible()) {
			Effect.Appear('div_showNotes');
			$('div_notesHider').className = 'a_expand';
		} else {
			$('div_showNotes').hide();
			$('div_notesHider').className = 'a_expanded';
		}
	},
}