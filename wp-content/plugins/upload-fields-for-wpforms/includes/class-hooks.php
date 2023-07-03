<?php

namespace UPWPForms;

class Hooks {

	private static $instance = null;

	public function __construct() {
		add_action( 'admin_action_upwpforms-authorize', array( $this, 'handle_authorization' ) );

		//enqueue scripts and styles on wpforms admin page
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		//enqueue scripts and styles on wpforms frontend page
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

	}

	public function frontend_scripts() {
		wp_register_style( 'upwpforms-frontend', UPWPFORMS_ASSETS . '/css/frontend.css', array(), UPWPFORMS_VERSION );

		wp_register_script( 'upwpforms-frontend', UPWPFORMS_ASSETS . '/js/frontend.js', [
			'jquery',
			'wp-plupload',
			'wp-util',
			'wp-i18n',
		], UPWPFORMS_VERSION, true );

		wp_localize_script( 'upwpforms-frontend', 'upwpforms', [
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'pluginUrl' => UPWPFORMS_URL,
			'nonce'     => wp_create_nonce( 'upwpforms' ),
		] );
	}


	public function admin_scripts() {
		$screen = get_current_screen();

		if ( empty( $screen ) || 'wpforms_page_wpforms-settings' !== $screen->id ) {
			return;
		}

		wp_enqueue_style( 'upwpforms-admin', UPWPFORMS_URL . '/assets/css/admin.css', array(), UPWPFORMS_VERSION );
		wp_enqueue_script( 'upwpforms-admin', UPWPFORMS_URL . '/assets/js/admin.js', array(
			'jquery',
		), UPWPFORMS_VERSION, true );
	}

	public function handle_authorization() {
		$client = Client::instance();

		$client->create_access_token();

		$redirect = admin_url( 'admin.php?page=wpforms-settings&view=integrations&wpforms-integration=google-drive' );

		echo '<script type="text/javascript">window.opener.parent.location.href = "' . $redirect . '"; window.close();</script>';
		die();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Hooks::instance();