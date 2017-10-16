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
define('DB_NAME', 'wordpress1234');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3308');

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
define('AUTH_KEY',         '?RK)5POKx`biwf;Q(~e#m?>S+tcg8|W7R=MjI<T6*V%|09QFZVe#U=qMlsT)sbM9');
define('SECURE_AUTH_KEY',  '.{(C$x.Q(9QFF,!`P%2;6(I(/<Yp>:]  3kV<f?5%O/fI8:[IZ)bDn)BaDQ>AB@j');
define('LOGGED_IN_KEY',    '` 4*<2J2dH8Mb5wJqbLUpr_qVyf 0&+RYWuMl4bo/91|B[JR{-/b/)rTewB4|shX');
define('NONCE_KEY',        '%C:{,/a`72~.LzLxfJydoQXs$] #d`w(ss4lp5c t=&5ro>wXnA@3[`C8#a@2xP_');
define('AUTH_SALT',        'Pg=5@.O`<rWeFLB>Y10oLQQfpR&(%sZqyS :Mw}ANJE(-57FttT49;!]NM3o5oF`');
define('SECURE_AUTH_SALT', '6n}fJ$F[i91>BvtVO$lk8 ;ivYw}#1&F%3KkC&,LMVA$&UvrtGB>pw:K-twT_|]V');
define('LOGGED_IN_SALT',   '2v6+/0CJh2j]iEbB@Cq}ZmwGFT{mT!8i+B|~<Cz;+I)a$-CLiSx74Ua:a{jTW(1O');
define('NONCE_SALT',       'g1D,`3>_?8_@OjiX1Grs}rp<dzGZd.|kWDdM==u`1b2bl!Y$c&=~,h&:GjWKdVUZ');

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
