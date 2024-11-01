<p>
	<?php _e( 'Snap offers flexible lease-to-own financing.', 'snap-marketing' ); ?>
	<a href="#"	onclick="snap.launch({ experience: 'PRE_APPROVAL', token: '<?php echo esc_html( $Snap_Token ); ?>', marketing: { treatment: '<?php echo esc_html( $Snap_TreatmentType ); ?>' } });return false;">
		<?php _e( 'Get Approved', 'snap-marketing' ); ?>
	</a>
</p>
