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
define('AUTH_KEY',         '&#vgxw:Wrp4$n;cfO7dn9?00sqQDw9?sR`O 8.p1Vh9vFvey&<g,{HR)0jLXjJe:');
define('SECURE_AUTH_KEY',  '!Uu{A/AQ&:8JxCyY&2}p)Ln3A`*{y>b0tXC~>[RLPL?7wHzuR3:l7J@sbZug(=pu');
define('LOGGED_IN_KEY',    '8bx>]5+O/,>5 XGL-jGA}B<gt,?Yi!y?&W&|4zB!!ZM9Jx$;p<g/ltYcTy7|e)Pi');
define('NONCE_KEY',        'Mh}A8tDB2(6pKBU%;xrN$6cv{Sj[q~0m?&,~H~R^h~JJTMy9*0gR !.<yv<]yjPS');
define('AUTH_SALT',        'l+AM]X|++7/aTQK(B5v91]bNX#v3f}+{WwZX**7:y[,-jb]yZU<e.OcOJ.c<@FG%');
define('SECURE_AUTH_SALT', 'x@gGTJ.G`hv0F5?O.m+gO]`8zDtv`m[^ynaicEV[h[Yv|kd=&KJ`~8qA$~|,)r4O');
define('LOGGED_IN_SALT',   'quU=Q.oC!r(~5+]4gi7A:!C/`wbo-M27?iEL%7!$.%TQ#1&KkZrVDjMew|ON-39(');
define('NONCE_SALT',       'VF{wjU|RNu5AzA;eM0l.rFNTzN):[$y[-Y=gxX./5VS`.4y4WOiAm,}cDO^5 H4L');

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
