jQuery(
	function () {
		/*jQuery('.learn-more-popup').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#username',
		modal: true
		});
		jQuery(document).on('click', '.learn-more-dismiss', function (e) {
		e.preventDefault();
		jQuery.magnificPopup.close();
		});
		jQuery(document).on('click', '.get-started', function (e) {
		e.preventDefault();
		jQuery('.learn-more-dismiss').click();
		});*/
		if ( jQuery( '.variations_form' ).size() == 1 && jQuery( this ).val() == '' ) {
			// jQuery('.snap-marketing-treatment').remove();
		}
		jQuery( document ).on(
			'change',
			'.variation_id',
			function(){
				if ( jQuery( this ).val() ) {
					var data = {
						'action': 'snap_marketing_front',
						'variation_id': jQuery( this ).val()
					};

					jQuery.post(
						ajax_object.ajax_url,
						data,
						function(response) {
							jQuery( '.snap-marketing-treatment' ).remove();
							jQuery( '.variations_form' ).before( response );
						}
					);
				} else {
					jQuery( '.snap-marketing-treatment' ).remove();
				}
				jQuery( document ).on(
					'click',
					'.reset_variations',
					function(){
						jQuery( '.snap-marketing-treatment' ).remove();
					}
				);
			}
		);
	}
);
