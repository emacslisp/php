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
define('AUTH_KEY',         '!B9kS;hoTpeL,j`;,hu&=~ol2kI)<N4TuA}Dbk!ciaw<=%L7VO#Rg71%!OXuB!4w');
define('SECURE_AUTH_KEY',  'o]R;Moyge^b(J_c)wOJ:kZFAgV@lN{n04BUx:7Tk%MHB0#.,a!LJ{=upzBieWz-t');
define('LOGGED_IN_KEY',    '_S<FV=+K6~m%?AdQvT0%)&vMXz/)[uoP`x2md|V0e>cGXKP_x]A$%(1|Y ,1 C%O');
define('NONCE_KEY',        'g5,q=qP)/theOA-iBDVXPZHI2}CK=9v-&lNaV:@zpv-m9OdB1+;@.t&,QnAy%G5&');
define('AUTH_SALT',        '@tJ&?qcz-~^Ji=y(7kN7~[j, ILA}/{1;u)Gc&I<+K`^Qsgarz0kUjN0DxdV:uhc');
define('SECURE_AUTH_SALT', '/)A.AtOmAmkIku7Yy8r>5@lTl_v-}$FZ-+|jyU0 $KgJF>yG{$$QEn:-+.-[S7<;');
define('LOGGED_IN_SALT',   '.AKKHn`AXJYddyL_$*8}{)#  o$xx(J~k&qTDrU(hVU2.*mB78h1&Z?:%gs&d~Y/');
define('NONCE_SALT',       '<LH>FGnoL6T_Q6d.ngQObF6(N]o,}Du52qP2R>T).Pa|1}:O26f}d<dBr*vp7!7R');

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
