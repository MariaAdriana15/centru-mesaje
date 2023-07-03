<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'centru_mesaje' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '_>Co1+_;@tZy ;P#zp9F@_JCg{)c.6+iv{f%]Wr05*Iyk+j/s0}W6&t`&-Ku#ERl' );
define( 'SECURE_AUTH_KEY',  'm*SCCK8sj).o:VD~ED[PdGXpUbP@@4bO>/<Fg(}[j3iEFL[H8>&|A0IhJxm  $W,' );
define( 'LOGGED_IN_KEY',    '||ws080&4__PxA.IIqtG2MH%=Dv{A|f_DS>&0~O>;6&z:uoz%F !:?^eau5[nY.b' );
define( 'NONCE_KEY',        '%l93o4SYM59p;`2p$c!+xg:LmBM|+0nsMc lJv6^d?1z1Y{.0ZCNXU_fM6HZG@Wb' );
define( 'AUTH_SALT',        '^p6*8-*M3P%/Y(YlkHeIg=ZPz>nFfMjP-6R[K2_bH95$uDiX1 Tb2ZC-aRJsrR#:' );
define( 'SECURE_AUTH_SALT', 'Z6JaoG1{{jmygTk: ,AnR2#Y#K6WNKx<,^-aonN(0zulLb}fA2L]y(5F?L~p2}v5' );
define( 'LOGGED_IN_SALT',   'L$~cYT9ArS:MOUMmsH>9=P0Pvz<d/yrJP}%Z]f:y,nZ?`tufb1&|7qhL$AN29]$7' );
define( 'NONCE_SALT',       'B{:?=bN]5Q^:GDA11Ig;yN,Tmi?pYtLr{jv.(G&X%.ic$msA)EIWKP%sweKS`I4N' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
