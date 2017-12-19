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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3307');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Pi(WYv#&&*F%gU#?jbNZ:^(D/1jI>9%ZT&9{VtY0~Fs/j}}|7`*7R7~0[)$&X]%u');
define('SECURE_AUTH_KEY',  'wu.n*Xpn@0kH3zW T]9lY,Fa[Ibm}N0S0+PcAzI6-Vg/[5k%~!lUE,~LR=tB;1G;');
define('LOGGED_IN_KEY',    '+@ma:.Q0 7bJiNx0QsQxF-{{BykllvQ>-@6vHSGaa?bSoF1GbV@e:>N 3EAH7hPw');
define('NONCE_KEY',        '*)3b_u-Pu/$mn1aSM?]y]o|Wcog?cS:NxTocBnRIGK~R2MjQi|0PeK_F5guN*7W<');
define('AUTH_SALT',        'k6n_#O-2R`8n!@z$_]Uw)%9HlEv!GWw*u%R/wVOuw<eIT;+_>inUGJXU{];S s39');
define('SECURE_AUTH_SALT', '_e^]^EvJ%R2n2mGAW:Rx.|Wt:1]k?:V9.;!%wnlsegCC+/qp6WfRhdz3z;/&Ot55');
define('LOGGED_IN_SALT',   'dhDjpZe-mCBV|k_[^-qSszUv%ty)8,~w2*Rm!~b*>U%$N=~d5JPN KL|,u{/?B[~');
define('NONCE_SALT',       '7>(m52{FP?|[E6%UhHN|VRu06}s#D$KJKd0;C|vn==^9]M4y<tXxPw_srO{A8U}Y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
