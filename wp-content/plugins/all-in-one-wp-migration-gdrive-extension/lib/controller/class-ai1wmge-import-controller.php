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

class Ai1wmge_Import_Controller {

	public static function button() {
		return Ai1wm_Template::get_content(
			'import/button',
			array( 'token' => get_option( 'ai1wmge_gdrive_token', false ) ),
			AI1WMGE_TEMPLATES_PATH
		);
	}

	public static function picker() {
		Ai1wm_Template::render(
			'import/picker',
			array(),
			AI1WMGE_TEMPLATES_PATH
		);
	}

	public static function browser( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set folder ID
		$folder_id = 'root';
		if ( ! empty( $params['folder_id'] ) ) {
			$folder_id = trim( $params['folder_id'] );
		}

		// Set team drive ID
		$team_drive_id = null;
		if ( ! empty( $params['team_drive_id'] ) ) {
			$team_drive_id = trim( $params['team_drive_id'] );
		}

		// Set next page token
		$next_page_token = null;
		if ( ! empty( $params['next_page_token'] ) ) {
			$next_page_token = trim( $params['next_page_token'] );
		}

		// Set GDrive client
		$gdrive = new Ai1wmge_GDrive_Client(
			get_option( 'ai1wmge_gdrive_token', false ),
			get_option( 'ai1wmge_gdrive_ssl', true )
		);

		try {
			if ( $folder_id === 'drive' ) {
				$response = $gdrive->list_team_drives();
			} else {
				$response = $gdrive->list_folder_by_id( $folder_id, $team_drive_id, $next_page_token, "mimeType = 'application/vnd.google-apps.folder' or fileExtension = 'wpress'", array( 'orderBy' => 'folder,createdDate desc' ) );
			}
		} catch ( Ai1wmge_Error_Exception $e ) {

		}

		$items = array();
		if ( isset( $response['items'] ) ) {
			foreach ( $response['items'] as $item ) {
				$items[] = array(
					'index' => null,
					'id'    => isset( $item['id'] ) ? $item['id'] : null,
					'name'  => isset( $item['name'] ) ? $item['name'] : null,
					'path'  => isset( $item['name'] ) ? $item['name'] : null,
					'date'  => isset( $item['date'] ) ? human_time_diff( $item['date'] ) : null,
					'size'  => isset( $item['bytes'] ) ? ai1wm_size_format( $item['bytes'] ) : null,
					'bytes' => isset( $item['bytes'] ) ? $item['bytes'] : null,
					'ext'   => isset( $item['ext'] ) ? $item['ext'] : null,
					'type'  => isset( $item['type'] ) ? $item['type'] : null,
				);
			}
		}

		echo json_encode( array( 'items' => $items, 'next_page_token' => ( isset( $response['token'] ) ? $response['token'] : null ) ) );
		exit;
	}

	public static function incremental( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set folder ID
		$folder_id = 'root';
		if ( ! empty( $params['folder_id'] ) ) {
			$folder_id = trim( $params['folder_id'] );
		}

		// Set GDrive client
		$gdrive = new Ai1wmge_GDrive_Client(
			get_option( 'ai1wmge_gdrive_token', false ),
			get_option( 'ai1wmge_gdrive_ssl', true )
		);

		try {
			if ( ( $response = $gdrive->list_folder_by_id( $folder_id, null, null, "title = 'incremental.backups.list'" ) ) ) {
				if ( isset( $response['items'][0]['id'] ) ) {
					$file_content = $gdrive->get_file_content( $response['items'][0]['id'] );
				}
			}
		} catch ( Ai1wmge_Error_Exception $e ) {
		}

		$items = array();
		if ( isset( $file_content ) ) {
			foreach ( str_getcsv( $file_content, "\n" ) as $row ) {
				if ( list( $file_index, $file_id, $file_path, $file_size, $file_mtime ) = str_getcsv( $row ) ) {
					$items[] = array(
						'index'  => $file_index,
						'id'     => $file_id,
						'name'   => sprintf( __( 'Restore point %d', AI1WMGE_PLUGIN_NAME ), $file_index ),
						'path'   => $file_path,
						'folder' => $folder_id,
						'date'   => get_date_from_gmt( date( 'Y-m-d H:i:s', $file_mtime ), 'M j, Y g:i a' ),
						'size'   => ai1wm_size_format( $file_size ),
						'bytes'  => $file_size,
						'type'   => 'application/octet-stream',
					);
				}
			}
		}

		echo json_encode( array( 'items' => array_reverse( $items ), 'cursor' => null ) );
		exit;
	}

	public static function pro() {
		return Ai1wm_Template::get_content( 'import/pro', array(), AI1WMGE_TEMPLATES_PATH );
	}
}
