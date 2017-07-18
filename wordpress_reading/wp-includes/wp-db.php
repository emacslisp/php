<?php

/**
 * WordPress DB Class
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */
define ( 'EZSQL_VERSION', 'WP1.25' );

define ( 'OBJECT', 'OBJECT' );
define ( 'object', 'OBJECT' ); // Back compat.

define ( 'OBJECT_K', 'OBJECT_K' );

define ( 'ARRAY_A', 'ARRAY_A' );

define ( 'ARRAY_N', 'ARRAY_N' );
class wpdb {
	var $show_errors = false;
	var $suppress_errors = false;
	public $last_error = '';
	public $num_queries = 0;
	public $num_rows = 0;
	var $rows_affected = 0;
	public $insert_id = 0;
	var $last_query;
	
	/**
	 * Results of the last query made
	 *
	 * @since 0.71
	 * @access private
	 * @var array|null
	 */
	var $last_result;
	
	/**
	 * MySQL result, which is either a resource or boolean.
	 *
	 * @since 0.71
	 * @access protected
	 * @var mixed
	 */
	protected $result;
	
	/**
	 * Cached column info, for sanity checking data before inserting
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var array
	 */
	protected $col_meta = array ();
	
	/**
	 * Calculated character sets on tables
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var array
	 */
	protected $table_charset = array ();
	
	/**
	 * Whether text fields in the current query need to be sanity checked.
	 *
	 * @since 4.2.0
	 * @access protected
	 * @var bool
	 */
	protected $check_current_query = true;
	
	/**
	 * Flag to ensure we don't run into recursion problems when checking the collation.
	 *
	 * @since 4.2.0
	 * @access private
	 * @see wpdb::check_safe_collation()
	 * @var bool
	 */
	private $checking_collation = false;
	
	/**
	 * Saved info on the table column
	 *
	 * @since 0.71
	 * @access protected
	 * @var array
	 */
	protected $col_info;
	
	/**
	 * Saved queries that were executed
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $queries;
	
	/**
	 * The number of times to retry reconnecting before dying.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @see wpdb::check_connection()
	 * @var int
	 */
	protected $reconnect_retries = 5;
	
	/**
	 * WordPress table prefix
	 *
	 * You can set this to have multiple WordPress installations
	 * in a single database. The second reason is for possible
	 * security precautions.
	 *
	 * @since 2.5.0
	 * @access public
	 * @var string
	 */
	public $prefix = '';
	
	/**
	 * WordPress base table prefix.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $base_prefix;
	
	/**
	 * Whether the database queries are ready to start executing.
	 *
	 * @since 2.3.2
	 * @access private
	 * @var bool
	 */
	var $ready = false;
	
	/**
	 * Blog ID.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int
	 */
	public $blogid = 0;
	
	/**
	 * Site ID.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int
	 */
	public $siteid = 0;
	
	/**
	 * List of WordPress per-blog tables
	 *
	 * @since 2.5.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $tables = array (
			'posts',
			'comments',
			'links',
			'options',
			'postmeta',
			'terms',
			'term_taxonomy',
			'term_relationships',
			'termmeta',
			'commentmeta' 
	);
	
	/**
	 * List of deprecated WordPress tables
	 *
	 * categories, post2cat, and link2cat were deprecated in 2.3.0, db version 5539
	 *
	 * @since 2.9.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $old_tables = array (
			'categories',
			'post2cat',
			'link2cat' 
	);
	
	/**
	 * List of WordPress global tables
	 *
	 * @since 3.0.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $global_tables = array (
			'users',
			'usermeta' 
	);
	
	/**
	 * List of Multisite global tables
	 *
	 * @since 3.0.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $ms_global_tables = array (
			'blogs',
			'signups',
			'site',
			'sitemeta',
			'sitecategories',
			'registration_log',
			'blog_versions' 
	);
	
	/**
	 * WordPress Comments table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $comments;
	
	/**
	 * WordPress Comment Metadata table
	 *
	 * @since 2.9.0
	 * @access public
	 * @var string
	 */
	public $commentmeta;
	
	/**
	 * WordPress Links table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $links;
	
	/**
	 * WordPress Options table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $options;
	
	/**
	 * WordPress Post Metadata table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $postmeta;
	
	/**
	 * WordPress Posts table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $posts;
	
	/**
	 * WordPress Terms table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	public $terms;
	
	/**
	 * WordPress Term Relationships table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	public $term_relationships;
	
	/**
	 * WordPress Term Taxonomy table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	public $term_taxonomy;
	
	/**
	 * WordPress Term Meta table.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $termmeta;
	
	//
	// Global and Multisite tables
	//
	
	/**
	 * WordPress User Metadata table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	public $usermeta;
	
	/**
	 * WordPress Users table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	public $users;
	
	/**
	 * Multisite Blogs table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $blogs;
	
	/**
	 * Multisite Blog Versions table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $blog_versions;
	
	/**
	 * Multisite Registration Log table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $registration_log;
	
	/**
	 * Multisite Signups table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $signups;
	
	/**
	 * Multisite Sites table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $site;
	
	/**
	 * Multisite Sitewide Terms table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $sitecategories;
	
	/**
	 * Multisite Site Metadata table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $sitemeta;
	
	/**
	 * Format specifiers for DB columns.
	 * Columns not listed here default to %s. Initialized during WP load.
	 *
	 * Keys are column names, values are format types: 'ID' => '%d'
	 *
	 * @since 2.8.0
	 * @see wpdb::prepare()
	 * @see wpdb::insert()
	 * @see wpdb::update()
	 * @see wpdb::delete()
	 * @see wp_set_wpdb_vars()
	 * @access public
	 * @var array
	 */
	public $field_types = array ();
	
	/**
	 * Database table columns charset
	 *
	 * @since 2.2.0
	 * @access public
	 * @var string
	 */
	public $charset;
	
	/**
	 * Database table columns collate
	 *
	 * @since 2.2.0
	 * @access public
	 * @var string
	 */
	public $collate;
	
	/**
	 * Database Username
	 *
	 * @since 2.9.0
	 * @access protected
	 * @var string
	 */
	protected $dbuser;
	
	/**
	 * Database Password
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbpassword;
	
	/**
	 * Database Name
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbname;
	
	/**
	 * Database Host
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbhost;
	
	/**
	 * Database Handle
	 *
	 * @since 0.71
	 * @access protected
	 * @var string
	 */
	protected $dbh;
	
	/**
	 * A textual description of the last query/get_row/get_var call
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $func_call;
	
	/**
	 * Whether MySQL is used as the database engine.
	 *
	 * Set in WPDB::db_connect() to true, by default. This is used when checking
	 * against the required MySQL version for WordPress. Normally, a replacement
	 * database drop-in (db.php) will skip these checks, but setting this to true
	 * will force the checks to occur.
	 *
	 * @since 3.3.0
	 * @access public
	 * @var bool
	 */
	public $is_mysql = null;
	
	/**
	 * A list of incompatible SQL modes.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $incompatible_modes = array (
			'NO_ZERO_DATE',
			'ONLY_FULL_GROUP_BY',
			'STRICT_TRANS_TABLES',
			'STRICT_ALL_TABLES',
			'TRADITIONAL' 
	);
	
	/**
	 * Whether to use mysqli over mysql.
	 *
	 * @since 3.9.0
	 * @access private
	 * @var bool
	 */
	private $use_mysqli = false;
	
	/**
	 * Whether we've managed to successfully connect at some point
	 *
	 * @since 3.9.0
	 * @access private
	 * @var bool
	 */
	private $has_connected = false;
	public function __construct($dbuser, $dbpassword, $dbname, $dbhost) {
		register_shutdown_function ( array (
				$this,
				'__destruct' 
		) );
		
		if (WP_DEBUG && WP_DEBUG_DISPLAY)
			$this->show_errors ();
		
		/*
		 * Use ext/mysqli if it exists and:
		 * - WP_USE_EXT_MYSQL is defined as false, or
		 * - We are a development version of WordPress, or
		 * - We are running PHP 5.5 or greater, or
		 * - ext/mysql is not loaded.
		 */
		if (function_exists ( 'mysqli_connect' )) {
			if (defined ( 'WP_USE_EXT_MYSQL' )) {
				$this->use_mysqli = ! WP_USE_EXT_MYSQL;
			} elseif (version_compare ( phpversion (), '5.5', '>=' ) || ! function_exists ( 'mysql_connect' )) {
				$this->use_mysqli = true;
			} elseif (false !== strpos ( $GLOBALS ['wp_version'], '-' )) {
				$this->use_mysqli = true;
			}
		}
		
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		
		// wp-config.php creation will manually connect when ready.
		if (defined ( 'WP_SETUP_CONFIG' )) {
			return;
		}
		
		$this->db_connect ();
	}
	public function db_connect($allow_bail = true) {
		file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
		$this->is_mysql = true;
		
		/*
		 * Deprecated in 3.9+ when using MySQLi. No equivalent
		 * $new_link parameter exists for mysqli_* functions.
		 */
		$new_link = defined ( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined ( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;
		
		if ($this->use_mysqli) {
			$this->dbh = mysqli_init ();
			
			// mysqli_real_connect doesn't support the host param including a port or socket
			// like mysql_connect does. This duplicates how mysql_connect detects a port and/or socket file.
			$port = null;
			$socket = null;
			$host = $this->dbhost;
			$port_or_socket = strstr ( $host, ':' );
			if (! empty ( $port_or_socket )) {
				$host = substr ( $host, 0, strpos ( $host, ':' ) );
				$port_or_socket = substr ( $port_or_socket, 1 );
				if (0 !== strpos ( $port_or_socket, '/' )) {
					$port = intval ( $port_or_socket );
					$maybe_socket = strstr ( $port_or_socket, ':' );
					if (! empty ( $maybe_socket )) {
						$socket = substr ( $maybe_socket, 1 );
					}
				} else {
					$socket = $port_or_socket;
				}
			}
			
			if (WP_DEBUG) {
				mysqli_real_connect ( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
			} else {
				@mysqli_real_connect ( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
			}
			
			if ($this->dbh->connect_errno) {
				$this->dbh = null;
				
				/*
				 * It's possible ext/mysqli is misconfigured. Fall back to ext/mysql if:
				 * - We haven't previously connected, and
				 * - WP_USE_EXT_MYSQL isn't set to false, and
				 * - ext/mysql is loaded.
				 */
				$attempt_fallback = true;
				
				if ($this->has_connected) {
					$attempt_fallback = false;
				} elseif (defined ( 'WP_USE_EXT_MYSQL' ) && ! WP_USE_EXT_MYSQL) {
					$attempt_fallback = false;
				} elseif (! function_exists ( 'mysql_connect' )) {
					file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
					$attempt_fallback = false;
				}
				
				if ($attempt_fallback) {
					$this->use_mysqli = false;
					return $this->db_connect ( $allow_bail );
				}
			}
		} else {
			if (WP_DEBUG) {
				$this->dbh = mysql_connect ( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
			} else {
				$this->dbh = @mysql_connect ( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
			}
		}
		
		if (! $this->dbh && $allow_bail) {
			wp_load_translations_early ();
			
			// Load custom DB error template, if present.
			if (file_exists ( WP_CONTENT_DIR . '/db-error.php' )) {
				require_once (WP_CONTENT_DIR . '/db-error.php');
				die ();
			}
			
			$message = '<h1>' . __ ( 'Error establishing a database connection' ) . "</h1>\n";
			
			$message .= '<p>' . sprintf(
				/* translators: 1: wp-config.php. 2: database host */
				__ ( 'This either means that the username and password information in your %1$s file is incorrect or we can&#8217;t contact the database server at %2$s. This could mean your host&#8217;s database server is down.' ), '<code>wp-config.php</code>', '<code>' . htmlspecialchars ( $this->dbhost, ENT_QUOTES ) . '</code>' ) . "</p>\n";
			
			$message .= "<ul>\n";
			$message .= '<li>' . __ ( 'Are you sure you have the correct username and password?' ) . "</li>\n";
			$message .= '<li>' . __ ( 'Are you sure that you have typed the correct hostname?' ) . "</li>\n";
			$message .= '<li>' . __ ( 'Are you sure that the database server is running?' ) . "</li>\n";
			$message .= "</ul>\n";
			
			$message .= '<p>' . sprintf(
						/* translators: %s: support forums URL */
						__ ( 'If you&#8217;re unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href="%s">WordPress Support Forums</a>.' ), __ ( 'https://wordpress.org/support/' ) ) . "</p>\n";
			
			$this->bail ( $message, 'db_connect_fail' );
			
			return false;
		} elseif ($this->dbh) {
			if (! $this->has_connected) {
				$this->init_charset ();
			}
			
			$this->has_connected = true;
			
			$this->set_charset ( $this->dbh );
			
			$this->ready = true;
			$this->set_sql_mode ();
			$this->select ( $this->dbname, $this->dbh );
			
			return true;
		}
		
		return false;
	}
}

?>