<?php

namespace UPWPForms;

defined( 'ABSPATH' ) || exit();

class Settings {

	private static $instance;

	public function __construct() {
		//add Google Drive settings to WPForms settings integrations tab
		add_action( 'wpforms_settings_providers', array( $this, 'add_settings' ) );
	}


	/**
	 * add Google Drive settings in the wpforms settings page integrations tab
	 */

	public function add_settings() {
		$auth_url = Client::instance()->get_auth_url();

		$accounts = Account::get_accounts();
		$config   = [
			'slug' => 'google-drive',
			'name' => __( 'Google Drive', 'upload-fields-for-wpforms' ),
			'icon' => UPWPFORMS_ASSETS . '/images/drive.png',
		];

		$class = 'connected';
		$arrow = 'right';

		// This lets us highlight a specific service by a special link.
		if ( ! empty( $_GET['wpforms-integration'] ) ) {
			if ( $config['slug'] === $_GET['wpforms-integration'] ) {
				$class .= ' focus-in';
				$arrow = 'down';
			} else {
				$class .= ' focus-out';
			}
		}

		?>

        <div id="wpforms-integration-<?php echo esc_attr( $config['slug'] ); ?>"
             class="wpforms-settings-provider wpforms-clear <?php echo esc_attr( $config['slug'] ); ?> <?php echo esc_attr( $class ); ?>">

            <div class="wpforms-settings-provider-header wpforms-clear"
                 data-provider="<?php echo esc_attr( $config['slug'] ); ?>">

                <div class="wpforms-settings-provider-logo">
                    <i title="<?php esc_attr_e( 'Show Accounts', 'upload-fields-for-wpforms' ); ?>"
                       class="fa fa-chevron-<?php echo esc_attr( $arrow ); ?>"></i>
                    <img src="<?php echo esc_url( $config['icon'] ); ?>">
                </div>

                <div class="wpforms-settings-provider-info">
                    <h3><?php echo esc_html( $config['name'] ); ?></h3>
                    <p>
						<?php
						/* translators: %s - provider name. */
						printf( esc_html__( 'Integrate %s with WPForms', 'upload-fields-for-wpforms' ), esc_html( $config['name'] ) );
						?>
                    </p>
                </div>

            </div>

            <div class="wpforms-settings-provider-accounts" id="provider-<?php echo esc_attr( $config['slug'] ); ?>">

                <div class="wpforms-settings-provider-accounts-list">
                    <ul>
						<?php
						if ( ! empty( $accounts ) ) {
							foreach (
								$accounts

								as $key => $account
							) { ?>
                                <li class="wpforms-clear">

                                    <img src="<?php echo esc_attr( $account['photo'] ); ?>" class="photo"
                                         onerror="jQuery.loadAvatar(this, '<?php echo esc_attr( $account['email'] ); ?>')">

                                    <div class="account-info">
                                        <span class="label"><?php echo esc_html( $account['name'] ); ?></span>
                                        <span class="email"><?php echo esc_html( $account['email'] ); ?></span>
                                    </div>

									<?php if ( ! empty( $account['date'] ) ) { ?>
                                        <span class="date"><?php printf( esc_html__( 'Connected on: %s', 'upload-fields-for-wpforms' ), date_i18n( get_option( 'date_format' ), intval( $account['date'] ) ) ); ?></span>
									<?php } ?>

                                    <span class="remove">
                                        <a href="#" data-provider="<?php echo esc_attr( $config['slug'] ) ?>"
                                           data-key="<?php echo esc_attr( $key ) ?>"><?php esc_html_e( 'Disconnect', 'upload-fields-for-wpforms' ) ?></a>
                                    </span>
                                </li>
							<?php }
						}
						?>
                    </ul>
                </div>

                <p class=" wpforms-settings-provider-accounts-toggle">
                    <a class="upwpforms-auth-btn wpforms-btn wpforms-btn-md wpforms-btn-light-grey"
                       href="<?php echo esc_url( $auth_url ); ?>"
                       data-provider="<?php echo esc_attr( $config['slug'] ); ?>">
                        <img src="<?php echo UPWPFORMS_ASSETS . '/images/google-icon.png' ?>"/>
						<?php esc_html_e( 'Sign in with Google', 'upload-fields-for-wpforms' ); ?>
                    </a>
                </p>

            </div>

        </div>


	<?php }

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Settings ) ) {
			self::$instance = new Settings();
		}

		return self::$instance;
	}

}

Settings::instance();



