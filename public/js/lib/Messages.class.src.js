MessagesClass = Class.create();

MessagesClass.prototype = {

	initialize : function()
	{
		if($('messages')) {
			this.messageBox = $('messages');

			if(this.messageBox.className == 'notify') {
				new Effect.Highlight(this.messageBox, {startcolor: '#ffffff', endcolor: '#FFD3A3'});
				// Effect.Fade(this.messageBox, { duration: 3.0, delay: 3.0 });
			}
		}
	}
}