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

class Ai1wmge_Import_Incremental_GDrive {

	public static function execute( $params, Ai1wmge_GDrive_Client $gdrive = null ) {

		// Set progress
		Ai1wm_Status::info( __( 'Preparing incremental backup files...', AI1WMGE_PLUGIN_NAME ) );

		// Set Google Drive client
		if ( is_null( $gdrive ) ) {
			$gdrive = new Ai1wmge_GDrive_Client(
				get_option( 'ai1wmge_gdrive_token', false ),
				get_option( 'ai1wmge_gdrive_ssl', true )
			);
		}

		// Download incremental files
		if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'wb' ) ) ) {
			try {
				if ( ( $result = $gdrive->list_folder_by_id( $params['folder_id'], null, null, "title = 'incremental.backups.list'" ) ) ) {
					if ( isset( $result['items'][0]['id'] ) ) {
						$gdrive->get_file_media( $incremental_list, $result['items'][0]['id'] );
					}
				}
			} catch ( Ai1wmge_Error_Exception $e ) {
			}

			ai1wm_close( $incremental_list );
		}

		$incremental_files = array();

		// Get incremental files
		if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'rb' ) ) ) {
			while ( list( $file_index, $file_id, $file_path, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
				$incremental_files[ $file_index ] = array( $file_id, $file_path, $file_size, $file_mtime );
			}

			ai1wm_close( $incremental_list );
		}

		$total_backups_files_size = 1;

		// Get total backups files size
		if ( isset( $params['file_index'] ) ) {
			for ( $i = 0; $i < $params['file_index']; $i++ ) {
				$total_backups_files_size += $incremental_files[ $i ][2];
			}
		}

		// Set total backups files size
		$params['total_backups_files_size'] = $total_backups_files_size;

		// Set file ID
		$params['file_id'] = $incremental_files[0][0];

		// Set file size
		$params['file_size'] = $incremental_files[0][2];

		// Set progress
		Ai1wm_Status::info( __( 'Done preparing incremental backup files.', AI1WMGE_PLUGIN_NAME ) );

		return $params;
	}
}
