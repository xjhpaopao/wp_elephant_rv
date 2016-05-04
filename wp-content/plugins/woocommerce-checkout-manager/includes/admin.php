<?php
if( is_admin() ) {
	// backend scripts
	add_action( 'admin_enqueue_scripts', 'wooccm_admin_enqueue_scripts' );
	// List of action links on the Plugins screen
	add_filter( sprintf( 'plugin_action_links_%s', WOOCCM_RELPATH ), 'wooccm_admin_plugin_actions', -10);
	// WordPress Settings screen for WooCheckout
	add_action( 'admin_init', 'wooccm_register_settings' );
}

// WordPress Administration menu
function wooccm_admin_menu() {

	add_menu_page( 'WooCheckout', 'WooCheckout', 'manage_options', 'woocommerce-checkout-manager' , 'wooccm_options_page', 'dashicons-businessman', 57);
	add_submenu_page( 'woocommerce-checkout-manager', 'Export', 'Export', 'manage_options', 'wooccm-advance-export', 'wooccm_advance_export' );

}
add_action( 'admin_menu', 'wooccm_admin_menu' );

function wooccm_admin_enqueue_scripts( $hook_suffix ) {

	if( $hook_suffix == 'toplevel_page_woocommerce-checkout-manager' ) {
		wp_enqueue_style( 'farbtastic' );
		// @mod - We need to check this file exists
		wp_enqueue_script( 'farbtastic', site_url('/wp-admin/js/farbtastic.js') );   
		wp_enqueue_style( 'wooccm-backend-css', plugins_url( 'includes/pickers/css/backend_css.css', WOOCCM_RELPATH ) );
		wp_enqueue_script( 'script_wccs', plugins_url( 'includes/templates/js/script_wccs.js', WOOCCM_RELPATH ), array( 'jquery' ), '1.2' );
		wp_enqueue_script( 'billing_script_wccs', plugins_url( 'includes/templates/js/billing_script_wccs.js', WOOCCM_RELPATH ), array( 'jquery' ), '1.2' );
		wp_enqueue_script( 'shipping_script_wccs', plugins_url( 'includes/templates/js/shipping_script_wccs.js', WOOCCM_RELPATH ), array( 'jquery' ), '1.2' );
		if( !wp_script_is('jquery-ui-sortable', 'queue') )
			wp_enqueue_script('jquery-ui-sortable');
	}
	if( $hook_suffix === 'woocheckout_page_wooccm-advance-export')
		wp_enqueue_style( 'export', plugins_url( 'includes/classes/sc/export.css', WOOCCM_RELPATH ) );

}

// List of action links on the Plugins screen
function wooccm_admin_plugin_actions( $links ) {

	$page_url = add_query_arg( 'page', 'woocommerce-checkout-manager', 'admin.php' );
	$support_url = 'https://wordpress.org/support/plugin/woocommerce-checkout-manager/';

	$wccs_plugin_links = array(
		'<a href="' . $page_url . '">'.__('Settings', 'woocommerce-checkout-manager' ).'</a>',
		'<a href="' . $support_url . '">'.__('Support', 'woocommerce-checkout-manager' ).'</a>',
	);
	return array_merge( $wccs_plugin_links, $links );

}

// WordPress Settings screen for WooCheckout
function wooccm_register_settings() {

	register_setting( 'wccs_options', 'wccs_settings', 'wooccm_options_validate' );
	register_setting( 'wccs_options2', 'wccs_settings2', 'wooccm_options_validate_shipping' );
	register_setting( 'wccs_options3', 'wccs_settings3', 'wooccm_options_validate_billing' );

	add_action( 'admin_notices', 'wooccm_display_notices' );


}

function wooccm_options_page() {

	if ( !current_user_can('manage_options') )
		wp_die( __('You do not have sufficient permissions to access this page.', 'woocommerce-checkout-manager') ); 

	$htmlshippingabbr = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode' );
	$htmlbillingabbr = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'email', 'phone' );
	$upload_dir = wp_upload_dir();
	$hidden_field_name = 'mccs_submit_hidden';
	$hidden_wccs_reset = "my_new_field_reset";
	$options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
	$options3 = get_option( 'wccs_settings3' );

	// Check if the reset button has been clicked
	if( isset($_POST[ $hidden_wccs_reset ]) && sanitize_text_field( $_POST[ $hidden_wccs_reset ] ) == 'Y' ) {
		delete_option('wccs_settings');
		delete_option('wccs_settings2');
		delete_option('wccs_settings3');
		$defaults = array(
			'checkness' => array(
				'position' => 'after_billing_form',
				'wooccm_notification_email' => ''.get_option('admin_email').'',
				'payment_method_t' => true,
				'shipping_method_t' => true,
				'payment_method_d' => __('Payment Method','woocommerce-checkout-manager'),
				'shipping_method_d' => __('Shipping Method','woocommerce-checkout-manager'),
				'time_stamp_title' => __('Order Time','woocommerce-checkout-manager'),
			),
		);

		$shipping = array(
			'country' => 'Country', 
			'first_name' => 'First Name', 
			'last_name' => 'Last Name', 
			'company' => 'Company Name', 
			'address_1' => 'Address', 
			'address_2' => '', 
			'city' => 'Town/ City', 
			'state' => 'State', 
			'postcode' => 'Zip'
		);
		$ship = 0;
		foreach( $shipping as $name => $value ) {

			$defaults2['shipping_buttons'][$ship]['label'] = __(''.$value.'', 'woocommerce');
			$defaults2['shipping_buttons'][$ship]['cow'] = $name;
			$defaults2['shipping_buttons'][$ship]['checkbox']  = 'true';
			$defaults2['shipping_buttons'][$ship]['order'] = $ship + 1;
			$defaults2['shipping_buttons'][$ship]['type'] = 'wooccmtext';

			switch( $name ) {

				case 'country':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
					break;

				case 'first_name':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-first';
					break;

				case 'last_name':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-last';
					$defaults2['shipping_buttons'][$ship]['clear_row'] = true;
					break;

				case 'company':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
					break;

				case 'address_1':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
					$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Street address', 'woocommerce');
					break;

				case 'address_2':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
					$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
					break;

				case 'city':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
					$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Town / City', 'woocommerce');
					break;

				case 'state':
					$defaults2['shipping_buttons'][$ship]['position'] = 'form-row-first';
					break;

			}

			$ship++;

		}

		$billing = array(
			'country' => 'Country', 
			'first_name' => 'First Name', 
			'last_name' => 'Last Name', 
			'company' => 'Company Name', 
			'address_1' => 'Address', 
			'address_2' => '', 
			'city' => 'Town/ City', 
			'state' => 'State', 
			'postcode' => 'Zip', 
			'email' => 'Email Address', 
			'phone' => 'Phone'
		);

		$bill = 0;

		foreach( $billing as $name => $value ) {

			$defaults3['billing_buttons'][$bill]['label'] = __(''.$value.'', 'woocommerce');
			$defaults3['billing_buttons'][$bill]['cow'] = $name;
			$defaults3['billing_buttons'][$bill]['checkbox']  = 'true';
			$defaults3['billing_buttons'][$bill]['order'] = $bill + 1;	
			$defaults3['billing_buttons'][$bill]['type'] = 'wooccmtext';

			switch( $name ) {

				case 'country':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
					break;

				case 'first_name':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
					break;

				case 'last_name':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
					$defaults3['billing_buttons'][$bill]['clear_row'] = true;
					break;

				case 'company':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
					break;

				case 'address_1':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
					$defaults3['billing_buttons'][$bill]['placeholder'] = __('Street address', 'woocommerce');
					break;

				case 'address_2':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
					$defaults3['billing_buttons'][$bill]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
					break;

				case 'city':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
					$defaults3['billing_buttons'][$bill]['placeholder'] = __('Town / City', 'woocommerce');
					break;

				case 'state':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
					break;

				case 'postcode':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
					$defaults3['billing_buttons'][$bill]['placeholder'] = __('Postcode / Zip', 'woocommerce');
					$defaults3['billing_buttons'][$bill]['clear_row'] = true;
					break;

				case 'email':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
					break;

				case 'phone':
					$defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
					$defaults3['billing_buttons'][$bill]['clear_row'] = true;
					break;

			}

			$bill++;

		}
		add_option( 'wccs_settings' , $defaults );
		add_option( 'wccs_settings2' , $defaults2 );
		add_option( 'wccs_settings3' , $defaults3 );

		// @mod - Change this to add_query_arg()
		echo '
<script type="text/javascript">window.location.href="'.$_SERVER['PHP_SELF'].'?page=woocommerce-checkout-manager";</script>';
		echo '
<noscript><meta http-equiv="refresh" content="0;url='.$_SERVER['PHP_SELF'].'?page=woocommerce-checkout-manager" /></noscript>';
		exit;

	}
	echo '
<script type="text/javascript" src="'.plugins_url( '/woocommerce/assets/js/jquery-blockui/jquery.blockUI.js' ).'"></script>';
	echo '
<div class="refreshwooccm">
';

	// display error
	settings_errors();

	// Now display the settings editing screen

	// header
?>
<h2><?php _e( 'WooCommerce Checkout Manager', 'woocommerce-checkout-manager' ); ?></h2>
<div id="content">

	<h2 class="nav-tab-wrapper add_tip_wrap">
		<a class="nav-tab general-tab nav-tab-active"><?php _e( 'General', 'woocommerce-checkout-manager' ); ?></a>
		<a class="nav-tab billing-tab"><?php _e( 'Billing', 'woocommerce-checkout-manager' ); ?></a>
		<a class="nav-tab shipping-tab"><?php _e( 'Shipping', 'woocommerce-checkout-manager' ); ?></a>
		<a class="nav-tab additional-tab"><?php _e( 'Additional', 'woocommerce-checkout-manager' ); ?></a>
		<a class="nav-tab star" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-checkout-manager?filter=5" target="_blank">
			<div id="star-five" title="<?php _e('Like the plugin? Rate it! On WordPress.org', 'woocommerce-checkout-manager' ); ?>">
				<div class="star-rating">
					<div class="star star-full"></div>
					<div class="star star-full"></div>
					<div class="star star-full"></div>
					<div class="star star-full"></div>
					<div class="star star-full"></div>
				</div>
				<!-- .star-rating -->
			</div>
			<!-- #star-five -->
		</a>
	</h2>
	<!-- .nav-tab-wrapper -->

	<?php do_action('wooccm_run_color_innerpicker'); ?>

	<form name="reset_form" class="reset_form" method="post" action="">
		<input type="hidden" name="<?php echo esc_attr( $hidden_wccs_reset ); ?>" value="Y">
		<input type="submit" name="submit" id="wccs_reset_submit" class="button button-hero" value="Reset">
	</form>
	<script type="text/javascript">
		jQuery( '#wccs_reset_submit' ).click( 'click', function() {
			return window.confirm( '<?php echo esc_js( __( 'Are you sure you wish to reset the settings on this tab for WooCommerce Checkout Manager?', 'woocommerce-checkout-manager' ) ); ?>' );
		});
	</script>

<?php require( WOOCCM_PATH.'includes/classes/import.php'); ?>

	<div class="wrap">
		<form name="wooccmform2" method="post" action="options.php" id="frm2">

			<?php settings_fields( 'wccs_options2' ); ?>

			<input type="submit" style="display:none;" name="Submit" class="save-shipping wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />

			<!-- SHIPPING SECTION -->

			<table class="widefat shipping-wccs-table shipping-semi" style="display:none;" border="1" name="shipping_table">
				<thead>
					<tr>
						<th style="width:3%;" class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

						<?php require( WOOCCM_PATH.'includes/templates/htmlheadship.php' ); ?>

						<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
					</tr>
				</thead>
				<tbody>

<?php
	if ( isset ( $options2['shipping_buttons'] ) ) {
		$shipping = array(
			'country', 
			'first_name', 
			'last_name', 
			'company', 
			'address_1', 
			'address_2', 
			'city', 
			'state', 
			'postcode'
		);
		for ( $ix = 0; $ix < count( $options2['shipping_buttons'] ); $ix++ ) {

			if ( ! isset( $options2['shipping_buttons'][$ix] ) )
				break;
?>

					<tr valign="top" class="shipping-wccs-row">

						<td style="display:none;" class="shipping-wccs-order-hidden" >
							<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['order'])) ? $ix :  $options2['shipping_buttons'][$ix]['order']; ?>" />
						</td>
						<td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix+1; ?></td>

<?php require(WOOCCM_PATH.'includes/templates/htmlbodyship.php'); ?>

<?php if( in_array( $options2['shipping_buttons'][$ix]['cow'],$shipping) ) { ?>
						<td style="text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['disabled'])) echo "checked='checked'"; ?> /></td>
<?php } else { ?>
						<td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;" >&times;</a></td>
<?php } ?>

					</tr>

<?php 
		}
	}
?>
<!-- Empty -->

<?php
	$ix = 999;
?>

					<tr valign="top" class="shipping-wccs-clone" >
						<td style="display:none;" class="shipping-wccs-order-hidden" >
							<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo $ix; ?>" />
						</td>

						<td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix; ?></td>

						<?php require( WOOCCM_PATH.'includes/templates/htmlbodycloneship.php' ); ?>

						<td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;">&times;</a></td>
					</tr>
				</tbody>
			</table>
			<!-- .widefat -->

			<div class="shipping-wccs-table-footer shipping-semi" style="display:none;">
				<a href="javascript:;" id="shipping-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
			</div>
			<!-- END SHIPPING SECTION -->

		</form>
		<!-- #frm2 -->

		<form name="wooccmform3" method="post" action="options.php" id="frm3">

			<?php settings_fields( 'wccs_options3' ); ?>

			<input type="submit" name="Submit" style="display:none;" class="save-billing wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />

			<!-- BILLING SECTION -->
			<table class="widefat billing-wccs-table billing-semi" style="display:none;" border="1" name="billing_table">
				<thead>
					<tr>
						<th style="width:3%;" class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

						<?php require( WOOCCM_PATH.'includes/templates/htmlheadbill.php' ); ?>

						<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
					</tr>
				</thead>
				<tbody>
<?php
	if ( isset ( $options3['billing_buttons'] ) ) {
		$billing = array(
			'country', 
			'first_name', 
			'last_name', 
			'company', 
			'address_1', 
			'address_2', 
			'city', 
			'state', 
			'postcode', 
			'email', 
			'phone'
		);
		for ( $i = 0; $i < count( $options3['billing_buttons'] ); $i++ ) {

			if ( ! isset( $options3['billing_buttons'][$i] ) )
				break;
?>

					<tr valign="top" class="billing-wccs-row">

						<td style="display:none;" class="billing-wccs-order-hidden" >
							<input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo (empty( $options3['billing_buttons'][$i]['order'])) ? $i :  $options3['billing_buttons'][$i]['order']; ?>" />
						</td>
						<td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $i+1; ?></td>

						<?php require( WOOCCM_PATH.'includes/templates/htmlbodybill.php' ); ?>

<?php if( in_array($options3['billing_buttons'][$i]['cow'], $billing) ) { ?>
						<td style="text-align:center;"><input name="wccs_settings3[billing_buttons][<?php echo $i; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options3['billing_buttons'][$i]['disabled'])) echo "checked='checked'"; ?> /></td>
<?php } else { ?>
						<td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;">&times;</a></td>
<?php } ?>

					</tr>

<?php
		}
	}
?>
<!-- Empty -->

<?php
	$i = 999;
?>

					<tr valign="top" class="billing-wccs-clone" >
						<td style="display:none;" class="billing-wccs-order-hidden"><input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo $i; ?>" /></td>
						<td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $i; ?></td>

						<?php require( WOOCCM_PATH.'includes/templates/htmlbodyclonebill.php' ); ?>

						<td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;" >&times;</a></td>
					</tr>
				</tbody>
			</table>
			<!-- .widefat -->

			<div class="billing-wccs-table-footer billing-semi" style="display:none;">
				<a href="javascript:;" id="billing-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
			</div>

			<!-- END BILLING SECTION -->

		</form>
		<!-- #frm3 -->

		<form name="wooccmform" method="post" action="options.php" id="frm1">

			<?php settings_fields( 'wccs_options' ); ?>

			<input type="submit" name="Submit" class="save-additional wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />

			<div id="general-semi-nav">

				<div id="main-nav-left">
					<ul>
						<li class="upload_class current"><a title="Upload">Upload</a></li>
						<li class="address_fields_class"><a title="Address Fields">Hide Address Fields</a></li>
						<li class="checkout_notice_class"><a title="Checkout Notice">Checkout Notice</a></li>
						<li class="switches_class"><a title="Switches">Switches</a></li>
						<li class="order_notes_class"><a title="Order Notes">Handlers</a></li>
						<li class="custom_css_class"><a title="Custom CSS">Custom CSS</a></li>
					</ul>
				</div>
				<!-- #main-nav-left -->
				<div id="content-nav-right" class="general-vibe">

<?php
	// file upload options section
	require( WOOCCM_PATH.'includes/classes/file_upload/upload_settings.php' ); 
?>

					<!-- ADDITIONAL SECTION -->

					<table class="widefat wccs-table additional-semi" style="display:none;" border="1" name="additional_table">
						<thead>
							<tr>
								<th style="width:3%;" class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>

								<?php require( WOOCCM_PATH.'includes/templates/htmlheadadd.php' ); ?>

								<th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
							</tr>
						</thead>
						<tbody>

<?php
	if ( isset ( $options['buttons'] ) ) {
		for ( $iz = 0; $iz < count( $options['buttons'] ); $iz++ ) {

			if ( ! isset( $options['buttons'][$iz] ) )
				break;
?>

							<tr valign="top" class="wccs-row">
								<td style="display:none;" class="wccs-order-hidden" >
									<input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo (empty( $options['buttons'][$iz]['order'])) ? $iz :  $options['buttons'][$iz]['order']; ?>" />
								</td>
								<td class="wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $iz+1; ?></td>

								<?php require( WOOCCM_PATH.'includes/templates/htmlbodyadd.php' ); ?>

								<td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>
							</tr>

<?php
		}
	}
?>
<!-- Empty -->

<?php
	$iz = 999;
?>

							<tr valign="top" class="wccs-clone" >
								<td style="display:none;" class="wccs-order-hidden"><input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo $iz; ?>" /></td>

								<td class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $iz; ?></td>

								<?php require( WOOCCM_PATH.'includes/templates/htmlbodycloneadd.php' ); ?>

								<td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>
							</tr>
						</tbody>
					</table>
					<!-- .widefat -->

					<div class="wccs-table-footer additional-semi" style="display:none;">
						<a href="javascript:;" id="wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
					</div>

					<!-- END ADDITIONAL SECTION -->

					<div class="widefat general-semi address_fields" border="1" style="display:none;">
						<div class="section">

							<h3 class="heading"><?php _e('Disable Billing Address fields for certain products', 'woocommerce-checkout-manager');  ?></h3>
							<div class="option">
								<input type="text" name="wccs_settings[checkness][productssave]" style="width: 100%;" value="<?php echo ( !empty( $options['checkness']['productssave'] ) ? sanitize_text_field( $options['checkness']['productssave'] ) : '' ); ?>" />
								<h3 class="heading address"><div class="info-of"><?php _e('To get product number, goto the listing of WooCoommerce Products then hover over each product and you will see ID. Example', 'woocommerce-checkout-manager'); ?> "ID: 3651"</div></h3>
							</div>
							<!-- .option -->

						</div>
						<!-- .section -->
					</div>
					<!-- .widefat -->

					<div class="widefat general-semi order_notes" border="1" style="display:none;">
						<div class="section">

							<h3 class="heading"><?php _e('Order Notes','woocommerce-checkout-manager'); ?></h3>
							<div style="float:left;width: 46%;" class="option">
								<input style="width: 100%;clear:both;" name="wccs_settings[checkness][noteslabel]" type="text" value="<?php echo ( isset( $options['checkness']['noteslabel'] ) ? sanitize_text_field( $options['checkness']['noteslabel'] ) : '' ); ?>" />
								<div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order Notes Label', 'woocommerce-checkout-manager');  ?></div>
							</div>

							<div style="float:left;width: 47%;" class="option">
								<input style="width: 100%;clear:both;" name="wccs_settings[checkness][notesplaceholder]" type="text" value="<?php echo ( isset( $options['checkness']['notesplaceholder'] ) ? sanitize_text_field( $options['checkness']['notesplaceholder'] ) : '' ); ?>" />
								<div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order Notes Placeholder', 'woocommerce-checkout-manager');  ?></div>
							</div>

							<h3 class="heading checkbox" style="clear:both;">
							<div class="option">
								<input name="wccs_settings[checkness][notesenable]" type="checkbox" value="true"<?php checked( !empty( $options['checkness']['notesenable'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Disable Order Notes.', 'woocommerce-checkout-manager');  ?></div>
							</div>
							</h3>

					</div>
					<!-- .section -->

					<div class="section">

						<h3 class="heading"><?php _e('Time order was purchased', 'woocommerce-checkout-manager');  ?></h3>
						<div style="float:left;width: 46%;" class="option">
							<input style="width: 100%;clear:both;" name="wccs_settings[checkness][time_stamp_title]" type="text" value="<?php echo ( !empty( $options['checkness']['time_stamp_title'] ) ? sanitize_text_field( $options['checkness']['time_stamp_title'] ) : '' ); ?>" />
							<div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order time title', 'woocommerce-checkout-manager');  ?></div>
						</div>

						<div style="float:left;width: 47%;" class="option">
							<input style="width: 100%;clear:both;" name="wccs_settings[checkness][set_timezone]" type="text" value="<?php echo ( !empty( $options['checkness']['set_timezone'] ) ? sanitize_text_field( $options['checkness']['set_timezone'] ) : '' ); ?>" />
							<div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Set TimeZone', 'woocommerce-checkout-manager');  ?></div>
						</div>

						<h3 class="heading checkbox" style="clear:both;">
							<div class="option">
								<input name="wccs_settings[checkness][time_stamp]" type="checkbox" value="true"<?php checked( !empty( $options['checkness']['time_stamp'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Enable display of order time.', 'woocommerce-checkout-manager');  ?></div>
							</div>
							<div class="option">
								<input name="wccs_settings[checkness][twenty_hour]" type="checkbox" value="true"<?php checked( !empty( $options['checkness']['twenty_hour]'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Enable 24 hour time.', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>

					</div>
					<!-- .section -->

					<div class="section">

						<h3 class="heading"><?php _e('Payment method used by customer', 'woocommerce-checkout-manager');  ?></h3>
						<div class="option">
							<input style="width: 50%;" name="wccs_settings[checkness][payment_method_d]" type="text" value="<?php echo ( !empty( $options['checkness']['payment_method_d'] ) ? sanitize_text_field( $options['checkness']['payment_method_d'] ) : '' ); ?>" />
						</div>

						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][payment_method_t]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['payment_method_t'] ), true ) ?> /><span></span>
								<div class="info-of"><?php _e('Enable display of payment method.', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- .section -->

					<div class="section">

						<h3 class="heading"><?php _e('Shipping method used by customer', 'woocommerce-checkout-manager');  ?></h3>
						<div class="option">
							<input style="width: 50%;" name="wccs_settings[checkness][shipping_method_d]" type="text" value="<?php echo ( !empty( $options['checkness']['shipping_method_d'] ) ? sanitize_text_field( $options['checkness']['shipping_method_d'] ) : '' ) ?>" />
						</div>

						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][shipping_method_t]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['shipping_method_t'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Enable display of shipping method.', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>

					</div>
					<!-- .section -->

					<div class="section">

						<h3 class="heading"><?php _e('Default sate code for checkout.', 'woocommerce-checkout-manager');  ?></h3>
						<div class="option">
							<input placeholder="ND" style="width: 50%;" name="wccs_settings[checkness][per_state]" type="text" value="<?php echo ( !empty( $options['checkness']['per_state'] ) ? sanitize_text_field( $options['checkness']['per_state'] ) : '' ); ?>" />
						</div>

						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][per_state_check]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['per_state_check'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Enable default state code.', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>

					</div>
					<!-- .section -->

					<br />

				</div>
				<!-- .widefat -->

				<div class="widefat general-semi custom_css" border="1" style="display:none;">

					<div class="section">
						<h3 class="heading"><?php _e('Custom CSS','woocommerce-checkout-manager'); ?></strong></h3>

						<h3 class="heading checkbox">
							<div class="option">
								<div class="info-of">
									<?php _e('CSS language stands for Cascading Style Sheets which is used to style html content. You can change the fonts size, colours, margins of content, which lines to show or input, adjust height, width, background images etc.','woocommerce-checkout-manager'); ?>
									<?php _e('Get help in our', 'woocommerce-checkout-manager');  ?> <a href="https://wordpress.org/support/plugin/woocommerce-checkout-manager" target="_blank"><?php _e('Support Forum', 'woocommerce-checkout-manager');  ?></a>.
								</div>
							</div>
						</h3>

						<textarea type="text" name="wccs_settings[checkness][custom_css_w]" style="height:200px;width: 100%;"><?php echo ( !empty( $options['checkness']['custom_css_w'] ) ? esc_textarea( $options['checkness']['custom_css_w'] ) : '' ); ?></textarea>

					</div>
					<!-- .section -->

				</div>
				<!-- .widefat -->

				<div class="widefat general-semi checkout_notices" border="1" style="display:none;" >

					<div class="section">
						<h3 class="heading"><?php _e('Position for notification one', 'woocommerce-checkout-manager');  ?></h3>

						<h3 class="heading checkbox">
						<div class="option">
							<input style="float:left;" name="wccs_settings[checkness][checkbox1]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['checkbox1'] ), true ); ?> /><span></span>
							<div class="info-of"><?php _e('Before Customer Address Fields', 'woocommerce-checkout-manager');  ?></div>
						</div>
						</h3>


						<h3 class="heading checkbox">
						<div class="option">
							<input style="float:left;" name="wccs_settings[checkness][checkbox2]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['checkbox2'] ), true ); ?> /><span></span>
							<div class="info-of"><?php _e('Before Order Summary', 'woocommerce-checkout-manager');  ?></div>
						</div>
						</h3>

						<div class="option">
							<div class="info-of"><?php _e('Notification text area: You can use class', 'woocommerce-checkout-manager');  ?> "woocommerce-info" <?php _e('for the same design as WooCommerce Coupon.', 'woocommerce-checkout-manager');  ?></div>
							<textarea style="width:100%;height:150px;" name="wccs_settings[checkness][text1]" type="textarea"><?php echo ( !empty( $options['checkness']['text1'] ) ? esc_attr( $options['checkness']['text1'] ) : '' ); ?></textarea>
						</div>

					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading"><?php _e('Position for notification two', 'woocommerce-checkout-manager');  ?></h3>

						<h3 class="heading checkbox">
							<div class="option">
								<input style="float:left;" name="wccs_settings[checkness][checkbox3]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['checkbox3'] ), true ); ?> /></th><span></span>
								<div class="info-of"><?php _e('Before Customer Address Fields', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>

						<h3 class="heading checkbox">
							<div class="option">
								<input style="float:left;" name="wccs_settings[checkness][checkbox4]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['checkbox4'] ), true ); ?> /></th><span></span>
								<div class="info-of"><?php _e('Before Order Summary', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>

						<div class="option">
							<div class="info-of"><?php _e('Notification text area: You can use class', 'woocommerce-checkout-manager');  ?> "woocommerce-info" <?php _e('for the same design as WooCommerce Coupon.', 'woocommerce-checkout-manager');  ?></div>
							<textarea style="width:100%;height:150px;" name="wccs_settings[checkness][text2]" type="textarea"><?php echo ( !empty( $options['checkness']['text2'] ) ? esc_attr( $options['checkness']['text2'] ) : '' ); ?></textarea>
						</div>
					</div>    
					<!-- section -->

				</div>
				<!-- .widefat -->

				<div class="widefat general-semi switches" border="1" style="display:none;">

					<div class="section"><h3 class="heading"><?php _e('General Switches', 'woocommerce-checkout-manager'); ?></h3></div>
					<div class="section">
						<h3 class="heading checkbox">  
							<div class="option">
								<input name="wccs_settings[checkness][additional_info]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['additional_info'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Remove Additional Information title', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][admin_translation]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['admin_translation'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Translate WooCCM Options Panel', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][auto_create_wccm_account]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['auto_create_wccm_account'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Hide registration checkbox', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][retainval]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['retainval'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Retain Fields Information', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox">
							<div class="option">
								<input name="wccs_settings[checkness][abbreviation]" type="checkbox" value="true" <?php checked( !empty( $options['checkness']['abbreviation'] ), true ); ?> /><span></span>
								<div class="info-of"><?php _e('Editing Of Abbreviation Fields', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section"><h3 class="heading"><?php _e('Additional Fields Positions', 'woocommerce-checkout-manager'); ?></h3></div>

					<div class="section">
						<h3 class="heading checkbox radio">
							<div class="option">
								<input name="wccs_settings[checkness][position]" type="radio" value="before_shipping_form" <?php checked( sanitize_text_field( $options['checkness']['position'] ), 'before_shipping_form' ); ?> />
								<div class="info-of"><?php _e('Before Shipping Form', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox radio">
							<div class="option">
								<input name="wccs_settings[checkness][position]" type="radio" value="after_shipping_form" <?php checked( sanitize_text_field( $options['checkness']['position'] ), 'after_shipping_form' ); ?> />
								<div class="info-of"><?php _e('After Shipping Form', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox radio">
							<div class="option">
								<input name="wccs_settings[checkness][position]" type="radio" value="before_billing_form" <?php checked( sanitize_text_field( $options['checkness']['position'] ), 'before_billing_form' ); ?> />
								<div class="info-of"><?php _e('Before Billing Form', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox radio">
							<div class="option">
								<input name="wccs_settings[checkness][position]" type="radio" value="after_billing_form" <?php checked( sanitize_text_field( $options['checkness']['position'] ), 'after_billing_form' ); ?> />
								<div class="info-of"><?php _e('After Billing Form', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

					<div class="section">
						<h3 class="heading checkbox radio">
							<div class="option">
								<input name="wccs_settings[checkness][position]" type="radio" value="after_order_notes" <?php checked( sanitize_text_field( $options['checkness']['position'] ), 'after_order_notes' ); ?> />
								<div class="info-of"><?php _e('After Order Notes', 'woocommerce-checkout-manager');  ?></div>
							</div>
						</h3>
					</div>
					<!-- section -->

				</div>
				<!-- .widefat -->

			</div>
			<!-- #content-nav-right -->

			</div>
			<!-- #general-semi-nav -->

		</form>
		<!-- #frm1 -->
	</div>
	<!-- .wrap -->

</div>
<!-- #content -->

</div>
<!-- #refreshwooccm -->

<?php 

}

function wooccm_display_notices() {

	if( in_array( get_option('wooccm_update_notice'), array( 1, 'yep' ) ) == true )
		return;

?>
<form method="post" name="clickhere" action="">
	<div id="message" class="updated settings-error click-here-wooccm">
		<p><?php _e( '<strong>WooCommerce Checkout Manager Data Update Required</strong> &#8211; We just need to update the settings for WooCommerce Checkout Manager to the latest version.', 'woocommerce-checkout-manager' ); ?></p>
<?php
	// Check whether we are on the WooCommerce Checkout Manager screen
	$screen = get_current_screen();
	if( strstr( $screen->base, 'woocommerce-checkout-manager' ) ) {
?>
		<p class="submit">
			<input type="submit" class="wooccm-update-now button-primary button-hero " value="<?php _e( 'Run the updater', 'woocommerce-checkout-manager' ); ?>" />
		</p>
<?php
	} else {
?>
		<p class="submit">
			<a href="<?php echo add_query_arg( 'page', 'woocommerce-checkout-manager' ); ?>" class="button-primary button-hero "><?php _e( 'Open WooCheckout', 'woocommerce-checkout-manager' ); ?></a>
		</p>
<?php
	}
?>
	</div>
	<!-- #message -->
	<input type="hidden" name="click-here-wooccm" value="y" />
</form>
<?php
	if( strstr( $screen->base, 'woocommerce-checkout-manager' ) ) {
?>
<script type="text/javascript">
	jQuery( '.wooccm-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'woocommerce-checkout-manager' ) ); ?>' );
	});
</script>
<?php

		if( isset($_POST['click-here-wooccm']) && sanitize_text_field( $_POST['click-here-wooccm'] ) == 'y') {
			// @mod - We need to check this file exists
?>

<!-- First Use -->
<script type="text/javascript">
	jQuery(document).ready(function($) {

		$( '#wpbody-content' ).block({message:null,overlayCSS:{background:"#fff url(<?php echo plugins_url( 'woocommerce/assets/images/ajax-loader.gif' ); ?> ) no-repeat center",opacity:.6}});

		var form = $('#frm1');
			data = $('#frm1');
			forma = $('#frm2'); 
			dataa = $('#frm2');
			formb = $('#frm3'); 
			datab = $('#frm3');
			
		$.ajax( {
			type: "POST",
			url: form.attr( 'action' ),
			data: data.serialize(),
			success: function( response ) {

				$.ajax( {
					type: "POST",
					url: forma.attr( 'action' ),
					data: dataa.serialize(),
					success: function( response ) {}
				});

				$.ajax( {
					type: "POST",
					url: formb.attr( 'action' ),
					data: datab.serialize(),
					success: function( response ) {}
				});
				$('.settings-error.click-here-wooccm').hide();
				$('#wpbody-content').unblock();

			}
		});

	});
</script>

<?php
			update_option('wooccm_update_notice', 1 );
		}

	}

}
?>