<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'andloft');

/** MySQL database username */
define('DB_USER', 'andloft');

/** MySQL database password */
define('DB_PASSWORD', 'andloft');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'ch9+>&_^|CG<C7A>[~BB=~W#FJa/zn9P}a7znGKvt__m+8w=ayz=mWXRsU$}W@;J');
define('SECURE_AUTH_KEY',  '@f!|YsT:06r9W0|r4}d.hS?.A7}yPP.sN)ME)H&&OK#4:JYU7irYTZj=<~;!L!yU');
define('LOGGED_IN_KEY',    'SlC!o5Mdj:*HdPkUI~O8n 7B`kv5+@EXE@Z/$;10(.~MujW?~a{LLTdSu# P)h:0');
define('NONCE_KEY',        '(lut9/v=)u<_*/]N;J_pSS}0H$?>W}sq1,rURAt=P@u^s<_ZvVg>E%l7o,9hGpYQ');
define('AUTH_SALT',        '$%2P-f?Z9.l$B8dC}E+D]=mR?s4fBh89W|9nkM5qUboOAT&#Tgh5=@_GCCVcnC/y');
define('SECURE_AUTH_SALT', '/faI7%2LKQCkx-z39^xa ?]~51ts#Y|2&ifO?L>fHrqX~L7{h0YD`TUh~w9h| W-');
define('LOGGED_IN_SALT',   '@FlbmiG4wR1>8J|AEY,]**9R](u`l$r8U;Fr$W#h<ET~jW:I2-t|*jPW}qh/W_EB');
define('NONCE_SALT',       'Qx2FODZ;6t=)Y=wM+6jZAlo64<wU[:MZL( IFkQ/.GeF?9V:!o[;W|H<+%oJX#+b');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
