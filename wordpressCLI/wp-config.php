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
define('DB_HOST', '127.0.0.1:3306');

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
define('AUTH_KEY',         '28>n.*w@.zDU4yWG+Rv)Rwx7-Mgfv|<3$!{4bel<L=:U5ySo :}a}>]8kV+lcP}P');
define('SECURE_AUTH_KEY',  'qQ^-Re>ae7:S)|Hr?EE;.XJAulO@!85+<}y?T1.` UjUaW.e{3~RJYg2 0*qmt1K');
define('LOGGED_IN_KEY',    'iys-~a}P.$i7XM )zF%8$$m@H0WW~`@Y3]pX8a_I#]w(v.qq[|l|9r1b-Yh$)Zxv');
define('NONCE_KEY',        's5tUiRWUS6HC9@UD!X{?HqH$echRM7KA929f3&->x}X&[u C![sOnNnP^:;Lp~mR');
define('AUTH_SALT',        'hQ@N($l)dmX,Q#Ye=|eN$5#RqwrbRMF&8:gVp;YML?T5Ih:1rdS05mc9}IF,xi|j');
define('SECURE_AUTH_SALT', 'S<HsohiNB6~dAH5^3=GwpPAR<]9pXx~D5kONaUzw&/;XEvWx,Aw:i4:n<!S-/A}q');
define('LOGGED_IN_SALT',   'E^)VZ6<-bAn^ZYU)!uCf9q0lx84/g[v!#@{wU_wI_nO/I=mdlG)c:4m>FlN{DiLN');
define('NONCE_SALT',       'eMfiF=wbP#S8@33</<t@s F)7.kgb4>=W~rAIKzU7pu!]6H_F:W}:@Zr,dKc$%,7');

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
	