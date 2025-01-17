<?php

/*******  admin side view *******/
class snap_marketing_front_side {

	public $snap_marketin_configuration = array( 'snap_marketing_estimate' => 'W' );

	public $Treatment_Estimate = array(
		'BW' => 'Bi Weekly',
		'W'  => 'week',
		'M'  => 'Monthly',
		'MW' => 'Monthly Week',
		'SM' => 'Semi Monthly',
	);

	public $set_snap_marketing_environment = '';

	/**
	 * Class constructor, more about it in Step 3
	 */
	public function __construct() {

		$treatment_types = array(
			'get_approved_as_low_as',
			'get_approved',
			'banner',
			/*
			'prequalification',
			'prequalification_as_low_as'*/
		);

		foreach ( $treatment_types as $treatment_type ) {
			add_shortcode( 'snap_treatment_add_' . $treatment_type, array( $this, 'snap_treatment_add' ) );
		}

		// This action use for show list of quality range price details on product detail page.
		add_action( 'woocommerce_single_product_summary', array( $this, 'snap_treatment_add' ), 15 );
		$Configuration_Options = array(
			'snap_marketing_active',
			'snap_marketing_title',
			'snap_marketing_description',
			'snap_marketing_environment',
			/*'snap_marketing_estimate',*/
			'snap_marketing_sandbox_id',
			'snap_marketing_sandbox_secret_key',
			'snap_marketing_live_id',
			'snap_marketing_live_secret_key',
		);
		foreach ( $Configuration_Options as $Configuration_Option ) {
			$this->snap_marketin_configuration[ $Configuration_Option ] = get_option( $Configuration_Option );
		}
		$this->set_snap_marketing_environment();
		add_filter( 'snap_marketing_configuration_details', $this->snap_marketin_configuration );
		add_action( 'wp_enqueue_scripts', array( $this, 'snap_marketing_front_scripts' ) );
		add_action( 'wp_ajax_snap_marketing_front', array( $this, 'snap_marketing_front' ) );
		add_action( 'wp_ajax_nopriv_snap_marketing_front', array( $this, 'snap_marketing_front' ) );

	}

	/*
	 * Add style and script file on front side of site.
	 */

	public function snap_marketing_front_scripts() {
		global $post;

		$snap_marketing_script = constant( 'Sandbox_Snap_Marketing_SDK' );
		if ( $this->snap_marketin_configuration['snap_marketing_environment'] == 'Production' ) {
			$snap_marketing_script = constant( 'Live_Snap_Marketing_SDK' );
		}
		//$payment_method = filter_input( INPUT_GET, 'payment_method', FILTER_SANITIZE_STRING );
		$payment_method = wp_strip_all_tags(filter_input( INPUT_GET, 'payment_method', FILTER_UNSAFE_RAW ));
		if ( empty( $payment_method ) ) {
			wp_enqueue_script( 'snap-finance-sdk', $snap_marketing_script, array( 'jquery' ), time(), true );
		}
		wp_localize_script(
			'snap-finance-sdk',
			'ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'we_value' => 1234,
			)
		);

		wp_enqueue_script( 'snap-finance-snap-marketing-front', plugins_url( 'assets/js/snap-marketing-front.js', __FILE__ ), array( 'jquery' ), time(), true );
		// wp_enqueue_script( 'snap-finance-sdk-front', 'https://js-qa-dev.snapfinance.com/dev/v2/snap-sdk.js', array( 'jquery' ), time(), true );

		wp_enqueue_style( 'snap-marketing-front-style', plugins_url( 'assets/css/snap-marketing-front.css', __FILE__ ), array(), time(), false );

	}

	public function snap_marketing_front() {

		$variation_id = sanitize_text_field($_REQUEST['variation_id']);

		$this->snap_treatment_add( $variation_id );

		die;
	}

	public function set_snap_marketing_environment() {
		$snap_finance_setting = $this->snap_marketin_configuration;
		if ( ! isset( $snap_finance_setting['snap_marketing_environment'] ) ) {
			$snap_finance_setting['snap_marketing_environment'] = 'Sandbox';
		}
		if ( empty( $snap_finance_setting['snap_marketing_environment'] ) ) {
			$snap_finance_setting['snap_marketing_environment'] = 'Sandbox';
		}

		$this->set_snap_marketing_environment = $snap_finance_setting['snap_marketing_environment'];

	}

	/*
	 * Create shortcode treatment
	 */
	public function snap_treatment_add( $atts = '' ) {

		if ( empty( $atts ) ) {
			$atts = array( 'variation_id' => 0 );
		}
		if ( ! $this->snap_marketin_configuration['snap_marketing_active'] ) {
			return '';
		}

		global $post, $product;
		$product_price = 0;
		if ( ! isset( $atts['treat_id'] ) ) {
			$Snap_Product_Active = get_option( 'Snap_Product_Active' );

			if ( $Snap_Product_Active ) {
				$atts = array(
					'treat_id'     => $Snap_Product_Active,
					'echo'         => true,
					'variation_id' => $atts,
				);
			} else {
				return '';
			}
		} else {
			if ( is_singular( 'product' ) ) {
				$Snap_Product_Active = get_option( 'Snap_Product_Active' );
				if ( $Snap_Product_Active && ( get_post_meta( $Snap_Product_Active, 'Snap_TreatmentType', true ) == get_post_meta( $atts['treat_id'], 'Snap_TreatmentType', true ) ) ) {
					return '';
				}
			}
		}

		$Treatment_data = get_post( $atts['treat_id'] );

		$Snap_TreatmentType   = get_post_meta( $atts['treat_id'], 'Snap_TreatmentType', true );
		$Snap_TreatmentLogo   = get_post_meta( $atts['treat_id'], 'Snap_TreatmentLogo', true );
		$Snap_TreatmentActive = get_post_meta( $atts['treat_id'], 'Snap_TreatmentActive', true );

		if ( $Snap_TreatmentActive == 'Disable' ) {
			return '';
		}

		if ( ( is_singular( 'product' ) && $Snap_TreatmentType == 'AS_LOW_AS' ) || isset( $atts['variation_id'] ) ) {

			if ( $atts['variation_id'] != 0 && ! is_array( $atts['variation_id'] ) ) {
				$sales_price = get_post_meta( $atts['variation_id'], '_sale_price', true );
				if ( $sales_price ) {
					$product_price = $sales_price;
				} else {
					$product_price = get_post_meta( $atts['variation_id'], '_regular_price', true );
				}

				$post = get_post_parent( $atts['variation_id'] );

				if ( $post ) {
					$product = wc_get_product( $post->ID );
				}
			} else {
				if ( $product->is_type( 'simple' ) ) {
					$product_price = $product->get_price();
				} elseif ( $product->is_type( 'variable' ) ) {
					$product_price = $product->get_variation_price();
				}
			}
			
			if ( $product_price < 75 || $product->stock_status=='outofstock' || empty($product_price)) {
				return '';
		   }
		}

		$Snap_TreatmentAlignment = get_post_meta( $atts['treat_id'], 'Snap_TreatmentAlignment', true );

		if ( ( ( $post->post_type != 'product' && $Snap_TreatmentType == 'AS_LOW_AS' ) || ( $Treatment_data->post_status != 'publish' ) ) && ! isset( $attr['variation_id'] ) ) {
			return '';
		}
		$Snap_Token = $this->get_snap_finance_token();

		ob_start();
		?>
		<section class="snap-marketing-treatment alignment-<?php echo esc_attr( strtolower( $Snap_TreatmentAlignment ) ); ?> <?php echo esc_attr( strtolower( $Snap_TreatmentType ) ); ?>">
			<div class="treatment-logo">
				<img src="<?php echo esc_url( $Snap_TreatmentLogo ); ?>"/>
			</div>
			<div class="treatment-desc">
				<?php
				switch ( $Snap_TreatmentType ) {
					case 'PRE_APPROVAL':
						include 'inc/snap-pre-approval.php';
						break;
					case 'BANNER':
						include 'inc/snap-banner.php';
						break;
					case 'PRE_QUALIFICATION':
						include 'inc/snap-pre-qualification.php';
						break;
					case 'PRE_QUALIFICATION_AS_LOW_AS':
						include 'inc/snap-pre-qualification-as-low-as.php';
						break;
					default:
						include 'inc/snap-get-approved-as-low-as.php';
				}
				?>
			</div>
		</section>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( isset( $atts['echo'] ) ) {
			echo __( $output );
		} else {
			return $output;
		}
	}

	public function get_snap_finance_token() {
		$api_url              = $client_id = $client_secret = $audience_url = '';
		$snap_finance_setting = $this->snap_marketin_configuration;
		if ( $this->set_snap_marketing_environment == 'Sandbox' ) {
			$client_id     = $snap_finance_setting['snap_marketing_sandbox_id'];
			$client_secret = $snap_finance_setting['snap_marketing_sandbox_secret_key'];
			$api_url       = constant( 'Sandbox_API_URL' );
			$audience_url  = constant( 'Sandbox_Audience_URL' );
		} else {
			$client_id     = $snap_finance_setting['snap_marketing_live_id'];
			$client_secret = $snap_finance_setting['snap_marketing_live_secret_key'];
			$api_url       = constant( 'Live_API_URL' );
			$audience_url  = constant( 'Live_Audience_URL' );
		}
		$snap_finance_token = false;
		// Checking last updated token data in database.
		if ( 1 == 1 or WP_DEBUG or false === ( $snap_finance_token = get_transient( 'snap_marketing_token' ) ) ) {

			$args = array(
				'returntransfer' => true,
				'maxredirs'      => 10,
				'httpversion'    => CURL_HTTP_VERSION_1_1,
				'headers'        => array(
					'content-type: application/json',
				),
				'body'           => array(
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'audience'      => $audience_url,
					'grant_type'    => 'client_credentials',
				),
			);

			$response = wp_remote_post( $api_url, $args );
			if ( ! is_wp_error( $response ) ) {
				$response = $response['body'];
				$response = json_decode( $response );
				if ( isset( $response->access_token ) ) {
					$snap_finance_token = $response->access_token;
				} else {
					$train_audience_url  = constant( 'Training_Audience_URL' );
					$args = array(
						'returntransfer' => true,
						'maxredirs'      => 10,
						'httpversion'    => CURL_HTTP_VERSION_1_1,
						'headers'        => array(
							'content-type: application/json',
						),
						'body'           => array(
							'client_id'     => $client_id,
							'client_secret' => $client_secret,
							'audience'      => $train_audience_url,
							'grant_type'    => 'client_credentials',
						),
					);
					$response_train = wp_remote_post( $api_url, $args );
					$response_train = $response_train['body'];
					$response_train = json_decode( $response_train );
					if ( isset( $response_train->access_token ) ) {
						$snap_finance_token = $response_train->access_token;
					} else {
						$snap_finance_token = false;
					}
				}
				// Add new updated token in database.
				// $this->add_log_message( 'Create new snap finance token' );
				if ( $snap_finance_token && empty( $version ) ) {
					set_transient( 'snap_marketing_token', $snap_finance_token, 600 );
				}
			}
		}

		return $snap_finance_token;
	}

	public function add_log_message( $message = '' ) {
		$log_url      = plugin_dir_url( __FILE__ ) . 'log.txt';
		$old_message  = '';
		$old_message  = wp_remote_get( $log_url )['body'];
		$old_message .= '\n\n\r' . $message . ' [ ' . date( 'm/d/Y h:i:s a', time() ) . ' ]';
		file_put_contents( $path, $old_message );
	}

}

$snap_marketing_front_side = new snap_marketing_front_side();
