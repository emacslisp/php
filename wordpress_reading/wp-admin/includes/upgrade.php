<?php

require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once(ABSPATH . 'wp-admin/includes/schema.php');

require_once( ABSPATH . WPINC . '/http.php' );

function wp_check_mysql_version() {
	global $wpdb;
	$result = $wpdb->check_database_version ();
	if (is_wp_error ( $result ))
		die ( $result->get_error_message () );
}

function wp_cache_flush() {
	global $wp_object_cache;
	
	return $wp_object_cache->flush();
}


/**
 * Maybe enable pretty permalinks on install.
 *
 * If after enabling pretty permalinks don't work, fallback to query-string permalinks.
 *
 * @since 4.2.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @return bool Whether pretty permalinks are enabled. False otherwise.
 */
function wp_install_maybe_enable_pretty_permalinks() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
global $wp_rewrite;

// Bail if a permalink structure is already enabled.
if ( get_option( 'permalink_structure' ) ) {
	return true;
}

/*
 * The Permalink structures to attempt.
 *
 * The first is designed for mod_rewrite or nginx rewriting.
 *
 * The second is PATHINFO-based permalinks for web server configurations
 * without a true rewrite module enabled.
 */
$permalink_structures = array(
		'/%year%/%monthnum%/%day%/%postname%/',
		'/index.php/%year%/%monthnum%/%day%/%postname%/'
);

foreach ( (array) $permalink_structures as $permalink_structure ) {
	$wp_rewrite->set_permalink_structure( $permalink_structure );
	
	/*
	 * Flush rules with the hard option to force refresh of the web-server's
	 * rewrite config file (e.g. .htaccess or web.config).
	 */
	$wp_rewrite->flush_rules( true );
	
	$test_url = '';
	
	// Test against a real WordPress Post
	$first_post = get_page_by_path( sanitize_title( _x( 'hello-world', 'Default post slug' ) ), OBJECT, 'post' );
	if ( $first_post ) {
		$test_url = get_permalink( $first_post->ID );
	}
	
	/*
	 * Send a request to the site, and check whether
	 * the 'x-pingback' header is returned as expected.
	 *
	 * Uses wp_remote_get() instead of wp_remote_head() because web servers
	 * can block head requests.
	 */
	$response          = wp_remote_get( $test_url, array( 'timeout' => 5 ) );
	$x_pingback_header = wp_remote_retrieve_header( $response, 'x-pingback' );
	$pretty_permalinks = $x_pingback_header && $x_pingback_header === get_bloginfo( 'pingback_url' );
	
	if ( $pretty_permalinks ) {
		return true;
	}
}

/*
 * If it makes it this far, pretty permalinks failed.
 * Fallback to query-string permalinks.
 */
$wp_rewrite->set_permalink_structure( '' );
$wp_rewrite->flush_rules( true );

return false;
}

if ( !function_exists('wp_install_defaults') ) :
function wp_install_defaults( $user_id ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
global $wpdb, $wp_rewrite, $table_prefix;

// Default category
$cat_name = __('Uncategorized');
/* translators: Default category slug */
$cat_slug = sanitize_title(_x('Uncategorized', 'Default category slug'));

if ( global_terms_enabled() ) {
	$cat_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );
	if ( $cat_id == null ) {
		$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
		$cat_id = $wpdb->insert_id;
	}
	update_option('default_category', $cat_id);
} else {
	$cat_id = 1;
}

$wpdb->insert( $wpdb->terms, array('term_id' => $cat_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $cat_id, 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1));
$cat_tt_id = $wpdb->insert_id;

// First post
$now = current_time( 'mysql' );
$now_gmt = current_time( 'mysql', 1 );
$first_post_guid = get_option( 'home' ) . '/?p=1';

if ( is_multisite() ) {
	$first_post = get_site_option( 'first_post' );
	
	if ( ! $first_post ) {
		/* translators: %s: site link */
		$first_post = __( 'Welcome to %s. This is your first post. Edit or delete it, then start blogging!' );
	}
	
	$first_post = sprintf( $first_post,
			sprintf( '<a href="%s">%s</a>', esc_url( network_home_url() ), get_network()->site_name )
			);
	
	// Back-compat for pre-4.4
	$first_post = str_replace( 'SITE_URL', esc_url( network_home_url() ), $first_post );
	$first_post = str_replace( 'SITE_NAME', get_network()->site_name, $first_post );
} else {
	$first_post = __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!' );
}

$wpdb->insert( $wpdb->posts, array(
		'post_author' => $user_id,
		'post_date' => $now,
		'post_date_gmt' => $now_gmt,
		'post_content' => $first_post,
		'post_excerpt' => '',
		'post_title' => __('Hello world!'),
		/* translators: Default post slug */
		'post_name' => sanitize_title( _x('hello-world', 'Default post slug') ),
		'post_modified' => $now,
		'post_modified_gmt' => $now_gmt,
		'guid' => $first_post_guid,
		'comment_count' => 1,
		'to_ping' => '',
		'pinged' => '',
		'post_content_filtered' => ''
));
$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => $cat_tt_id, 'object_id' => 1) );

// Default comment
$first_comment_author = __( 'A WordPress Commenter' );
$first_comment_email = 'wapuu@wordpress.example';
$first_comment_url = 'https://wordpress.org/';
$first_comment = __( 'Hi, this is a comment.
To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.
Commenter avatars come from <a href="https://gravatar.com">Gravatar</a>.' );
if ( is_multisite() ) {
	$first_comment_author = get_site_option( 'first_comment_author', $first_comment_author );
	$first_comment_email = get_site_option( 'first_comment_email', $first_comment_email );
	$first_comment_url = get_site_option( 'first_comment_url', network_home_url() );
	$first_comment = get_site_option( 'first_comment', $first_comment );
}
$wpdb->insert( $wpdb->comments, array(
		'comment_post_ID' => 1,
		'comment_author' => $first_comment_author,
		'comment_author_email' => $first_comment_email,
		'comment_author_url' => $first_comment_url,
		'comment_date' => $now,
		'comment_date_gmt' => $now_gmt,
		'comment_content' => $first_comment
));

// First Page
$first_page = sprintf( __( "This is an example page. It's different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:
		
<blockquote>Hi there! I'm a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin' caught in the rain.)</blockquote>
		
...or something like this:
		
<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>
		
As a new WordPress user, you should go to <a href=\"%s\">your dashboard</a> to delete this page and create new pages for your content. Have fun!" ), admin_url() );
if ( is_multisite() )
	$first_page = get_site_option( 'first_page', $first_page );
	$first_post_guid = get_option('home') . '/?page_id=2';
	$wpdb->insert( $wpdb->posts, array(
			'post_author' => $user_id,
			'post_date' => $now,
			'post_date_gmt' => $now_gmt,
			'post_content' => $first_page,
			'post_excerpt' => '',
			'comment_status' => 'closed',
			'post_title' => __( 'Sample Page' ),
			/* translators: Default page slug */
			'post_name' => __( 'sample-page' ),
			'post_modified' => $now,
			'post_modified_gmt' => $now_gmt,
			'guid' => $first_post_guid,
			'post_type' => 'page',
			'to_ping' => '',
			'pinged' => '',
			'post_content_filtered' => ''
	));
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 2, 'meta_key' => '_wp_page_template', 'meta_value' => 'default' ) );
	
	// Set up default widgets for default theme.
	update_option( 'widget_search', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
	update_option( 'widget_recent-posts', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );
	update_option( 'widget_recent-comments', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );
	update_option( 'widget_archives', array ( 2 => array ( 'title' => '', 'count' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
	update_option( 'widget_categories', array ( 2 => array ( 'title' => '', 'count' => 0, 'hierarchical' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
	update_option( 'widget_meta', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
	update_option( 'sidebars_widgets', array( 'wp_inactive_widgets' => array(), 'sidebar-1' => array( 0 => 'search-2', 1 => 'recent-posts-2', 2 => 'recent-comments-2', 3 => 'archives-2', 4 => 'categories-2', 5 => 'meta-2' ), 'sidebar-2' => array(), 'sidebar-3' => array(), 'array_version' => 3 ) );
	if ( ! is_multisite() )
		update_user_meta( $user_id, 'show_welcome_panel', 1 );
		elseif ( ! is_super_admin( $user_id ) && ! metadata_exists( 'user', $user_id, 'show_welcome_panel' ) )
		update_user_meta( $user_id, 'show_welcome_panel', 2 );
		
		if ( is_multisite() ) {
			// Flush rules to pick up the new page.
			$wp_rewrite->init();
			$wp_rewrite->flush_rules();
			
			$user = new WP_User($user_id);
			$wpdb->update( $wpdb->options, array('option_value' => $user->user_email), array('option_name' => 'admin_email') );
			
			// Remove all perms except for the login user.
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'user_level') );
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'capabilities') );
			
			// Delete any caps that snuck into the previously active blog. (Hardcoded to blog 1 for now.) TODO: Get previous_blog_id.
			if ( !is_super_admin( $user_id ) && $user_id != 1 )
				$wpdb->delete( $wpdb->usermeta, array( 'user_id' => $user_id , 'meta_key' => $wpdb->base_prefix.'1_capabilities' ) );
		}
}
endif;


function wp_should_upgrade_global_tables() {

// Return false early if explicitly not upgrading
if ( defined( 'DO_NOT_UPGRADE_GLOBAL_TABLES' ) ) {
	return false;
}

// Assume global tables should be upgraded
$should_upgrade = true;

// Set to false if not on main network (does not matter if not multi-network)
if ( ! is_main_network() ) {
	$should_upgrade = false;
}

// Set to false if not on main site of current network (does not matter if not multi-site)
if ( ! is_main_site() ) {
	$should_upgrade = false;
}

/**
 * Filters if upgrade routines should be run on global tables.
 *
 * @param bool $should_upgrade Whether to run the upgrade routines on global tables.
 */
return apply_filters( 'wp_should_upgrade_global_tables', $should_upgrade );
}

function dbDelta( $queries = '', $execute = true ) {
global $wpdb;

if ( in_array( $queries, array( '', 'all', 'blog', 'global', 'ms_global' ), true ) )
	$queries = wp_get_db_schema( $queries );
	
	// Separate individual queries into an array
	if ( !is_array($queries) ) {
		$queries = explode( ';', $queries );
		$queries = array_filter( $queries );
	}
	
	/**
	 * Filters the dbDelta SQL queries.
	 *
	 * @since 3.3.0
	 *
	 * @param array $queries An array of dbDelta SQL queries.
	 */
	$queries = apply_filters( 'dbdelta_queries', $queries );
	
	$cqueries = array(); // Creation Queries
	$iqueries = array(); // Insertion Queries
	$for_update = array();
	
	// Create a tablename index for an array ($cqueries) of queries
	foreach ($queries as $qry) {
		if ( preg_match( "|CREATE TABLE ([^ ]*)|", $qry, $matches ) ) {
			$cqueries[ trim( $matches[1], '`' ) ] = $qry;
			$for_update[$matches[1]] = 'Created table '.$matches[1];
		} elseif ( preg_match( "|CREATE DATABASE ([^ ]*)|", $qry, $matches ) ) {
			array_unshift( $cqueries, $qry );
		} elseif ( preg_match( "|INSERT INTO ([^ ]*)|", $qry, $matches ) ) {
			$iqueries[] = $qry;
		} elseif ( preg_match( "|UPDATE ([^ ]*)|", $qry, $matches ) ) {
			$iqueries[] = $qry;
		} else {
			// Unrecognized query type
		}
	}
	
	/**
	 * Filters the dbDelta SQL queries for creating tables and/or databases.
	 *
	 * Queries filterable via this hook contain "CREATE TABLE" or "CREATE DATABASE".
	 *
	 * @since 3.3.0
	 *
	 * @param array $cqueries An array of dbDelta create SQL queries.
	 */
	$cqueries = apply_filters( 'dbdelta_create_queries', $cqueries );
	
	/**
	 * Filters the dbDelta SQL queries for inserting or updating.
	 *
	 * Queries filterable via this hook contain "INSERT INTO" or "UPDATE".
	 *
	 * @since 3.3.0
	 *
	 * @param array $iqueries An array of dbDelta insert or update SQL queries.
	 */
	$iqueries = apply_filters( 'dbdelta_insert_queries', $iqueries );
	
	$text_fields = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
	$blob_fields = array( 'tinyblob', 'blob', 'mediumblob', 'longblob' );
	
	$global_tables = $wpdb->tables( 'global' );
	foreach ( $cqueries as $table => $qry ) {
		// Upgrade global tables only for the main site. Don't upgrade at all if conditions are not optimal.
		if ( in_array( $table, $global_tables ) && false ) { //! wp_should_upgrade_global_tables() ) {
			unset( $cqueries[ $table ], $for_update[ $table ] );
			continue;
		}
		
		// Fetch the table column structure from the database
		$suppress = $wpdb->suppress_errors();
		$tablefields = $wpdb->get_results("DESCRIBE {$table};");
		$wpdb->suppress_errors( $suppress );
		
		if ( ! $tablefields )
			continue;
			
			// Clear the field and index arrays.
			$cfields = $indices = array();
			
			// Get all of the field names in the query from between the parentheses.
			preg_match("|\((.*)\)|ms", $qry, $match2);
			$qryline = trim($match2[1]);
			
			// Separate field lines into an array.
			$flds = explode("\n", $qryline);
			
			// For every field line specified in the query.
			foreach ( $flds as $fld ) {
				$fld = trim( $fld, " \t\n\r\0\x0B," ); // Default trim characters, plus ','.
				
				// Extract the field name.
				preg_match( '|^([^ ]*)|', $fld, $fvals );
				$fieldname = trim( $fvals[1], '`' );
				$fieldname_lowercased = strtolower( $fieldname );
				
				// Verify the found field name.
				$validfield = true;
				switch ( $fieldname_lowercased ) {
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
					case 'spatial':
						$validfield = false;
						
						/*
						 * Normalize the index definition.
						 *
						 * This is done so the definition can be compared against the result of a
						 * `SHOW INDEX FROM $table_name` query which returns the current table
						 * index information.
						 */
						
						// Extract type, name and columns from the definition.
						preg_match(
								'/^'
								.   '(?P<index_type>'             // 1) Type of the index.
								.       'PRIMARY\s+KEY|(?:UNIQUE|FULLTEXT|SPATIAL)\s+(?:KEY|INDEX)|KEY|INDEX'
								.   ')'
								.   '\s+'                         // Followed by at least one white space character.
								.   '(?:'                         // Name of the index. Optional if type is PRIMARY KEY.
								.       '`?'                      // Name can be escaped with a backtick.
								.           '(?P<index_name>'     // 2) Name of the index.
								.               '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
								.           ')'
								.       '`?'                      // Name can be escaped with a backtick.
								.       '\s+'                     // Followed by at least one white space character.
								.   ')*'
								.   '\('                          // Opening bracket for the columns.
								.       '(?P<index_columns>'
								.           '.+?'                 // 3) Column names, index prefixes, and orders.
								.       ')'
								.   '\)'                          // Closing bracket for the columns.
								. '$/im',
								$fld,
								$index_matches
								);
						
						// Uppercase the index type and normalize space characters.
						$index_type = strtoupper( preg_replace( '/\s+/', ' ', trim( $index_matches['index_type'] ) ) );
						
						// 'INDEX' is a synonym for 'KEY', standardize on 'KEY'.
						$index_type = str_replace( 'INDEX', 'KEY', $index_type );
						
						// Escape the index name with backticks. An index for a primary key has no name.
						$index_name = ( 'PRIMARY KEY' === $index_type ) ? '' : '`' . strtolower( $index_matches['index_name'] ) . '`';
						
						// Parse the columns. Multiple columns are separated by a comma.
						$index_columns = array_map( 'trim', explode( ',', $index_matches['index_columns'] ) );
						
						// Normalize columns.
						foreach ( $index_columns as &$index_column ) {
							// Extract column name and number of indexed characters (sub_part).
							preg_match(
									'/'
									.   '`?'                      // Name can be escaped with a backtick.
									.       '(?P<column_name>'    // 1) Name of the column.
									.           '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
									.       ')'
									.   '`?'                      // Name can be escaped with a backtick.
									.   '(?:'                     // Optional sub part.
									.       '\s*'                 // Optional white space character between name and opening bracket.
									.       '\('                  // Opening bracket for the sub part.
									.           '\s*'             // Optional white space character after opening bracket.
									.           '(?P<sub_part>'
									.               '\d+'         // 2) Number of indexed characters.
									.           ')'
									.           '\s*'             // Optional white space character before closing bracket.
									.        '\)'                 // Closing bracket for the sub part.
									.   ')?'
									. '/',
									$index_column,
									$index_column_matches
									);
							
							// Escape the column name with backticks.
							$index_column = '`' . $index_column_matches['column_name'] . '`';
							
							// Append the optional sup part with the number of indexed characters.
							if ( isset( $index_column_matches['sub_part'] ) ) {
								$index_column .= '(' . $index_column_matches['sub_part'] . ')';
							}
						}
						
						// Build the normalized index definition and add it to the list of indices.
						$indices[] = "{$index_type} {$index_name} (" . implode( ',', $index_columns ) . ")";
						
						// Destroy no longer needed variables.
						unset( $index_column, $index_column_matches, $index_matches, $index_type, $index_name, $index_columns );
						
						break;
				}
				
				// If it's a valid field, add it to the field array.
				if ( $validfield ) {
					$cfields[ $fieldname_lowercased ] = $fld;
				}
			}
			
			// For every field in the table.
			foreach ( $tablefields as $tablefield ) {
				$tablefield_field_lowercased = strtolower( $tablefield->Field );
				$tablefield_type_lowercased = strtolower( $tablefield->Type );
				
				// If the table field exists in the field array ...
				if ( array_key_exists( $tablefield_field_lowercased, $cfields ) ) {
					
					// Get the field type from the query.
					preg_match( '|`?' . $tablefield->Field . '`? ([^ ]*( unsigned)?)|i', $cfields[ $tablefield_field_lowercased ], $matches );
					$fieldtype = $matches[1];
					$fieldtype_lowercased = strtolower( $fieldtype );
					
					// Is actual field type different from the field type in query?
					if ($tablefield->Type != $fieldtype) {
						$do_change = true;
						if ( in_array( $fieldtype_lowercased, $text_fields ) && in_array( $tablefield_type_lowercased, $text_fields ) ) {
							if ( array_search( $fieldtype_lowercased, $text_fields ) < array_search( $tablefield_type_lowercased, $text_fields ) ) {
								$do_change = false;
							}
						}
						
						if ( in_array( $fieldtype_lowercased, $blob_fields ) && in_array( $tablefield_type_lowercased, $blob_fields ) ) {
							if ( array_search( $fieldtype_lowercased, $blob_fields ) < array_search( $tablefield_type_lowercased, $blob_fields ) ) {
								$do_change = false;
							}
						}
						
						if ( $do_change ) {
							// Add a query to change the column type.
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[ $tablefield_field_lowercased ];
							$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}
					}
					
					// Get the default value from the array.
					if ( preg_match( "| DEFAULT '(.*?)'|i", $cfields[ $tablefield_field_lowercased ], $matches ) ) {
						$default_value = $matches[1];
						if ($tablefield->Default != $default_value) {
							// Add a query to change the column's default value
							$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";
							$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
						}
					}
					
					// Remove the field from the array (so it's not added).
					unset( $cfields[ $tablefield_field_lowercased ] );
				} else {
					// This field exists in the table, but not in the creation queries?
				}
			}
			
			// For every remaining field specified for the table.
			foreach ($cfields as $fieldname => $fielddef) {
				// Push a query line into $cqueries that adds the field to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
				$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
			}
			
			// Index stuff goes here. Fetch the table index structure from the database.
			$tableindices = $wpdb->get_results("SHOW INDEX FROM {$table};");
			
			if ($tableindices) {
				// Clear the index array.
				$index_ary = array();
				
				// For every index in the table.
				foreach ($tableindices as $tableindex) {
					
					// Add the index to the index data array.
					$keyname = strtolower( $tableindex->Key_name );
					$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
					$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
					$index_ary[$keyname]['index_type'] = $tableindex->Index_type;
				}
				
				// For each actual index in the index array.
				foreach ($index_ary as $index_name => $index_data) {
					
					// Build a create string to compare to the query.
					$index_string = '';
					if ($index_name == 'primary') {
						$index_string .= 'PRIMARY ';
					} elseif ( $index_data['unique'] ) {
						$index_string .= 'UNIQUE ';
					}
					if ( 'FULLTEXT' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'FULLTEXT ';
					}
					if ( 'SPATIAL' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'SPATIAL ';
					}
					$index_string .= 'KEY ';
					if ( 'primary' !== $index_name  ) {
						$index_string .= '`' . $index_name . '`';
					}
					$index_columns = '';
					
					// For each column in the index.
					foreach ($index_data['columns'] as $column_data) {
						if ( $index_columns != '' ) {
							$index_columns .= ',';
						}
						
						// Add the field to the column list string.
						$index_columns .= '`' . $column_data['fieldname'] . '`';
						if ($column_data['subpart'] != '') {
							$index_columns .= '('.$column_data['subpart'].')';
						}
					}
					
					// The alternative index string doesn't care about subparts
					$alt_index_columns = preg_replace( '/\([^)]*\)/', '', $index_columns );
					
					// Add the column list to the index create string.
					$index_strings = array(
							"$index_string ($index_columns)",
							"$index_string ($alt_index_columns)",
					);
					
					foreach ( $index_strings as $index_string ) {
						if ( ! ( ( $aindex = array_search( $index_string, $indices ) ) === false ) ) {
							unset( $indices[ $aindex ] );
							break;
						}
					}
				}
			}
			
			// For every remaining index specified for the table.
			foreach ( (array) $indices as $index ) {
				// Push a query line into $cqueries that adds the index to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD $index";
				$for_update[] = 'Added index ' . $table . ' ' . $index;
			}
			
			// Remove the original table creation query from processing.
			unset( $cqueries[ $table ], $for_update[ $table ] );
	}
	
	$allqueries = array_merge($cqueries, $iqueries);
	if ($execute) {
		foreach ($allqueries as $query) {
			$wpdb->query($query);
		}
	}
	
	return $for_update;
}

function __get_option($setting) {
global $wpdb;
	
	if ($setting == 'home' && defined ( 'WP_HOME' ))
		return untrailingslashit ( WP_HOME );
	
	if ($setting == 'siteurl' && defined ( 'WP_SITEURL' ))
		return untrailingslashit ( WP_SITEURL );
	
	$option = $wpdb->get_var ( $wpdb->prepare ( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", $setting ) );
	
	if ('home' == $setting && '' == $option)
		return __get_option ( 'siteurl' );
	
	if ('siteurl' == $setting || 'home' == $setting || 'category_base' == $setting || 'tag_base' == $setting)
		$option = untrailingslashit ( $option );
	
	return maybe_unserialize ( $option );
}

function make_db_current_silent( $tables = 'all' ) {
	dbDelta( $tables );
}

function wp_install( $blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '', $language = '' ) {
if ( !empty( $deprecated ) )
	_deprecated_argument( __FUNCTION__, '2.6.0' );
	
	wp_check_mysql_version();
	wp_cache_flush();
	make_db_current_silent();
	populate_options();
	populate_roles();
	
	update_option('blogname', $blog_title);
	update_option('admin_email', $user_email);
	update_option('blog_public', $public);
	
	// Freshness of site - in the future, this could get more specific about actions taken, perhaps.
	update_option( 'fresh_site', 1 );
	
	if ( $language ) {
		update_option( 'WPLANG', $language );
	}
	
	$guessurl = wp_guess_url();
	
	update_option('siteurl', $guessurl);
	
	// If not a public blog, don't ping.
	if ( ! $public )
		update_option('default_pingback_flag', 0);
		
		/*
		 * Create default user. If the user already exists, the user tables are
		 * being shared among sites. Just set the role in that case.
		 */
		$user_id = username_exists($user_name);
		$user_password = trim($user_password);
		$email_password = false;
		if ( !$user_id && empty($user_password) ) {
			$user_password = wp_generate_password( 12, false );
			$message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
			$user_id = wp_create_user($user_name, $user_password, $user_email);
			update_user_option($user_id, 'default_password_nag', true, true);
			$email_password = true;
		} elseif ( ! $user_id ) {
			// Password has been provided
			$message = '<em>'.__('Your chosen password.').'</em>';
			$user_id = wp_create_user($user_name, $user_password, $user_email);
		} else {
			$message = __('User already exists. Password inherited.');
		}
		
		$user = new WP_User($user_id);
		$user->set_role('administrator');
		
		wp_install_defaults($user_id);
		
		wp_install_maybe_enable_pretty_permalinks();
		
		flush_rewrite_rules();
		
		wp_new_blog_notification($blog_title, $guessurl, $user_id, ($email_password ? $user_password : __('The password you chose during the install.') ) );
		
		wp_cache_flush();
		
		/**
		 * Fires after a site is fully installed.
		 *
		 * @since 3.9.0
		 *
		 * @param WP_User $user The site owner.
		 */
		do_action( 'wp_install', $user );
		
		return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message);
}

?>