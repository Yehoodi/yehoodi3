AccountAvatarForm = Class.create();

AccountAvatarForm.prototype = {

	a: null,
	
	initialize : function(a)
	{
	    // Observe delete avatar button
	    $('button_avatarDelete').observe('click', this.confirmDelete.bindAsEventListener(this));
	},
	
	//
	// Confirm delete avatar?
	//
	confirmDelete :	function(e)
	{
		if(!confirm('Delete current avatar?')) {
			Event.stop(e);
			return false;
		}
	}
};