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

class Ai1wmge_Import_Incremental_Download {

	public static function execute( $params, Ai1wmge_GDrive_Client $gdrive = null ) {

		$params['completed'] = false;

		// Validate file path
		if ( ! isset( $params['file_index'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Google Drive File Index is not specified.', AI1WMGE_PLUGIN_NAME ) );
		}

		// Validate file path
		if ( ! isset( $params['file_id'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Google Drive File ID is not specified.', AI1WMGE_PLUGIN_NAME ) );
		}

		// Validate file size
		if ( ! isset( $params['file_size'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Google Drive File Size is not specified.', AI1WMGE_PLUGIN_NAME ) );
		}

		// Set file chunk size for download
		$file_chunk_size = get_option( 'ai1wmge_gdrive_file_chunk_size', AI1WMGE_DEFAULT_FILE_CHUNK_SIZE );

		// Set Google Drive client
		if ( is_null( $gdrive ) ) {
			$gdrive = new Ai1wmge_GDrive_Client(
				get_option( 'ai1wmge_gdrive_token', false ),
				get_option( 'ai1wmge_gdrive_ssl', true )
			);
		}

		// Set archive offset
		if ( ! isset( $params['archive_offset'] ) ) {
			$params['archive_offset'] = 0;
		}

		// Set file range start
		if ( ! isset( $params['file_range_start'] ) ) {
			$params['file_range_start'] = 0;
		}

		// Set file range end
		if ( ! isset( $params['file_range_end'] ) ) {
			$params['file_range_end'] = $file_chunk_size - 1;
		}

		// Set incremental index
		if ( ! isset( $params['incremental_index'] ) ) {
			$params['incremental_index'] = 0;
		}

		// Set download retries
		if ( ! isset( $params['download_retries'] ) ) {
			$params['download_retries'] = 0;
		}

		if ( ! isset( $params['download_url'] ) ) {
			$params['download_url'] = $gdrive->get_file_url( $params['file_id'] );
		}

		$gdrive->load_download_url( $params['download_url'] );

		// Open the archive file for writing
		if ( ( $archive = fopen( ai1wm_archive_path( $params ), 'cb' ) ) ) {
			if ( ( fseek( $archive, $params['archive_offset'] ) !== -1 ) ) {
				try {

					$params['download_retries'] += 1;

					// Download file chunk data
					$gdrive->get_file( $archive, $params['file_range_start'], $params['file_range_end'] );

					// Unset download retries
					unset( $params['download_retries'] );

				} catch ( Ai1wmge_Connect_Exception $e ) {
					if ( $params['download_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

				// Set archive offset
				$params['archive_offset'] = ftell( $archive );

				// Set file range start
				$params['file_range_start'] = min( $params['file_range_start'] + $file_chunk_size, $params['file_size'] - 1 );

				// Set file range end
				$params['file_range_end'] = min( $params['file_range_end'] + $file_chunk_size, $params['file_size'] - 1 );
			}

			// Get progress
			$progress = (int) ( ( $params['archive_offset'] / $params['total_backups_files_size'] ) * 100 );

			// Set progress
			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( __( 'Downloading %s (%s) [%d%% complete]', AI1WMGE_PLUGIN_NAME ), $params['file_path'], $params['file_size'], $progress ) );
			} else {
				Ai1wm_Status::progress( $progress );
			}

			// Completed?
			if ( $params['file_range_start'] === ( $params['file_size'] - 1 ) ) {
				$incremental_files = array();

				// Get incremental files
				if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'rb' ) ) ) {
					while ( list( $file_index, $file_id, $file_path, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
						$incremental_files[ $file_index ] = array( $file_id, $file_path, $file_size, $file_mtime );
					}

					ai1wm_close( $incremental_list );
				}

				// Unset file parameters
				if ( ++$params['incremental_index'] <= $params['file_index'] ) {
					if ( isset( $incremental_files[ $params['incremental_index'] ][0] ) ) {
						$params['file_id'] = $incremental_files[ $params['incremental_index'] ][0];
					}

					if ( isset( $incremental_files[ $params['incremental_index'] ][2] ) ) {
						$params['file_size'] = $incremental_files[ $params['incremental_index'] ][2];
					}

					if ( isset( $params['archive_offset'] ) ) {
						$params['archive_offset'] -= 4377;
					}

					unset( $params['file_range_start'] );
					unset( $params['file_range_end'] );
					unset( $params['download_url'] );
				} else {
					unset( $params['file_id'] );
					unset( $params['file_size'] );
					unset( $params['file_range_start'] );
					unset( $params['file_range_end'] );
					unset( $params['download_url'] );
					unset( $params['archive_offset'] );
					unset( $params['incremental_index'] );
					unset( $params['completed'] );
				}
			}

			fclose( $archive );
		}

		return $params;
	}
}
