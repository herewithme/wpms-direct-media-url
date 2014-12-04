<?php
/*
Plugin Name: WPMS - Direct Medias URL
Version: 1.2
Plugin URI: http://www.beapi.fr
Description: Replace virtual link by direct link for file/folder with blog ID + New media have only direct URL !
Author: Be API
Author URI: http://www.beapi.fr

Copyright 2014 - Amaury BALMER (amaury@beapi.fr)
*/

class Direct_Media_URL {
	public function __construct() {
		if ( !is_admin() ) {
			//add_filter( 'the_content', 'replace_virtual_links' );
			
			// Prefere use a PHP buffer function for replace all links of HTML before PHP send data to broswer !
			ob_start( array(__CLASS__, 'replace_virtual_links'));
		}

		// Medias URL
		add_filter( 'upload_dir', array(__CLASS__, 'upload_dir') );
	}

	/**
	 * Replace old virtual media links by direct links
	 */
	public static function replace_virtual_links( $content ) {
		global $wpdb;

		// Replace mapped URL
		$content = str_replace( get_site_url($wpdb->blogid,'/files'), get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS'))), $content );

		// Replace original URL - WordPress MU Domain Mapping
		if( function_exists('get_original_url') ) {
			$original_url = untrailingslashit(get_original_url( 'siteurl', $wpdb->blogid ));
			
			$content = str_replace( $original_url . '/files', get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS'))), $content );
		}

		// Replace original URL - Domain Mapping (https://premium.wpmudev.org/project/domain-mapping/)
		if( function_exists('dm_site_url') ) {
			$content = str_replace( dm_site_url('/files'), get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS'))), $content );
		}

		// Replace original URL - Domain Mapping (https://premium.wpmudev.org/project/domain-mapping/)
		if( function_exists('dm_home_url') ) {
			$content = str_replace( dm_home_url('/files'), get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS'))), $content );
		}

		return $content;
	}

	/**
	 * Fix URL during WordPress Image processing
	 */
	public static function upload_dir( $uploads ) {
		global $wpdb;

		// Direct URL for future uploads
		$uploads['url'] = get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS')) . $uploads['subdir']);
		// Direct baseURL
		$uploads['baseurl'] = get_site_url($wpdb->blogid,'/'.untrailingslashit(constant('UPLOADS')));

		return $uploads;
	}
}

new Direct_Media_URL();
