<?php
/**
 * Copyright (C) 2014-2020 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

/**
 * Check whether export/import is running
 *
 * @return boolean
 */
function ai1wmge_is_running() {
	if ( isset( $_GET['gdrive'] ) || isset( $_POST['gdrive'] ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether export/import is incremental
 *
 * @return boolean
 */
function ai1wmge_is_incremental() {
	if ( isset( $_GET['gdrive'], $_GET['incremental'] ) || isset( $_POST['gdrive'], $_POST['incremental'] ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether current user is Google Drive Admin
 *
 * @return boolean
 */
function ai1wmge_is_admin() {
	return current_user_can( 'ai1wm_gdrive_admin' ) || ! get_option( 'ai1wmge_gdrive_lock_mode', false );
}

/**
 * Get Google Drive root folder
 *
 * @return mixed
 */
function ai1wmge_get_root_folder() {
	if ( ai1wmge_is_admin() ) {
		return null;
	}

	return array(
		'folder_id'     => get_option( 'ai1wmge_gdrive_folder_id', null ),
		'team_drive_id' => get_option( 'ai1wmge_gdrive_team_drive_id', null ),
	);
}
