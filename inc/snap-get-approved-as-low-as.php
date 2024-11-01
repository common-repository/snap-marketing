<?php
$product_price = str_replace( wc_get_price_thousand_separator(), '', $product_price );
$product_price = str_replace( wc_get_price_decimal_separator(), '.', $product_price );

$curl                         = curl_init();
$product_details              = array(
	"items" => array(
		array(
			"price"  => (float)$product_price,
			"itemId" => '"' . get_the_ID() . '"',
			"sku"    => $product->get_sku(),
		)
	)
);

$Snap_Marketing_frequency_url = constant( 'Sandbox_Snap_Marketing_frequency_URL' );
if ( $this->set_snap_marketing_environment != 'Sandbox' ) {
	$Snap_Marketing_frequency_url = constant( 'Live_Snap_Marketing_frequency_URL' );
}
$api_url = $Snap_Marketing_frequency_url . $this->snap_marketin_configuration['snap_marketing_estimate'];
$args = array(
	'method'      => 'POST',
	'returntransfer' => true,
	'maxredirs'      => 10,
	'timeout'     => 30,
	'httpversion'    => CURL_HTTP_VERSION_1_1,
	'headers'        => array(
		"authorization" => "Bearer " . $Snap_Token,
		"cache-control" => "no-cache",
		"content-type" => "application/json",
		"referrer-policy" => "no-referrer-when-downgrade",
	),
	'body'           => json_encode($product_details)
);

$response = wp_remote_post( $api_url, $args );
$response = $response['body'];
$response = json_decode( $response );

if ( $response->status != 200 ) {
	?>
	<p>
		<?php _e( 'Error in processing request', 'snap-marketing' ); ?>
	</p>
	<?php
} else {

	
	if ( $response->success ) {
		?>
		<p>
			<script type="text/javascript">
				var paymentEstimateData = { price:<?php echo esc_html( (float)$product_price ); ?>,itemId:<?php echo esc_html(get_the_ID()); ?>,sku:'<?php echo esc_html($product->get_sku()); ?>',paymentScheduleAmount:<?php echo esc_html($response->data->items[0]->paymentScheduleAmount); ?>,costOfLease:<?php echo esc_html($response->data->costOfLease); ?>,totalCost:<?php echo esc_html($response->data->totalCost); ?>,frequency:'W'};
			</script>
			<?php
			echo sprintf( __( "Lease for as low as $%s/%s.", 'snap-marketing' ), $response->data->items[0]->paymentScheduleAmount, $this->Treatment_Estimate[ $this->snap_marketin_configuration['snap_marketing_estimate'] ] );
			?>
			<a class="learn-more-popup" href="#marketing-modal" onclick="snap.launch({
				experience: 'PRE_APPROVAL',
				token: '<?php echo esc_html( $Snap_Token ); ?>',
				marketing: {
					treatment: 'AS_LOW_AS'
				},
				data: paymentEstimateData
			});">
			<?php _e( 'Learn More', 'snap-marketing' ); ?>
		</a>
	</p>
	<!-- white-popup-block mfp-hide -->
	<!-- <div id="marketing-modal" class="marketing-modal">
		<div class="marketing-modal-wrapper">
			<header>
				<img class="logo"
				src="<?php echo sprintf( '%sassets/images/snap-marketing-logo.svg', plugin_dir_url( __DIR__ ) ); ?>"/>
				<a class="learn-more-dismiss" href="#">
					<img src="<?php echo sprintf( '%sassets/images/close_24px.png', plugin_dir_url( __DIR__ ) ); ?>"/>
				</a>
			</header>
			<div class="bg">
				<img src="<?php echo sprintf( '%sassets/images/BG-1.png', plugin_dir_url( __DIR__ ) ); ?>">
				<?php _e( '<p>Get it now, <br>pay over time</p>', 'snap-marketing' ); ?>				
			</div>
			<div class="snap-content-area">
				<div class="center-content">
					<?php
					echo sprintf( __( "<p>Your payments could be as low as <span>$%s/%s</span>.</p>", 'snap-marketing' ), $response->data->items[0]->paymentScheduleAmount, $this->Treatment_Estimate[ $this->snap_marketin_configuration['snap_marketing_estimate'] ] );
					echo sprintf( __( "<p>based on the merchandise price of <span>$%s</span>.*</p>", 'snap-marketing' ), $product->get_price() );
					?>
				</div>
				<a class="get-started" href="#"
				onclick="snap.launch({
					experience: 'PRE_APPROVAL',
					token: '<?php echo esc_attr( $Snap_Token ); ?>',
					marketing: {
						treatment: 'AS_LOW_AS'
					},
					data: paymentEstimateData
				});"><?php _e( 'GET STARTED', 'snap-marketing' ); ?></a>

				<?php
				echo sprintf( __( "<p>If you have a valid approval, please select Snap as your payment method at checkout.</p>", 'snap-marketing' ) );
				echo sprintf( __( "<p>* This is a Lease-to-own transaction. This payment is based on 52 weekly
					payments for a
					total amount
					of $%s with a cost of lease of $%s. Your
					total amount
					and
					cost of
					lease may vary upon credit inquiry and approval. You do not obtain ownership
					until all
					required
					payments are made.</p>", 'snap-marketing' ), $response->data->totalCost, $response->data->costOfLease );
					?>
				</div>
				<footer>
					<p><img
						src="<?php echo sprintf( '%sassets/images/lock_24px.png', plugin_dir_url( __DIR__ ) ); ?>"/>
					Secure.</p>
					<p>Copyright © 2020 Snap Finance. All Rights Reserved</p>
				</footer>
			</div>
		</div> -->
		<?php
	} else {
		?>
		<p>
			<?php _e( 'Error in processing request', 'snap-marketing' ); ?>
		</p>
		<?php
	}
}