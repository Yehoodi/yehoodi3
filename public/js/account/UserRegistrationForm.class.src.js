UserRegistrationForm = Class.create();

UserRegistrationForm.prototype = {

    form   : null,

    initialize : function(form)
    {
        this.form = $(form);
        this.form.observe('submit', this.onSubmit.bindAsEventListener(this));

        this.resetErrors();
	    
        // set focus on the user name field
        $('form_user_name').focus()
    },

    resetErrors : function()
    {
        this.form.getElementsBySelector('.error').invoke('hide');
    },

    showError : function(key, val)
    {
        var formElement = this.form[key];
        var container = formElement.up().down('.error');

		//console.log(formElement);
        if (container) {
            container.update(val);
            container.show();
        }
    },

    onSubmit : function(e)
    {
        Event.stop(e);

        var options = {
            parameters : this.form.serialize(),
            method     : this.form.method,
            onSuccess  : this.onFormSuccess.bind(this)
        };

        this.resetErrors();
        new Ajax.Request(this.form.action, options);
    },

    onFormSuccess : function(transport)
    {
        var json = transport.responseText.evalJSON(true);
        
        // more error stuff
        var errors = $H(json.errors);
        //alert(errors.keys().size());

        if (errors.size() > 0) {
            this.form.down('.error').show();
            errors.each(function(pair) {
                this.showError(pair.key, pair.value);
            }.bind(this));
        }
        else {
            this.form.submit();
        }
    }
};
