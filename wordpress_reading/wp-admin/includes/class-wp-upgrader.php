<?php
/**
 * Upgrade API: WP_Upgrader class
 *
 * Requires skin classes and WP_Upgrader subclasses for backward compatibility.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */

/** WP_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';

/** Language_Pack_Upgrader class */
require_once ABSPATH . 'wp-admin/includes/class-language-pack-upgrader.php';

/** Plugin_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader-skin.php';

/** Theme_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader-skin.php';

/** Bulk_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-bulk-upgrader-skin.php';

/** Bulk_Plugin_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-bulk-plugin-upgrader-skin.php';

/** Bulk_Theme_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-bulk-theme-upgrader-skin.php';

/** Plugin_Installer_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php';

/** Theme_Installer_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-theme-installer-skin.php';

/** Language_Pack_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-language-pack-upgrader-skin.php';

/** Automatic_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

/** WP_Ajax_Upgrader_Skin class */
require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';


class WP_Upgrader {
	public $strings = array();
	public $skin = null;
	
	public $result = array();
	
	public $update_count = 0;
	
	public $update_current = 0;
	
	public function __construct( $skin = null ) {
	if ( null == $skin )
		$this->skin = new WP_Upgrader_Skin();
		else
			$this->skin = $skin;
	}
	
	
	public function init() {
		$this->skin->set_upgrader($this);
		$this->generic_strings();
	}
	
	public function generic_strings() {
		$this->strings['bad_request'] = __('Invalid data provided.');
		$this->strings['fs_unavailable'] = __('Could not access filesystem.');
		$this->strings['fs_error'] = __('Filesystem error.');
		$this->strings['fs_no_root_dir'] = __('Unable to locate WordPress root directory.');
		$this->strings['fs_no_content_dir'] = __('Unable to locate WordPress content directory (wp-content).');
		$this->strings['fs_no_plugins_dir'] = __('Unable to locate WordPress plugin directory.');
		$this->strings['fs_no_themes_dir'] = __('Unable to locate WordPress theme directory.');
		/* translators: %s: directory name */
		$this->strings['fs_no_folder'] = __('Unable to locate needed folder (%s).');
		
		$this->strings['download_failed'] = __('Download failed.');
		$this->strings['installing_package'] = __('Installing the latest version&#8230;');
		$this->strings['no_files'] = __('The package contains no files.');
		$this->strings['folder_exists'] = __('Destination folder already exists.');
		$this->strings['mkdir_failed'] = __('Could not create directory.');
		$this->strings['incompatible_archive'] = __('The package could not be installed.');
		$this->strings['files_not_writable'] = __( 'The update cannot be installed because we will be unable to copy some files. This is usually due to inconsistent file permissions.' );
		
		$this->strings['maintenance_start'] = __('Enabling Maintenance mode&#8230;');
		$this->strings['maintenance_end'] = __('Disabling Maintenance mode&#8230;');
	}
	
	public function set_upgrader(&$upgrader) {
	if ( is_object($upgrader) )
		$this->upgrader =& $upgrader;
		$this->add_strings();
	}
	
	public function fs_connect( $directories = array(), $allow_relaxed_file_ownership = false ) {
	global $wp_filesystem;
	
	if ( false === ( $credentials = $this->skin->request_filesystem_credentials( false, $directories[0], $allow_relaxed_file_ownership ) ) ) {
		return false;
	}
	
	if ( ! WP_Filesystem( $credentials, $directories[0], $allow_relaxed_file_ownership ) ) {
		$error = true;
		if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() )
			$error = $wp_filesystem->errors;
			// Failed to connect, Error and request again
			$this->skin->request_filesystem_credentials( $error, $directories[0], $allow_relaxed_file_ownership );
			return false;
	}
	
	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', $this->strings['fs_unavailable'] );
		
		if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
			return new WP_Error('fs_error', $this->strings['fs_error'], $wp_filesystem->errors);
			
			foreach ( (array)$directories as $dir ) {
				switch ( $dir ) {
					case ABSPATH:
						if ( ! $wp_filesystem->abspath() )
							return new WP_Error('fs_no_root_dir', $this->strings['fs_no_root_dir']);
							break;
					case WP_CONTENT_DIR:
						if ( ! $wp_filesystem->wp_content_dir() )
							return new WP_Error('fs_no_content_dir', $this->strings['fs_no_content_dir']);
							break;
					case WP_PLUGIN_DIR:
						if ( ! $wp_filesystem->wp_plugins_dir() )
							return new WP_Error('fs_no_plugins_dir', $this->strings['fs_no_plugins_dir']);
							break;
					case get_theme_root():
						if ( ! $wp_filesystem->wp_themes_dir() )
							return new WP_Error('fs_no_themes_dir', $this->strings['fs_no_themes_dir']);
							break;
					default:
						if ( ! $wp_filesystem->find_folder($dir) )
							return new WP_Error( 'fs_no_folder', sprintf( $this->strings['fs_no_folder'], esc_html( basename( $dir ) ) ) );
							break;
				}
			}
			return true;
	}
}

?>