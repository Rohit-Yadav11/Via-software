<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'atestwj9_via-software' );

/** MySQL database username */
define( 'DB_USER', 'atestwj9_via-sof' );

/** MySQL database password */
define( 'DB_PASSWORD', 'via-software' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'CC5-!/;|/Dayf4oF~fC68<jZYR0^AC4+n8cfa_lNFoU)=yJQfOZH!}51G1U`<0jj' );
define( 'SECURE_AUTH_KEY',  '?Gn?e4#mSwfa5vVszwf-ipUV0w{@EPDN~uP@W5E*?e+E5VI_T.iYAzGMJL!K[e,d' );
define( 'LOGGED_IN_KEY',    '% V5KRY)Y?2hWy[ AE8ntY[.qAHDsmx0VU:cZ%Gg~h;jF,dQ1=i%qO~tF0VoI9_j' );
define( 'NONCE_KEY',        'C(QpVabuJlpts$?P9~0&Tt=7KyC>DzN]id?da67Hq+EEu@}{]S$j8i.4gOYt^f?D' );
define( 'AUTH_SALT',        'dn~?d^_]x:-DTv7u7bCl&wm%-XNR-j40GSk,NhybBBcq!1Z%e<Ib,IX*6M#`a$_|' );
define( 'SECURE_AUTH_SALT', '-JAd/x~+^w,])O1:a|T&%-sI}kBdq`F`p7vQ/t|_BJ{$~7IZD~2SU0Q^pVe%MN]J' );
define( 'LOGGED_IN_SALT',   '&^}!m^4Yq`COLEHzCE;6,1E<w9oZaRM0^:7b}/QNNt~?{x= 0.r&P=#.nAJg6|7_' );
define( 'NONCE_SALT',       'Bs9,3ynm]Qhm9kFo$`eQv9-A,C;E!2LDYn^A0>-RNhshJ8X0ywa2t,@&8U$aNwMK' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
