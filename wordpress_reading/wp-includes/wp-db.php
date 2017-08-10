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
		
		/*if (WP_DEBUG && WP_DEBUG_DISPLAY)
			$this->show_errors ();*/
		
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
	
	public function select( $db, $dbh = null ) {
	if ( is_null($dbh) )
		$dbh = $this->dbh;
		
		if ( $this->use_mysqli ) {
			$success = mysqli_select_db( $dbh, $db );
		} else {
			$success = mysql_select_db( $db, $dbh );
		}
		if ( ! $success ) {
			$this->ready = false;
			//if ( ! did_action( 'template_redirect' ) ) 
			{
				//wp_load_translations_early();
				
				$message = '<h1>' . __( 'Can&#8217;t select database' ) . "</h1>\n";
				
				$message .= '<p>' . sprintf(
						/* translators: %s: database name */
						__( 'We were able to connect to the database server (which means your username and password is okay) but not able to select the %s database.' ),
						'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
						) . "</p>\n";
						
						$message .= "<ul>\n";
						$message .= '<li>' . __( 'Are you sure it exists?' ) . "</li>\n";
						
						$message .= '<li>' . sprintf(
								/* translators: 1: database user, 2: database name */
								__( 'Does the user %1$s have permission to use the %2$s database?' ),
								'<code>' . htmlspecialchars( $this->dbuser, ENT_QUOTES )  . '</code>',
								'<code>' . htmlspecialchars( $db, ENT_QUOTES ) . '</code>'
								) . "</li>\n";
								
								$message .= '<li>' . sprintf(
										/* translators: %s: database name */
										__( 'On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?' ),
										htmlspecialchars( $db, ENT_QUOTES )
										). "</li>\n";
										
										$message .= "</ul>\n";
										
										$message .= '<p>' . sprintf(
												/* translators: %s: support forums URL */
												__( 'If you don&#8217;t know how to set up a database you should <strong>contact your host</strong>. If all else fails you may find help at the <a href="%s">WordPress Support Forums</a>.' ),
												__( 'https://wordpress.org/support/' )
												) . "</p>\n";
												
												$this->bail( $message, 'db_select_fail' );
			}
		}
	}
	
	public function bail( $message, $error_code = '500' ) {
	if ( !$this->show_errors ) {
		if ( class_exists( 'WP_Error', false ) ) {
			$this->error =  $message;//new WP_Error($error_code, $message);
		} else {
			$this->error = $message;
		}
		return false;
	}
	wp_die($message);
	}
	
	public function set_sql_mode( $modes = array() ) {
		if ( empty( $modes ) ) {
			if ( $this->use_mysqli ) {
				$res = mysqli_query( $this->dbh, 'SELECT @@SESSION.sql_mode' );
			} else {
				$res = mysql_query( 'SELECT @@SESSION.sql_mode', $this->dbh );
			}
			
			if ( empty( $res ) ) {
				return;
			}
			
			if ( $this->use_mysqli ) {
				$modes_array = mysqli_fetch_array( $res );
				if ( empty( $modes_array[0] ) ) {
					return;
				}
				$modes_str = $modes_array[0];
			} else {
				$modes_str = mysql_result( $res, 0 );
			}
			
			if ( empty( $modes_str ) ) {
				return;
			}
			
			$modes = explode( ',', $modes_str );
		}
		
		$modes = array_change_key_case( $modes, CASE_UPPER );
		
		/**
		 * Filters the list of incompatible SQL modes to exclude.
		 *
		 * @since 3.9.0
		 *
		 * @param array $incompatible_modes An array of incompatible modes.
		 */
		//$incompatible_modes = (array) apply_filters( 'incompatible_sql_modes', $this->incompatible_modes );
		
		$incompatible_modes = $this->incompatible_modes;
		
		foreach ( $modes as $i => $mode ) {
			if ( in_array( $mode, $incompatible_modes ) ) {
				unset( $modes[ $i ] );
			}
		}
		
		$modes_str = implode( ',', $modes );
		
		if ( $this->use_mysqli ) {
			mysqli_query( $this->dbh, "SET SESSION sql_mode='$modes_str'" );
		} else {
			mysql_query( "SET SESSION sql_mode='$modes_str'", $this->dbh );
		}
	}
	
	public function set_charset( $dbh, $charset = null, $collate = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	if ( ! isset( $charset ) )
		$charset = $this->charset;
		if ( ! isset( $collate ) )
			$collate = $this->collate;
			if ( $this->has_cap( 'collation' ) && ! empty( $charset ) ) {
				$set_charset_succeeded = true;
				
				if ( $this->use_mysqli ) {
					if ( function_exists( 'mysqli_set_charset' ) && $this->has_cap( 'set_charset' ) ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
					$set_charset_succeeded = mysqli_set_charset( $dbh, $charset );
					}
					
					if ( $set_charset_succeeded ) {
						$query = $this->prepare( 'SET NAMES %s', $charset );
						if ( ! empty( $collate ) )
							$query .= $this->prepare( ' COLLATE %s', $collate );
							mysqli_query( $dbh, $query );
					}
				} else {
					if ( function_exists( 'mysql_set_charset' ) && $this->has_cap( 'set_charset' ) ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
					$set_charset_succeeded = mysql_set_charset( $charset, $dbh );
					}
					if ( $set_charset_succeeded ) {
						$query = $this->prepare( 'SET NAMES %s', $charset );
						if ( ! empty( $collate ) )
							$query .= $this->prepare( ' COLLATE %s', $collate );
							mysql_query( $query, $dbh );
					}
				}
			}
	}
	
	public function init_charset() {
		$charset = '';
		$collate = '';
		
		if ( function_exists('is_multisite') && is_multisite() ) {
			$charset = 'utf8';
			if ( defined( 'DB_COLLATE' ) && DB_COLLATE ) {
				$collate = DB_COLLATE;
			} else {
				$collate = 'utf8_general_ci';
			}
		} elseif ( defined( 'DB_COLLATE' ) ) {
			$collate = DB_COLLATE;
		}
		
		if ( defined( 'DB_CHARSET' ) ) {
			$charset = DB_CHARSET;
		}
		
		$charset_collate = $this->determine_charset( $charset, $collate );
		
		$this->charset = $charset_collate['charset'];
		$this->collate = $charset_collate['collate'];
	}
	
	public function determine_charset( $charset, $collate ) {
		if ( ( $this->use_mysqli && ! ( $this->dbh instanceof mysqli ) ) || empty( $this->dbh ) ) {
			return compact( 'charset', 'collate' );
		}
		
		if ( 'utf8' === $charset && $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8mb4';
		}
		
		if ( 'utf8mb4' === $charset && ! $this->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8';
			$collate = str_replace( 'utf8mb4_', 'utf8_', $collate );
		}
		
		if ( 'utf8mb4' === $charset ) {
			// _general_ is outdated, so we can upgrade it to _unicode_, instead.
			if ( ! $collate || 'utf8_general_ci' === $collate ) {
				$collate = 'utf8mb4_unicode_ci';
			} else {
				$collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
			}
		}
		
		// _unicode_520_ is a better collation, we should use that when it's available.
		if ( $this->has_cap( 'utf8mb4_520' ) && 'utf8mb4_unicode_ci' === $collate ) {
			$collate = 'utf8mb4_unicode_520_ci';
		}
		
		return compact( 'charset', 'collate' );
	}
	
	public function db_version() {
		if ( $this->use_mysqli ) {
			$server_info = mysqli_get_server_info( $this->dbh );
		} else {
			$server_info = mysql_get_server_info( $this->dbh );
		}
		return preg_replace( '/[^0-9.].*/', '', $server_info );
	}
	
	public function has_cap( $db_cap ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	$version = $this->db_version();
	
	switch ( strtolower( $db_cap ) ) {
		case 'collation' :    // @since 2.5.0
		case 'group_concat' : // @since 2.7.0
		case 'subqueries' :   // @since 2.7.0
			return version_compare( $version, '4.1', '>=' );
		case 'set_charset' :
			return version_compare( $version, '5.0.7', '>=' );
		case 'utf8mb4' :      // @since 4.1.0
			if ( version_compare( $version, '5.5.3', '<' ) ) {
				return false;
			}
			if ( $this->use_mysqli ) {
				$client_version = mysqli_get_client_info();
			} else {
				$client_version = mysql_get_client_info();
			}
			
			/*
			 * libmysql has supported utf8mb4 since 5.5.3, same as the MySQL server.
			 * mysqlnd has supported utf8mb4 since 5.0.9.
			 */
			if ( false !== strpos( $client_version, 'mysqlnd' ) ) {
				$client_version = preg_replace( '/^\D+([\d.]+).*/', '$1', $client_version );
				return version_compare( $client_version, '5.0.9', '>=' );
			} else {
				return version_compare( $client_version, '5.5.3', '>=' );
			}
		case 'utf8mb4_520' : // @since 4.6.0
			return version_compare( $version, '5.6', '>=' );
	}
	
	return false;
	}
	
	public function query( $query ) {
	if ( ! $this->ready ) {
		$this->check_current_query = true;
		return false;
	}
	
	/**
	 * Filters the database query.
	 *
	 * Some queries are made before the plugins have been loaded,
	 * and thus cannot be filtered with this method.
	 *
	 * @since 2.1.0
	 *
	 * @param string $query Database query.
	 */
	$query = apply_filters( 'query', $query );
	
	$this->flush();
	
	// Log how the function was called
	$this->func_call = "\$db->query(\"$query\")";
	
	// If we're writing to the database, make sure the query will write safely.
	if ( $this->check_current_query && ! $this->check_ascii( $query ) ) {
		$stripped_query = $this->strip_invalid_text_from_query( $query );
		// strip_invalid_text_from_query() can perform queries, so we need
		// to flush again, just to make sure everything is clear.
		$this->flush();
		if ( $stripped_query !== $query ) {
			$this->insert_id = 0;
			return false;
		}
	}
	
	$this->check_current_query = true;
	
	// Keep track of the last query for debug.
	$this->last_query = $query;
	
	$this->_do_query( $query );
	
	// MySQL server has gone away, try to reconnect.
	$mysql_errno = 0;
	if ( ! empty( $this->dbh ) ) {
		if ( $this->use_mysqli ) {
			if ( $this->dbh instanceof mysqli ) {
				$mysql_errno = mysqli_errno( $this->dbh );
			} else {
				// $dbh is defined, but isn't a real connection.
				// Something has gone horribly wrong, let's try a reconnect.
				$mysql_errno = 2006;
			}
		} else {
			if ( is_resource( $this->dbh ) ) {
				$mysql_errno = mysql_errno( $this->dbh );
			} else {
				$mysql_errno = 2006;
			}
		}
	}
	
	if ( empty( $this->dbh ) || 2006 == $mysql_errno ) {
		if ( $this->check_connection() ) {
			$this->_do_query( $query );
		} else {
			$this->insert_id = 0;
			return false;
		}
	}
	
	// If there is an error then take note of it.
	if ( $this->use_mysqli ) {
		if ( $this->dbh instanceof mysqli ) {
			$this->last_error = mysqli_error( $this->dbh );
		} else {
			$this->last_error = __( 'Unable to retrieve the error message from MySQL' );
		}
	} else {
		if ( is_resource( $this->dbh ) ) {
			$this->last_error = mysql_error( $this->dbh );
		} else {
			$this->last_error = __( 'Unable to retrieve the error message from MySQL' );
		}
	}
	
	if ( $this->last_error ) {
		// Clear insert_id on a subsequent failed insert.
		if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) )
			$this->insert_id = 0;
			
			$this->print_error();
			return false;
	}
	
	if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
		$return_val = $this->result;
	} elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
		if ( $this->use_mysqli ) {
			$this->rows_affected = mysqli_affected_rows( $this->dbh );
		} else {
			$this->rows_affected = mysql_affected_rows( $this->dbh );
		}
		// Take note of the insert_id
		if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
			if ( $this->use_mysqli ) {
				$this->insert_id = mysqli_insert_id( $this->dbh );
			} else {
				$this->insert_id = mysql_insert_id( $this->dbh );
			}
		}
		// Return number of rows affected
		$return_val = $this->rows_affected;
	} else {
		$num_rows = 0;
		if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
			while ( $row = mysqli_fetch_object( $this->result ) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}
		} elseif ( is_resource( $this->result ) ) {
			while ( $row = mysql_fetch_object( $this->result ) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}
		}
		
		// Log number of rows the query returned
		// and return number of rows selected
		$this->num_rows = $num_rows;
		$return_val     = $num_rows;
	}
	
	return $return_val;
	}
	
	private function _do_query( $query ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
		$this->timer_start();
	}
	
	if ( ! empty( $this->dbh ) && $this->use_mysqli ) {
		$this->result = mysqli_query( $this->dbh, $query );
	} elseif ( ! empty( $this->dbh ) ) {
		$this->result = mysql_query( $query, $this->dbh );
	}
	$this->num_queries++;
	
	if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
		$this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );
	}
	}
	
	public function flush() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	$this->last_result = array();
	$this->col_info    = null;
	$this->last_query  = null;
	$this->rows_affected = $this->num_rows = 0;
	$this->last_error  = '';
	
	if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
		mysqli_free_result( $this->result );
		$this->result = null;
		
		// Sanity check before using the handle
		if ( empty( $this->dbh ) || !( $this->dbh instanceof mysqli ) ) {
			return;
		}
		
		// Clear out any results from a multi-query
		while ( mysqli_more_results( $this->dbh ) ) {
			mysqli_next_result( $this->dbh );
		}
	} elseif ( is_resource( $this->result ) ) {
		mysql_free_result( $this->result );
	}
	}
}

?>