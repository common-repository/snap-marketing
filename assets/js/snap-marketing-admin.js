jQuery(document).ready(function () {
    var change_apply = false;
    snap_marketing_enviroment();
    snap_marketing_treatmentactive();
    snap_treatment_type();
    jQuery(document).on('change', '#snap_marketing_environment', function () {
        snap_marketing_enviroment();
    });
    jQuery(document).on('change', '#TreatmentActive', function () {
        snap_marketing_treatmentactive();
    });
    
    jQuery(document).on('change', 'input[name="Snap_Product_Active"]', function () {
        if ( jQuery(this).prop('checked') ) {
            if ( !confirm('Note: If your site is using a third party eCommerce management tool, please test this setting in Sandbox before deploying this on production.') ) {
                jQuery(this).prop('checked',false);
            } 
        }
    });
    jQuery(document).on('change', 'form.snap-marketing select,form.snap-marketing input', function () {
        change_apply = true;
        //console.log(change_apply);
    });
    jQuery('#snap_marketing_banner').attr('src',jQuery('#TreatmentLogo').val());
    jQuery(document).on('change', '#TreatmentLogo', function () {
        jQuery('#snap_marketing_banner').attr('src',jQuery(this).val());
        change_img_width();
    });
    change_img_width();
    jQuery(document).on('change', '.snap-marketing input', function () {
        var valid_option = false;
        var snap_marketing_active = jQuery('#snap_marketing_active');
        if ( snap_marketing_active.prop('checked') ) {
            var snap_marketing_environment = jQuery('#snap_marketing_environment').val();        
            if (snap_marketing_environment != 'Production') {
                if( !jQuery('#snap_marketing_sandbox_id_tr input').val() || !jQuery('#snap_marketing_sandbox_secret_key_tr input').val() ) {
                    valid_option = true;
                }
            } else {
              if( !jQuery('#snap_marketing_live_secret_key_tr input').val() || !jQuery('#snap_marketing_live_id_tr input').val() ) {
                valid_option = true;
            }
        }
        if ( valid_option ) {
            alert('Please enter ClientID and Client Secret');
            snap_marketing_active.prop('checked',false);
        }        
    }
});

  
    jQuery(document).on('change', '#TreatmentType', function () {
        snap_treatment_type();
    });
    jQuery('form.snap-marketing').submit(function () {
        if ( change_apply ) {
            var r = confirm("Are you sure you want to change your credentials?");
            if (r == true) {
                createCookie('snap_marketing_token', 'yes');
                snap_marketing_reset_token();
            } else {
                return false;
            }
        }
    });
    var snap_change = readCookie('snap_marketing_token');
    if (snap_change == 'yes') {
        snap_marketing_reset_token();
        eraseCookie('snap_marketing_token');
        jQuery('form.snap-marketing').before('<div id="message" class="updated inline"><p><strong>Credentials are updated and token successfully reset</strong></p></div>');
    }

   
}
);
  function snap_marketing_treatmentactive() {
        if ( jQuery('#TreatmentActive').val() == 'Enable' ) {
            jQuery('.hide_active').show();
        } else {
            jQuery('.hide_active').hide();
        }
    }
    function change_img_width() {
        setTimeout( function() {
        //console.log(jQuery('#snap_marketing_banner').width() );
        if ( jQuery('#snap_marketing_banner').width() > 500 ) {
            jQuery('.snap_marketing_banner').addClass('active');
            //console.log('add active');
        } else {
            jQuery('.snap_marketing_banner').removeClass('active');
            //console.log('not active');
        }
    }, 500 );
    }
    
 function snap_marketing_reset_token() {
        var data = {action: 'reset_marketing_token'};
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: snap_marketing.ajaxurl,
            data: data,
            success: function (response) {

            }
        });
    }

    function snap_marketing_enviroment() {
        var snap_marketing_environment = jQuery('#snap_marketing_environment').val();
        jQuery('#snap_marketing_sandbox_id_tr,#snap_marketing_live_secret_key_tr,#snap_marketing_live_id_tr,#snap_marketing_sandbox_secret_key_tr').hide();
        if (snap_marketing_environment == 'Production') {
            jQuery('#snap_marketing_live_id_tr,#snap_marketing_live_secret_key_tr').show();
        } else {
            jQuery('#snap_marketing_sandbox_id_tr,#snap_marketing_sandbox_secret_key_tr').show();
        }
    }

    function snap_treatment_type() {
        var TreatmentType = jQuery('#TreatmentType').val();
        var TreatmentID = jQuery('#post_ID').val();
        var shortcode_text = '';
        var option_block = treatment_type_value = '';
        jQuery('#TreatmentLogo option').hide();
        if (TreatmentType == 'PRE_APPROVAL') {
            treatment_type_value = 'get_approved';
            jQuery('.enable_in_all_product').hide();
            jQuery('.enable_in_all_product input').prop('checked',false);
        }else if (TreatmentType == 'BANNER') {
            treatment_type_value = 'banner';
            jQuery('.enable_in_all_product').hide();
            jQuery('.enable_in_all_product input').prop('checked',false);
        }else if (TreatmentType == 'PRE_QUALIFICATION') {
            treatment_type_value = 'prequalification';
            jQuery('.enable_in_all_product').show();
        }else if (TreatmentType == 'PRE_QUALIFICATION_AS_LOW_AS') {
            treatment_type_value = 'prequalification_as_low_as';
            jQuery('.enable_in_all_product').show();
        } else {
            treatment_type_value = 'get_approved_as_low_as';  
            jQuery('.enable_in_all_product').show();
        }
        var option_block = jQuery('#TreatmentLogo option[data-type="' + treatment_type_value + '"]');

        option_block.show();
        jQuery('.desciption_box').hide();
        jQuery('.' + treatment_type_value + '_desc').show();
        if ( jQuery('#TreatmentLogo option:selected').data('type') != treatment_type_value ) {
            jQuery('#TreatmentLogo').val(option_block.val()).change();  change_img_width();  
        }
        
        shortcode_text = '[snap_treatment_add_' + treatment_type_value + ' treat_id="' + TreatmentID + '"]';
        //console.log(shortcode_text);
        jQuery('.shortcode-text').html(shortcode_text);
    }

    function createCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";

        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name, "", -1);
    }