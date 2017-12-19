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
define('DB_NAME', 'wordpress');

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
define('AUTH_KEY',         '|N^U;dl4Mu3I 1mL@O#v{MIY`X/v%FvSv}&3R@Q{{ s-.R(IzT|H4*]|sB X4KrE');
define('SECURE_AUTH_KEY',  'D2*;#3=DwkqO8T^OUd!.m^g}az58y&-%~VmR[];8!!L,_btb(eDoYb}s*9D]t |I');
define('LOGGED_IN_KEY',    'O/.;=$_Me=,!6G|D0XdrCWcTc?.*GQNnNar~SExFUKT@g=d&WLV-iAV5yGvx[`bb');
define('NONCE_KEY',        'zi!x&2pp5-$s[S`!Lr]8FFjb##.KAy2X=PfxA?MTJM.*[-d`B:n}X<8-)4fl +|H');
define('AUTH_SALT',        '8~?^gOI=.0s`(8+}2EMa$HA%XaQOE>P6USPo@]~;tH1{ZI!>]ge*Fl>(7OV#Q3[Y');
define('SECURE_AUTH_SALT', 'ai eXE=;Vh!WYi%z<:^#QkbRg1i2ky?1z&YLA)8q$;/FkC=16 H *wI&vZciF7U8');
define('LOGGED_IN_SALT',   'o9_ORiqnlDn+J.LlJU=-DYG0q&F,|SCfc@~ >0MybLp[7T:K*uD-G>fVa]8HGG&e');
define('NONCE_SALT',       'J~<Yq8d{73NJ;s8:!H%k~:k2puN;YR%fcmj/?%NCWDsk-;CO<{6Wf>&i!GZ+w$5n');

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

