"use strict";

// Class definition
var KTWizard2 = function () {
    // Base elements
    var wizardEl;
    var formEl;
    var validator;
    var wizard;

    // Private functions
    var initWizard = function () {
        // Initialize form wizard
        wizard = new KTWizard('kt_wizard_v2', {
            startStep: 1, // initial active step number
			clickableSteps: true  // allow step clicking
        });

        // Change event
        wizard.on('change', function(wizard) {
            //KTUtil.scrollTop();
        });
    }

    var initValidation = function() {
        validator = formEl.validate({
            // Validate only visible fields
            ignore: ":hidden",

            // Validation rules
            rules: {
               	//= Step 1
				fname: {
					required: false
				},
				lname: {
					required: false
				},
				phone: {
					required: false
				},
				emaul: {
					required: false,
					email: true
				},

				//= Step 2
				address1: {
					required: false
				},
				postcode: {
					required: false
				},
				city: {
					required: false
				},
				state: {
					required: false
				},
				country: {
					required: false
				},

				//= Step 3
				delivery: {
					required: false
				},
				packaging: {
					required: false
				},
				preferreddelivery: {
					required: false
				},

				//= Step 4
				locaddress1: {
					required: false
				},
				locpostcode: {
					required: false
				},
				loccity: {
					required: false
				},
				locstate: {
					required: false
				},
				loccountry: {
					required: false
				},

				//= Step 5
				ccname: {
					required: false
				},
				ccnumber: {
					required: false,
					creditcard: true
				},
				ccmonth: {
					required: false
				},
				ccyear: {
					required: false
				},
				cccvv: {
					required: false,
					minlength: 2,
					maxlength: 3
				},
            },

            // Display error
            invalidHandler: function(event, validator) {
                KTUtil.scrollTop();

                swal.fire({
                    "title": "",
                    "text": "There are some errors in your submission. Please correct them.",
                    "type": "error",
                    "confirmButtonClass": "btn btn-secondary"
                });
            },

            // Submit valid form
            submitHandler: function (form) {

            }
        });
    }

    var initSubmit = function() {
        var btn = formEl.find('[data-ktwizard-type="action-submit"]');

        btn.on('click', function(e) {
            e.preventDefault();

           
                // See: src\js\framework\base\app.js
                KTApp.progress(btn);
                //KTApp.block(formEl);

                // See: http://malsup.com/jquery/form/#ajaxSubmit
                formEl.ajaxSubmit({
                    success: function() {
                        KTApp.unprogress(btn);
                        //KTApp.unblock(formEl);

                        swal.fire({
                            "title": "",
                            "text": "The application has been successfully submitted!",
                            "type": "success",
                            "confirmButtonClass": "btn btn-secondary"
                        });
                    }
                });
            
        });
    }

    return {
        // public functions
        init: function() {
            wizardEl = KTUtil.get('kt_wizard_v2');
            formEl = $('#kt_form');

            initWizard();
            //initValidation();
            //initSubmit();
        }
    };
}();

jQuery(document).ready(function() {
    KTWizard2.init();
});
