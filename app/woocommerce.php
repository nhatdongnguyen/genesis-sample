<?php
/**
 * Genesis Sample.
 *
 * This file adds the required WooCommerce setup functions to the Genesis Sample Theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

// Adds product gallery support.
if ( class_exists( 'WooCommerce' ) ) {

	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-zoom' );

}

add_action( 'wp_enqueue_scripts', 'genesis_sample_products_match_height', 99 );
/**
 * Prints an inline script to the footer to keep products the same height.
 *
 * @since 2.3.0
 */
function genesis_sample_products_match_height() {

	// If Woocommerce is not activated, or a product page isn't showing, exit early.
	if ( ! class_exists( 'WooCommerce' ) || ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}

	wp_enqueue_script(
		'genesis-sample-match-height',
		get_stylesheet_directory_uri() . '/js/jquery.matchHeight.min.js',
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);
	wp_add_inline_script(
		'genesis-sample-match-height',
		"jQuery(document).ready( function() { jQuery( '.product .woocommerce-LoopProduct-link').matchHeight(); });"
	);

}

add_filter( 'woocommerce_style_smallscreen_breakpoint', 'genesis_sample_woocommerce_breakpoint' );
/**
 * Modifies the WooCommerce breakpoints.
 *
 * @since 2.3.0
 *
 * @return string Pixel width of the theme's breakpoint.
 */
function genesis_sample_woocommerce_breakpoint() {

	$current = genesis_site_layout();
	$layouts = array(
		'one-sidebar' => array(
			'content-sidebar',
			'sidebar-content',
		),
	);

	if ( in_array( $current, $layouts['one-sidebar'], true ) ) {
		return '1200px';
	}

	return '860px';

}

add_filter( 'genesiswooc_products_per_page', 'genesis_sample_default_products_per_page' );
/**
 * Sets the default products per page.
 *
 * @since 2.3.0
 *
 * @return int Number of products to show per page.
 */
function genesis_sample_default_products_per_page() {

	return 8;

}

add_filter( 'woocommerce_pagination_args', 'genesis_sample_woocommerce_pagination' );
/**
 * Updates the next and previous arrows to the default Genesis style.
 *
 * @param array $args The previous and next text arguments.
 *
 * @since 2.3.0
 *
 * @return array New next and previous text arguments.
 */
function genesis_sample_woocommerce_pagination( $args ) {

	$args['prev_text'] = sprintf( '&laquo; %s', __( 'Previous Page', 'genesis-sample' ) );
	$args['next_text'] = sprintf( '%s &raquo;', __( 'Next Page', 'genesis-sample' ) );

	return $args;

}

add_action( 'after_switch_theme', 'genesis_sample_woocommerce_image_dimensions_after_theme_setup', 1 );
/**
 * Defines WooCommerce image sizes on theme activation.
 *
 * @since 2.3.0
 */
function genesis_sample_woocommerce_image_dimensions_after_theme_setup() {

	global $pagenow;

	// Checks conditionally to see if we're activating the current theme and that WooCommerce is installed.
	if ( ! isset( $_GET['activated'] ) || 'themes.php' !== $pagenow || ! class_exists( 'WooCommerce' ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- low risk, follows official snippet at https://goo.gl/nnHHQa.
		return;
	}

	genesis_sample_update_woocommerce_image_dimensions();

}

add_action( 'activated_plugin', 'genesis_sample_woocommerce_image_dimensions_after_woo_activation', 10, 2 );
/**
 * Defines the WooCommerce image sizes on WooCommerce activation.
 *
 * @since 2.3.0
 *
 * @param string $plugin The path of the plugin being activated.
 */
function genesis_sample_woocommerce_image_dimensions_after_woo_activation( $plugin ) {

	// Checks to see if WooCommerce is being activated.
	if ( 'woocommerce/woocommerce.php' !== $plugin ) {
		return;
	}

	genesis_sample_update_woocommerce_image_dimensions();

}

/**
 * Updates WooCommerce image dimensions.
 *
 * @since 2.3.0
 */
function genesis_sample_update_woocommerce_image_dimensions() {

	// Updates image size options.
	update_option( 'woocommerce_single_image_width', 655 );    // Single product image.
	update_option( 'woocommerce_thumbnail_image_width', 500 ); // Catalog image.

	// Updates image cropping option.
	update_option( 'woocommerce_thumbnail_cropping', '1:1' );

}

add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'genesis_sample_gallery_image_thumbnail' );
/**
 * Filters the WooCommerce gallery image dimensions.
 *
 * @since 2.6.0
 *
 * @param array $size The gallery image size and crop arguments.
 *
 * @return array The modified gallery image size and crop arguments.
 */
function genesis_sample_gallery_image_thumbnail( $size ) {

	$size = array(
		'width'  => 180,
		'height' => 180,
		'crop'   => 1,
	);

	return $size;

}


add_filter( 'woocommerce_enqueue_styles', 'genesis_sample_woocommerce_styles' );
/**
 * Enqueues the theme's custom WooCommerce styles to the WooCommerce plugin.
 *
 * @param array $enqueue_styles The WooCommerce styles to enqueue.
 *
 * @since 2.3.0
 *
 * @return array Modified WooCommerce styles to enqueue.
 */
function genesis_sample_woocommerce_styles( $enqueue_styles ) {

	$enqueue_styles['genesis-sample-woocommerce'] = array(
		'src'     => get_stylesheet_directory_uri() . '/lib/woocommerce/woocommerce.css',
		'deps'    => '',
		'version' => CHILD_THEME_VERSION,
		'media'   => 'screen',
	);

	return $enqueue_styles;

}

add_action( 'wp_enqueue_scripts', 'genesis_sample_woocommerce_css' );
/**
 * Adds the themes's custom CSS to the WooCommerce stylesheet.
 *
 * @since 2.3.0
 *
 * @return string CSS to be outputted after the theme's custom WooCommerce stylesheet.
 */
function genesis_sample_woocommerce_css() {

	// If WooCommerce isn't active, exit early.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$color_link   = get_theme_mod( 'genesis_sample_link_color', genesis_sample_customizer_get_default_link_color() );
	$color_accent = get_theme_mod( 'genesis_sample_accent_color', genesis_sample_customizer_get_default_accent_color() );

	$woo_css = '';

	$woo_css .= ( genesis_sample_customizer_get_default_link_color() !== $color_link ) ? sprintf(
		'

		.woocommerce div.product p.price,
		.woocommerce div.product span.price,
		.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover,
		.woocommerce div.product .woocommerce-tabs ul.tabs li a:focus,
		.woocommerce ul.products li.product h3:hover,
		.woocommerce ul.products li.product .price,
		.woocommerce .woocommerce-breadcrumb a:hover,
		.woocommerce .woocommerce-breadcrumb a:focus,
		.woocommerce .widget_layered_nav ul li.chosen a::before,
		.woocommerce .widget_layered_nav_filters ul li a::before,
		.woocommerce .widget_rating_filter ul li.chosen a::before {
			color: %s;
		}

	',
		$color_link
	) : '';

	$woo_css .= ( genesis_sample_customizer_get_default_accent_color() !== $color_accent ) ? sprintf(
		'
		.woocommerce a.button:hover,
		.woocommerce a.button:focus,
		.woocommerce a.button.alt:hover,
		.woocommerce a.button.alt:focus,
		.woocommerce button.button:hover,
		.woocommerce button.button:focus,
		.woocommerce button.button.alt:hover,
		.woocommerce button.button.alt:focus,
		.woocommerce input.button:hover,
		.woocommerce input.button:focus,
		.woocommerce input.button.alt:hover,
		.woocommerce input.button.alt:focus,
		.woocommerce input[type="submit"]:hover,
		.woocommerce input[type="submit"]:focus,
		.woocommerce span.onsale,
		.woocommerce #respond input#submit:hover,
		.woocommerce #respond input#submit:focus,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce #respond input#submit.alt:focus,
		.woocommerce.widget_price_filter .ui-slider .ui-slider-handle,
		.woocommerce.widget_price_filter .ui-slider .ui-slider-range {
			background-color: %1$s;
			color: %2$s;
		}

		.woocommerce-error,
		.woocommerce-info,
		.woocommerce-message {
			border-top-color: %1$s;
		}

		.woocommerce-error::before,
		.woocommerce-info::before,
		.woocommerce-message::before {
			color: %1$s;
		}

	',
		$color_accent,
		genesis_sample_color_contrast( $color_accent )
	) : '';

	if ( $woo_css ) {
		wp_add_inline_style( 'genesis-sample-woocommerce', $woo_css );
	}

}


add_action( 'admin_print_styles', 'genesis_sample_remove_woocommerce_notice' );
/**
 * Removes the default WooCommerce Notice.
 *
 * @since 2.3.0
 */
function genesis_sample_remove_woocommerce_notice() {

	// If below version WooCommerce 2.3.0, exit early.
	if ( ! class_exists( 'WC_Admin_Notices' ) ) {
		return;
	}

	WC_Admin_Notices::remove_notice( 'theme_support' );

}

add_action( 'admin_notices', 'genesis_sample_woocommerce_theme_notice' );
/**
 * Adds a prompt to activate Genesis Connect for WooCommerce
 * if WooCommerce is active but Genesis Connect is not.
 *
 * @since 2.3.0
 */
function genesis_sample_woocommerce_theme_notice() {

	// If WooCommerce isn't installed or Genesis Connect is installed, exit early.
	if ( ! class_exists( 'WooCommerce' ) || function_exists( 'gencwooc_setup' ) ) {
		return;
	}

	// If user doesn't have access, exit early.
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	// If message dismissed, exit early.
	if ( get_user_option( 'genesis_sample_woocommerce_message_dismissed', get_current_user_id() ) ) {
		return;
	}

	/* translators: %s: child theme name */
	$notice_html = sprintf( __( 'Please install and activate <a href="https://wordpress.org/plugins/genesis-connect-woocommerce/" target="_blank">Genesis Connect for WooCommerce</a> to <strong>enable WooCommerce support for %s</strong>.', 'genesis-sample' ), esc_html( CHILD_THEME_NAME ) );

	if ( current_user_can( 'install_plugins' ) ) {
		$plugin_slug  = 'genesis-connect-woocommerce';
		$admin_url    = network_admin_url( 'update.php' );
		$install_link = sprintf(
			'<a href="%s">%s</a>',
			wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $plugin_slug,
					),
					$admin_url
				),
				'install-plugin_' . $plugin_slug
			),
			__( 'install and activate Genesis Connect for WooCommerce', 'genesis-sample' )
		);

		/* translators: 1: plugin install prompt presented as link, 2: child theme name */
		$notice_html = sprintf( __( 'Please %1$s to <strong>enable WooCommerce support for %2$s</strong>.', 'genesis-sample' ), $install_link, esc_html( CHILD_THEME_NAME ) );
	}

	echo '<div class="notice notice-info is-dismissible genesis-sample-woocommerce-notice"><p>' . wp_kses_post( $notice_html ) . '</p></div>';

}

add_action( 'wp_ajax_genesis_sample_dismiss_woocommerce_notice', 'genesis_sample_dismiss_woocommerce_notice' );
/**
 * Adds option to dismiss Genesis Connect for Woocommerce plugin install prompt.
 *
 * @since 2.3.0
 */
function genesis_sample_dismiss_woocommerce_notice() {

	update_user_option( get_current_user_id(), 'genesis_sample_woocommerce_message_dismissed', 1 );

}

add_action( 'admin_enqueue_scripts', 'genesis_sample_notice_script' );
/**
 * Enqueues script to clear the Genesis Connect for WooCommerce plugin install prompt on dismissal.
 *
 * @since 2.3.0
 */
function genesis_sample_notice_script() {

	wp_enqueue_script( 'genesis_sample_notice_script', get_stylesheet_directory_uri() . '/app/woocommerce/js/notice-update.js', array( 'jquery' ), '1.0', true );

}

add_action( 'switch_theme', 'genesis_sample_reset_woocommerce_notice', 10, 2 );
/**
 * Clears the Genesis Connect for WooCommerce plugin install prompt on theme change.
 *
 * @since 2.3.0
 */
function genesis_sample_reset_woocommerce_notice() {

	global $wpdb;

	$args  = array(
		'meta_key'   => $wpdb->prefix . 'genesis_sample_woocommerce_message_dismissed',
		'meta_value' => 1,
	);
	$users = get_users( $args );

	foreach ( $users as $user ) {
		delete_user_option( $user->ID, 'genesis_sample_woocommerce_message_dismissed' );
	}

}

add_action( 'deactivated_plugin', 'genesis_sample_reset_woocommerce_notice_on_deactivation', 10, 2 );
/**
 * Clears the Genesis Connect for WooCommerce plugin prompt on deactivation.
 *
 * @since 2.3.0
 *
 * @param string $plugin               The plugin slug.
 * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network. or just the
 *                                     current site.
 */
function genesis_sample_reset_woocommerce_notice_on_deactivation( $plugin, $network_deactivating ) {

	// Conditional check to see if we're deactivating WooCommerce or Genesis Connect for WooCommerce.
	if ( 'woocommerce/woocommerce.php' !== $plugin && 'genesis-connect-woocommerce/genesis-connect-woocommerce.php' !== $plugin ) {
		return;
	}

	genesis_sample_reset_woocommerce_notice();

}
