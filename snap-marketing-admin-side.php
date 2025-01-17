<?php

/*******  admin side view *******/
class snap_marketing_admin_side {


	public $Treatment_Types = array(
		'PRE_APPROVAL' => 'Get Approved',
		'AS_LOW_AS'    => 'Get Approved – As low as',
		'BANNER'       => 'Banner',
		/*
		'PRE_QUALIFICATION' => 'Prequalification',
		'PRE_QUALIFICATION_AS_LOW_AS' => 'Prequalification - As low as',*/
	);

	public $Treatment_Estimate = array(
		'BW' => 'Bi Weekly',
		'W'  => 'Weekly',
		'M'  => 'Monthly',
		'MW' => 'Monthly Week',
		'SM' => 'Semi Monthly',
	);

	public $Treatment_Alignment = array(
		'Left',
		'Center',
		'Right',
	);

	public $Treatment_Logo = array();

	public $Treatment_Active = array( 'Enable', 'Disable' );

	public $Configuration_Options = array();

	public $Treatment_Settings = array();

	/**
	 * Class constructor, more about it in Step 3
	 */
	public function __construct() {

		$this->Treatment_Logo        = $this->treatment_logo_urls();
		$this->Configuration_Options = array(
			'snap_marketing_active'             => array(
				'type'     => 'checkbox',
				'value'    => '',
				'label'    => __( 'Enable/Disable', 'snap-marketing' ),
				'details'  => __( 'Enable Snap Marketing', 'snap-marketing' ),
				'readonly' => '',
			),
			'snap_marketing_title'              => array(
				'type'     => 'text',
				'value'    => 'Snap Marketing',
				'label'    => __( 'Title', 'snap-marketing' ),
				'readonly' => 'readonly',
			),
			'snap_marketing_description'        => array(
				'type'     => 'textarea',
				'value'    => 'The Snap Marketing plugin enables preapproval functionality where your credit-challenged shoppers can get potentially approved during key moments of their shopping journey on your webstore; thereby, giving you greater ability to close more sales. There are multiple ways to drive e-commerce preapproval customers, with our “Get Approved? and “Get Approved – As low as? treatments. We encourage you to use both on key places on your site to drive visibility for customers who may need financing to transact on your store.',
				'label'    => __( 'Description', 'snap-marketing' ),
				'readonly' => 'readonly',
			),
			'snap_marketing_environment'        => array(
				'type'     => 'select',
				'value'    => '',
				'options'  => array(
					'Sandbox'    => 'Sandbox',
					'Production' => 'Production',
				),
				'label'    => __( 'Environment', 'snap-marketing' ),
				'readonly' => '',
			),
			/*
			'snap_marketing_estimate'        => array(
				'type'    => 'select',
				'value'   => '',
				'options' => $this->Treatment_Estimate,
				'label'   => __( 'As Low As Frequency', 'snap-marketing' ),
				'readonly' => ''
			),*/
			'snap_marketing_sandbox_id'         => array(
				'type'     => 'text',
				'value'    => '',
				'label'    => __( 'Sandbox Client ID', 'snap-marketing' ),
				'readonly' => '',
			),
			'snap_marketing_sandbox_secret_key' => array(
				'type'     => 'password',
				'value'    => '',
				'label'    => __( 'Sandbox Secret Key', 'snap-marketing' ),
				'readonly' => '',
			),
			'snap_marketing_live_id'            => array(
				'type'     => 'text',
				'value'    => '',
				'label'    => __( 'Production Client ID', 'snap-marketing' ),
				'readonly' => '',
			),
			'snap_marketing_live_secret_key'    => array(
				'type'     => 'password',
				'value'    => '',
				'label'    => __( 'Production Secret Key', 'snap-marketing' ),
				'readonly' => '',
			),
		);

		$this->Treatment_Settings = array(
			'Snap_TreatmentType'   => 'Treatment Type',
			'Snap_TreatmentLogo'   => 'Logo URL',
			'Snap_TreatmentActive' => 'Active?',
			'Shortcode'            => 'Shortcode',
		);

		add_filter( 'snap_marketing_treatment_types', $this->Treatment_Types );
		add_filter( 'snap_marketing_treatment_Logo', $this->Treatment_Logo );
		add_filter( 'snap_marketing_treatment_Active', $this->Treatment_Active );

		// We need custom JavaScript to obtain a token
		add_action( 'admin_enqueue_scripts', array( $this, 'snap_marketing_scripts' ) );
		add_action( 'init', array( $this, 'create_treatments_post_type' ), 0 );
		add_action( 'admin_menu', array( $this, 'create_configuration_page' ) );
		add_action( 'add_meta_boxes', array( $this, 'create_treatments_post_type_extra_options' ) );
		add_action( 'save_post', array( $this, 'save_create_treatments_post_type' ) );
		add_action( 'admin_init', array( $this, 'register_marketing_settings' ) );
		add_action( 'wp_ajax_reset_marketing_token', array( $this, 'snap_marketing_reset_token' ) );
		add_filter( 'manage_snap_treatments_posts_columns', array( $this, 'treatment_columns_head' ) );
		add_action( 'manage_snap_treatments_posts_custom_column', array( $this, 'treatment_columns_content' ), 10, 2 );
	}

	/*
	 * Reset Snap marketing token on site
	 */
	public function snap_marketing_reset_token() {
		delete_transient( 'snap_marketing_token' );
	}

	/*
	 * Add treatment setting columns on treatment post list page
	 */
	public function treatment_columns_head( $defaults ) {
		$date_column = $defaults['date'];
		unset( $defaults['date'] );
		foreach ( $this->Treatment_Settings as $Treatment_Setting_Key => $Treatment_Setting_Heading ) {
			$defaults[ $Treatment_Setting_Key ] = $Treatment_Setting_Heading;
		}
		$defaults['date'] = $date_column;

		return $defaults;
	}

	/*
	 * Add treatment setting columns value on treatment post list page
	 */
	public function treatment_columns_content( $column_name, $treatment_ID ) {
		foreach ( $this->Treatment_Settings as $Treatment_Setting_Key => $Treatment_Setting_Heading ) {
			if ( $column_name == $Treatment_Setting_Key ) {
				switch ( $Treatment_Setting_Key ) {
					case 'Shortcode':
						$treatment_Type = $this->get_treatment_type( get_post_meta( $treatment_ID, 'Snap_TreatmentType', true ) );
						$treatment_Type = strtolower( str_replace( array( ' - ', ' – ', ' ' ), array( '_', '_', '_' ), $this->Treatment_Types[ $treatment_Type ] ) );
						echo sprintf( __( '[snap_treatment_add_%1$s treat_id="%2$s" ]', 'snap-marketing' ), $treatment_Type, $treatment_ID );
						break;
					case 'Snap_TreatmentLogo':
						echo sprintf( __( '<img class="img_%1$s" src="%2$s" />', 'snap-marketing' ), $Treatment_Setting_Key, get_post_meta( $treatment_ID, $Treatment_Setting_Key, true ) );
						break;
					case 'Snap_TreatmentType':
						$Snap_TreatmentType = $this->get_treatment_type( get_post_meta( $treatment_ID, $Treatment_Setting_Key, true ) );
						echo esc_html( $this->Treatment_Types[ $Snap_TreatmentType ] );
						break;
					default:
						echo esc_html( get_post_meta( $treatment_ID, $Treatment_Setting_Key, true ) );
						break;
				}
			}
		}

	}

	/*
	 * Set Default treatment type
	 */
	public function get_treatment_type( $type ) {
		if ( empty( $type ) ) {
			$type = 'PRE_APPROVAL';
		}
		return $type;
	}

	/*
	 * Register configuration options
	 */
	public function register_marketing_settings() {
		$args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => null,
		);
		register_setting( 'snap-marketing-configuration', 'active', $args );
		register_setting( 'snap-marketing-configuration', 'title', $args );
		register_setting( 'snap-marketing-configuration', 'description', $args );
		register_setting( 'snap-marketing-configuration', 'environment', $args );
		/*register_setting( 'snap-marketing-configuration', 'estimate' , $args );*/
		register_setting( 'snap-marketing-configuration', 'sandbox_id', $args );
		register_setting( 'snap-marketing-configuration', 'sandbox_secret_key', $args );
		register_setting( 'snap-marketing-configuration', 'live_id', $args );
		register_setting( 'snap-marketing-configuration', 'live_secret_key', $args );
		if ( isset( $_POST['option_page'] ) ) {
			if ( $_POST['option_page'] == 'snap-marketing-configuration' ) {
				foreach ( $this->Configuration_Options as $configuration_option_key => $Configuration_Options_Value ) {
					update_option( $configuration_option_key, sanitize_text_field( $_POST[ $configuration_option_key ] ) );
				}
			}
		}
	}

	/*
	 * Save treatment extra options
	 */
	public function save_create_treatments_post_type( $post_id ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['post_type'] ) && 'snap_treatments' === $_POST['post_type'] && wp_verify_nonce( $_POST['nonce'], 'snap_treatment_details' ) ) {
			update_post_meta( $post_id, 'Snap_TreatmentType', sanitize_text_field( $_POST['TreatmentType'] ) );
			update_post_meta( $post_id, 'Snap_TreatmentLogo', sanitize_text_field( $_POST['TreatmentLogo'] ) );
			update_post_meta( $post_id, 'Snap_TreatmentActive', sanitize_text_field( $_POST['TreatmentActive'] ) );
			update_post_meta( $post_id, 'Snap_TreatmentAlignment', sanitize_text_field( $_POST['TreatmentAlignment'] ) );
			if ( isset( $_POST['Snap_Product_Active'] ) ) {
				update_option( 'Snap_Product_Active', sanitize_text_field( $_POST['Snap_Product_Active'] ) );
			} else {
				$Snap_Product_Active = get_option( 'Snap_Product_Active' );
				if ( $Snap_Product_Active == $post_id ) {
					update_option( 'Snap_Product_Active', '' );
				}
			}
		}

	}

	/*
	 * Create custom extra option meta box for treatment post type
	 */
	public function create_treatments_post_type_extra_options() {
		add_meta_box(
			'snap-treatments-extra-options',
			__( 'Treatments Settings', 'snap-marketing' ),
			array( $this, 'treatment_extra_options' ),
			'snap_treatments'
		);
	}

	/*
	 * Get Logo URls from snap finace server side
	 */
	public function treatment_logo_urls() {
		$Treatment_Logo = array();
		// Get all logos URL from snapfinance server by URL
		$sand_box_urls     = constant( 'Snap_Marketing_Logo_URL' );
		$response_xml_data = wp_remote_get( $sand_box_urls );
		if ( is_array( $response_xml_data ) && ! is_wp_error( $response_xml_data ) ) {
			$response_xml_data = (array) $response_xml_data;
			$response_xml_data = simplexml_load_string( $response_xml_data['body'] );
			if ( $response_xml_data ) {

				foreach ( $response_xml_data->Contents as $Contents ) {
					$value_text = (string) $Contents->Key;
					$value_name = basename( $value_text );
					$value_name = explode( '.', $value_name );
					if ( count( $value_name ) > 1 ) {
						$value_name = str_replace( array( '-', '_' ), ' ', $value_name[0] );
						if ( strpos( $Contents->Key, 'aslowas' ) !== false && $value_text ) {
							$Treatment_Logo[ $sand_box_urls . $value_text ]            = array(
								'name' => $value_name,
								'type' => 'get_approved_as_low_as',
							);
							$Treatment_Logo[ $sand_box_urls . $value_text . '?ver=1' ] = array(
								'name' => $value_name,
								'type' => 'prequalification_as_low_as',
							);
						}
						if ( strpos( $Contents->Key, 'ecomm-plugin-banners' ) !== false && $value_text ) {
							$Treatment_Logo[ $sand_box_urls . $value_text ] = array(
								'name' => $value_name,
								'type' => 'banner',
							);
						}
						if ( strpos( $Contents->Key, 'getapproved' ) !== false && $value_text ) {
							$Treatment_Logo[ $sand_box_urls . $value_text ]            = array(
								'name' => $value_name,
								'type' => 'get_approved',
							);
							$Treatment_Logo[ $sand_box_urls . $value_text . '?ver=1' ] = array(
								'name' => $value_name,
								'type' => 'prequalification',
							);
						}
					}
				}
			}
		}
		return $Treatment_Logo;
	}

	/*
	 * Treatment extra option seting fields
	 */
	public function treatment_extra_options( $meta_id ) {
		$outline             = '<table>';
		$outline            .= '<tr><th><label>' . esc_html__( 'Treatment Type', 'snap-marketing' ) . '</label></th>';
		$TreatmentType_value = get_post_meta( $meta_id->ID, 'Snap_TreatmentType', true );
		$outline            .= '<td><select type="text" name="TreatmentType" id="TreatmentType" class="TreatmentType" />';
		foreach ( $this->Treatment_Types as $TreatmentType_Key => $TreatmentType ) {
			$selected = '';
			if ( $TreatmentType_Key == $TreatmentType_value ) {
				$selected = 'selected';
			}
			$outline .= '<option value="' . esc_attr($TreatmentType_Key) . '" ' . esc_attr($selected) . ' >' . esc_html($TreatmentType) . '</option>';
		}
		$outline .= '</select></td></tr>';
		$outline .= '<tr><th><label>' . esc_html__( 'Description', 'snap-marketing' ) . '</label></th>';
		$outline .= '<td>';
		$outline .= '<div class="get_approved_as_low_as_desc desciption_box">';
		$outline .= __( '<b>Offer contextual financing experience with “Get Approved – As low as? treatment and banners</b><br/>Enable the “Get Approved – As low as? promotional treatment and get enhanced benefits of the “Get Approved? but customized for your product pages. Place the treatment in your product pages to give customers a glimpse of what their payments for may look like with Snap. Our research has shown that customers are more likely to apply and transact if they have an idea of how much their payments will look like with Snap.', 'snap-marketing' );
		$outline .= '</div>';
		$outline .= '<div class="prequalification_as_low_as_desc desciption_box">';
		$outline .= __( '<b>Offer contextual financing experience with "Prequalification - As low as" treatment and banners</b><br/>Enable the "Prequalification - As low as" promotional treatment and get enhanced benefits of the "Get Approved" but customized for your product pages. Place the treatment in your product pages to give customers a glimpse of what their payments for may look like with Snap. Our research has shown that customers are more likely to apply and transact if they have an idea of how much their payments will look like with Snap.', 'snap-marketing' );
		$outline .= '</div>';
		$outline .= '<div class="banner_desc desciption_box">';

		$outline               .= '</div>';
		$outline               .= '<div class="prequalification_desc desciption_box">';
		$outline               .= __( '<b>Capture customers at top of funnel with "Prequalification" treatment</b><br/>Enable the "Prequalification" promotional treatment on any pages of your webstore. Let customers know they have a financing option, from the start, to turn browsers into actual customers. With the preapproval application flow, if approved, customers will know exactly how much they have been approved for, giving them the motivation to transact for a higher shopping cart value on your store. As research shows, when customers know they have an approval and the amount, not only are they likely to purchase but purchase more items or a higher price point item ', 'snap-marketing' );
		$outline               .= '</div>';
		$outline               .= '<div class="get_approved_desc desciption_box">';
		$outline               .= __( '<b>Capture customers at top of funnel with “Get Approved? treatment and banners</b><br/>Enable the “Get Approved? promotional treatment on any pages of your webstore. Let customers know they have a financing option, from the start, to turn browsers into actual customers. With the preapproval application flow, if approved, customers will know exactly how much they have been approved for, giving them the motivation to transact for a higher shopping cart value on your store. As research shows, when customers know they have an approval and the amount, not only are they likely to purchase but purchase more items or a higher price point item. ', 'snap-marketing' );
		$outline               .= '</div>';
		$outline               .= '</td></tr>';
		$outline               .= '<tr><th><label>' . esc_html__( 'Active?', 'snap-marketing' ) . '</label></th>';
		$Treatment_Active_value = get_post_meta( $meta_id->ID, 'Snap_TreatmentActive', true );
		$outline               .= '<td><select type="text" name="TreatmentActive" id="TreatmentActive" class="TreatmentActive" />';
		foreach ( $this->Treatment_Active as $Treatment_Active ) {
			$selected = '';
			if ( $Treatment_Active == $Treatment_Active_value ) {
				$selected = 'selected';
			}
			$outline .= '<option value="' . esc_attr($Treatment_Active) . '" ' . esc_attr($selected) . ' >' . esc_html($Treatment_Active) . '</option>';
		}
		$outline             .= '</select></td></tr>';
		$outline             .= '<tr class="hide_active" ><th><label>' . esc_html__( 'Logo URL', 'snap-marketing' ) . '</label></th>';
		$Marketing_Logo_value = get_post_meta( $meta_id->ID, 'Snap_TreatmentLogo', true );
		$outline             .= '<td><select type="text" name="TreatmentLogo" id="TreatmentLogo" class="TreatmentLogo"/>';
		foreach ( $this->Treatment_Logo as $Treatment_Logo_URL => $Treatment_Logo_Name ) {
			$selected = '';
			if ( $Treatment_Logo_URL == $Marketing_Logo_value ) {
				$selected = 'selected';
			}

			$outline .= '<option value="' . esc_attr(esc_url($Treatment_Logo_URL)) . '" ' . esc_attr($selected) . ' data-type="' . esc_attr($Treatment_Logo_Name['type']) . '" >' . esc_html($Treatment_Logo_Name['name']) . '</option>';
		}
		$outline            .= '</select><div class="snap_marketing_banner"><img id="snap_marketing_banner" attr="Snap Marketing Banner" /></div></td></tr>';
		$outline            .= '<tr class="enable_in_all_product" ><th><label>' . esc_html__( 'Enable in all product description pages?', 'snap-marketing' ) . '</label></th>';
		$Snap_Product_Active = get_option( 'Snap_Product_Active' );
		$outline            .= '<td><input type="checkbox" name="Snap_Product_Active" value="' . esc_attr($meta_id->ID) . '" ';
		if ( $meta_id->ID == $Snap_Product_Active ) {
			$outline .= 'checked';
		}
		$outline                  .= ' /></td></tr>';
		$outline                  .= '<tr class="hide_active" ><th><label>' . esc_html__( 'Alignment', 'snap-marketing' ) . '</label></th>';
		$Treatment_Alignment_value = get_post_meta( $meta_id->ID, 'Snap_TreatmentAlignment', true );
		$outline                  .= '<td><select type="text" name="TreatmentAlignment" id="TreatmentAlignment" class="TreatmentAlignment" />';
		foreach ( $this->Treatment_Alignment as $Treatment_Alignment ) {
			$selected = '';
			if ( $Treatment_Alignment == $Treatment_Alignment_value ) {
				$selected = 'selected';
			}
			$outline .= '<option value="' . esc_attr($Treatment_Alignment) . '" ' . esc_attr($selected) . ' >' . esc_html($Treatment_Alignment) . '</option>';
		}
		$outline .= '</select></td></tr>';

		$outline               .= '<tr class="hide_active" > <th><label>' . esc_html__( 'Shortcode', 'snap-marketing' ) . '</label></th>';
		$Treatment_Active_value = get_post_meta( $meta_id->ID, 'Snap_TreatmentActive', true );
		$outline               .= '<td class="shortcode-text" >[snap_treatment_add treat_id="' . $meta_id->ID . '" ]</td></tr>';

		$outline .= '</table>';
		$outline .= '<input type="hidden" name="nonce" value="' . esc_attr(wp_create_nonce( 'snap_treatment_details' )) . '" >';
		echo __( $outline );
	}

	/**
	 * Adds a submenu page snap marketing a Treatment post type parent.
	 */
	public function create_configuration_page() {
		add_submenu_page(
			'edit.php?post_type=snap_treatments',
			__( 'Configuration', 'snap-marketing' ),
			__( 'Configuration', 'snap-marketing' ),
			'manage_options',
			'snap-marketing-configuration',
			array( $this, 'snap_marketing_configuration_settings' )
		);
	}

	/*
	 * Create snap marketing configuration settings page
	 */
	public function snap_marketing_configuration_settings() {
		register_setting( 'snap-marketing-configuration', 'active' );
		register_setting( 'snap-marketing-configuration', 'title' );
		register_setting( 'snap-marketing-configuration', 'description' );
		register_setting( 'snap-marketing-configuration', 'environment' );
		register_setting( 'snap-marketing-configuration', 'sandbox_id' );
		register_setting( 'snap-marketing-configuration', 'sandbox_secret_key' );
		register_setting( 'snap-marketing-configuration', 'live_id' );
		register_setting( 'snap-marketing-configuration', 'live_secret_key' );

		?>
		<div class="wrap">
			<h1><?php _e( 'Snap Marketing Configuration', 'snap-marketing' ); ?></h1>
			<form method="post" class="snap-marketing" action="options.php">
				<?php settings_fields( 'snap-marketing-configuration' ); ?>
				<?php do_settings_sections( 'snap-marketing-configuration' ); ?>
				<table class="form-table">
					<?php
					foreach ( $this->Configuration_Options as $snap_marketing_configuration_id => $snap_marketing_configuration_value ) {
						switch ( $snap_marketing_configuration_value['type'] ) {
							case 'text':
								$this->configuration_input_fields( $snap_marketing_configuration_value, $snap_marketing_configuration_id );
								break;
							case 'password':
								$this->configuration_input_fields( $snap_marketing_configuration_value, $snap_marketing_configuration_id );
								break;
							case 'textarea':
								$this->configuration_textarea_fields( $snap_marketing_configuration_value, $snap_marketing_configuration_id );
								break;
							case 'select':
								$this->configuration_select_fields( $snap_marketing_configuration_value, $snap_marketing_configuration_id );
								break;
							case 'checkbox':
								$this->configuration_checkbox_fields( $snap_marketing_configuration_value, $snap_marketing_configuration_id );
								break;
						}
					}
					?>
				</table>

				<?php submit_button(); ?>

			</form>
		</div>
		<?php
	}

	/*
	 * Create text filed for snap marketing configuration option
	 */
	public function configuration_input_fields( $option_values, $option_id ) {
		?>
		<tr valign="top" id="<?php echo esc_attr( $option_id ); ?>_tr">
			<th scope="row"><?php echo esc_html( $option_values['label'] ); ?></th>
			<td><input type="<?php echo esc_attr( $option_values['type'] ); ?>" <?php echo esc_attr( $option_values['readonly'] ); ?> name="<?php echo esc_attr( $option_id ); ?>"
				value="<?php echo esc_attr( get_option( $option_id, $option_values['value'] ) ); ?>"/></td>
			</tr>
			<?php
	}
	/*
	 * Create text filed for snap marketing configuration option
	 */
	public function configuration_textarea_fields( $option_values, $option_id ) {
		?>
		<tr valign="top" id="<?php echo $option_id; ?>_tr">
			<th scope="row">
				<?php echo esc_html( $option_values['label'] ); ?>
			</th>
			<td>
				<textarea rows="10" <?php echo esc_attr( $option_values['readonly'] ); ?>
				name="<?php echo esc_attr( $option_id ); ?>"><?php echo esc_attr( $option_values['value'] ); ?></textarea>
			</td>
		</tr>
		<?php
	}

	/*
	 * Create text filed for snap marketing configuration option
	 */
	public function configuration_checkbox_fields( $option_values, $option_id ) {
		?>
		<tr valign="top" id="<?php echo esc_attr( $option_id ); ?>_tr">
			<th scope="row"><?php echo esc_html( $option_values['label'] ); ?></th>
			<td>
				<label for="<?php echo esc_attr( $option_id ); ?>">
					<input class="" type="checkbox" name="<?php echo esc_attr( $option_id ); ?>"
					id="<?php echo esc_attr( $option_id ); ?>"
					value="on" 
					<?php
					if ( get_option( $option_id, $option_values['value'] ) == 'on' ) {
						echo esc_attr( 'checked' );
					}
					?>
					>
					<?php echo esc_html( $option_values['details'] ); ?>
				</label>
			</tr>
			<?php
	}

	/*
	 * Create text filed for snap marketing configuration option
	 */
	public function configuration_select_fields( $option_values, $option_id ) {
		$select_value = get_option( $option_id, $option_values['value'] );

		?>
		<tr valign="top" id="<?php echo esc_attr( $option_id ); ?>_tr">
			<th scope="row"><?php echo esc_attr( $option_values['label'] ); ?></th>
			<td>
				<select id="<?php echo esc_attr( $option_id ); ?>" <?php echo esc_attr( $option_values['readonly'] ); ?> name="<?php echo esc_attr( $option_id ); ?>">
					<?php
					foreach ( $option_values['options'] as $option_key => $option_value ) {
						$select_data = '';
						if ( $select_value == $option_key ) {
							$select_data = 'selected';
						}
						echo '<option value="' . esc_attr( $option_key ) . '" ' . esc_attr( $select_data ) . ' > ' . esc_html( $option_value ) . ' </option>';
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	}

	/*
	* Register treatments post type on admin side
	*/
	public function create_treatments_post_type() {

		// Set UI labels for Treatments Post Type
		$labels = array(
			'name'               => _x( 'Treatments', 'Post Type General Name', 'snap-marketing' ),
			'singular_name'      => _x( 'Treatment', 'Post Type Singular Name', 'snap-marketing' ),
			'menu_name'          => __( 'Snap Marketing', 'snap-marketing' ),
			'parent_item_colon'  => __( 'Parent Treatment', 'snap-marketing' ),
			'all_items'          => __( 'All Treatments', 'snap-marketing' ),
			'view_item'          => __( 'View Treatment', 'snap-marketing' ),
			'add_new_item'       => __( 'Add New Treatment', 'snap-marketing' ),
			'add_new'            => __( 'Add New', 'snap-marketing' ),
			'edit_item'          => __( 'Edit Treatment', 'snap-marketing' ),
			'update_item'        => __( 'Update Treatment', 'snap-marketing' ),
			'search_items'       => __( 'Search Treatment', 'snap-marketing' ),
			'not_found'          => __( 'Not Found', 'snap-marketing' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'snap-marketing' ),
		);

		// Set other options for Treatments Post Type

		$args = array(
			'label'               => __( 'Treatments', 'snap-marketing' ),
			'description'         => __( 'Snap Treatments', 'snap-marketing' ),
			'labels'              => $labels,
			// Features this Treatments supports in Post Editor
			'supports'            => array( 'title' ),
			/*
			 A hierarchical Treatments is like Pages and can have
			* Parent and child items. A non-hierarchical Treatments
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,

		);

		// Registering treatments Post Type
		register_post_type( 'snap_treatments', $args );
	}

	/*
	 * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
	 */
	public function snap_marketing_scripts() {

		global $post;
		$load_style = false;
		if ( isset( $_GET['page'] ) ) {
			if ( $_GET['page'] == 'snap-marketing-configuration' ) {
				$load_style = true;
			}
		}
		if ( isset( $_GET['post_type'] ) ) {
			if ( $_GET['post_type'] == 'snap_treatments' ) {
				$load_style = true;
			}
		}
		if ( $post ) {
			if ( $post->post_type == 'snap_treatments' ) {
				$load_style = true;
			}
		}
		if ( $load_style ) {
			wp_enqueue_script( 'snap-marketing-admin-script', plugins_url( 'assets/js/snap-marketing-admin.js', __FILE__ ), array( 'jquery' ), 1.1, true );
			wp_localize_script( 'snap-marketing-admin-script', 'snap_marketing', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_style( 'snap-marketing-admin-style', plugins_url( 'assets/css/snap-marketing-admin.css', __FILE__ ), array(), '', false );
		}
	}

}

$snap_marketing_admin_side = new snap_marketing_admin_side();
