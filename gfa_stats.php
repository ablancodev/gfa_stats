<?php
/**
 * gfastats.php
 *
 * Copyright (c) 2017 Antonio Blanco http://www.blancoleon.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco	
 * @package gfastats
 * @since gfastats 1.0.0
 *
 * Plugin Name: Groups File Access Stats
 * Plugin URI: http://www.eggemplo.com
 * Description: Add a simple statistics menu for GFA
 * Version: 1.0.0
 * Author: eggemplo
 * Author URI: http://www.blancoleon.com
 * License: GPLv3
 */

class GFAStats_Plugin {

	private static $notices = array ();

	public static function init() {
		add_action ( 'init', array ( __CLASS__, 'wp_init' ) );
		add_action ( 'admin_notices', array ( __CLASS__, 'admin_notices' ) );
	}

	public static function wp_init() {
		if ( !defined( 'GFA_PLUGIN_DOMAIN' ) ) {
			self::$notices [] = "<div class='error'>" . __ ( '<strong>Groups File Access Stats</strong> plugin requires <a href="https://codecanyon.net/item/groups-file-access-wordpress-plugin/2228793?ref=eggemplo" target="_blank">Groups File Access</a>.' ) . "</div>";
		} else {

			add_action ( 'admin_menu', array ( __CLASS__, 'admin_menu' ), 40 );

		}
	}

	public static function admin_notices() {
		if (! empty ( self::$notices )) {
			foreach ( self::$notices as $notice ) {
				echo $notice;
			}
		}
	}
	
	/**
	 * Adds the admin section.
	 */
	public static function admin_menu() {
		$admin_page = add_submenu_page(
				'groups-admin',
				__( 'File Access Stats' ),
				__( 'File Access Stats' ),
				GROUPS_ADMINISTER_GROUPS,
				'groups-admin-files-stats',
				array( __CLASS__, 'gfastats_settings' )
		);
	}

	public static function gfastats_settings() {
		global $wpdb;
		?>
		<h2><?php echo __( 'Groups File Access Stats' ); ?></h2>

		<?php 
		$file_access_table = _groups_get_tablename( 'file_access' );
		$user_files_access = $wpdb->get_results( esc_sql( "SELECT * FROM $file_access_table" ) );
		if ( $user_files_access ) {
			echo '<table width="90%" class="wp-list-table widefat fixed">';
			echo '<thead>';
			echo '<tr><th width="20%">Username</th><th width="20%">File</th><th width="20%">Access</th><th width="20%">Remaining</th><th width="20%">Max. allowed per user</th></tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach ( $user_files_access as $user_file_access ) {
				$user_id = $user_file_access->user_id;
				$file_id = $user_file_access->file_id;
				$count   = $user_file_access->count;
				echo '<tr>';
				$user = get_userdata( $user_id );
				echo '<td>' . $user->user_login . '</td>';
				$name = GFA_Shortcodes::groups_file_info( array( 'file_id' => $file_id, 'show' => 'name') );
				echo '<td>' . $name . '</td>';
				echo '<td>' . Groups_File_Access::get_count( $user_id, $file_id ) . '</td>';
				echo '<td>' . Groups_File_Access::get_remaining( $user_id, $file_id ) . '</td>';
				echo '<td>' . Groups_File_Access::get_max_count( $file_id ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		} else {
			echo "No entries.";
		}
	}

}
GFAStats_Plugin::init();

