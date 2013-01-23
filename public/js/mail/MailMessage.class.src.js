MailMessage = Class.create();

MailMessage.prototype = {

	form: null,
	
	initialize : function(form)
	{
		this.form = $(form);
		this.controller = 'mail';

		// for the main submit button
		Event.observe($('button_submit'),'click', this.onSubmit.bindAsEventListener(this));
	},
	
	//
	// When the submit button is pressed
	//
	onSubmit : function(e)
	{
		$('button_submit').disable();
		this.form.submit();
	}
}
