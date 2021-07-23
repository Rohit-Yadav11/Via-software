<?php
/**
 * Helper functions
 *
 * @package     EDD\FreeDownloads\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup form fields
 *
 * @since       1.1.0
 * @return      array $fields The configured fields
 */
function edd_free_downloads_get_form_fields() {
	$fields = array(
		array(
			'id'       => 'edd_free_download_email',
			'type'     => 'text',
			'label'    => edd_get_option( 'edd_free_downloads_email_label', __( 'Email Address', 'edd-free-downloads' ) ),
			'required' => true
		)
	);

	return apply_filters( 'edd_free_downloads_form_fields', $fields );
}


/**
 * Check if a download should use the modal dialog
 *
 * @since       1.0.0
 * @param       int $download_id The ID to check
 * @return      bool $use_modal True if we should use the modal, false otherwise
 */
function edd_free_downloads_use_modal( $download_id = false ) {
	$use_modal = false;
	$sold_out  = false;

	if ( class_exists( 'EDD_Purchase_Limit' ) ) {
		$price_id = false;

		if ( is_user_logged_in() ) {
			$user = new WP_User( get_current_user_id() );
		}

		$email = isset( $user ) ? $user->user_email : false;

		if ( edd_has_variable_prices( $download_id ) ) {
			$prices = edd_get_variable_prices( $download_id );

			foreach ( $prices as $price ) {
				if ( floatval( $price['amount'] ) == 0 ) {
					$price_id = $price['index'];
				}
			}
		}

		$sold_out = edd_pl_is_item_sold_out( $download_id, $price_id, $email );
	}

	if ( get_post_meta( $download_id, '_edd_free_downloads_bypass', true ) !== 'on' && ! $sold_out ) {
		if ( $download_id && ! edd_has_variable_prices( $download_id ) ) {
			if ( edd_is_free_download( $download_id ) ) {
				if ( edd_is_bundled_product( $download_id ) && get_post_meta( $download_id, '_edd_free_downloads_bundle', true ) ) {
					$use_modal = true;
				} elseif ( ! edd_is_bundled_product( $download_id ) ) {
					$use_modal = true;
				}
			}
		} elseif ( edd_has_variable_prices( $download_id ) ) {
			$price = floatval( edd_get_lowest_price_option( $download_id ) );

			if ( $price == 0 ) {
				$use_modal = true;
			}
		}
	}

	return $use_modal;
}


/**
 * Check for supported newsletter plugins
 *
 * @since       1.1.0
 * @return      bool $plugin_exists True if a supported plugin is active, false otherwise
 */
function edd_free_downloads_has_newsletter_plugin() {
	$plugin_exists = false;

	/**
	 * The $supported_plugins array is an array of
	 * plugin CLASSES which use the EDD_Newsletter class
	 */
	$supported_plugins = apply_filters( 'edd_free_downloads_supported_plugins', array(
		'EDD_GetResponse',
		'EDD_MailChimp',
		'EDD_Aweber',
		'EDD_MailPoet',
		'EDD_Sendy',
		'EDD_ConvertKit',
		'EDD_ActiveCampaign'
	) );

	foreach ( $supported_plugins as $plugin_class ) {
		if ( class_exists( $plugin_class ) ) {
			$plugin_exists = true;
		}
	}

	return $plugin_exists;
}


/**
 * Get Free Downloads form errors
 *
 * @since       1.2.5
 * @return      array $errors The existing errors
 */
function edd_free_downloads_form_errors() {
	$errors = apply_filters( 'edd_free_downloads_form_errors', array(
		'email-required'     => __( 'Please enter a valid email address', 'edd-free-downloads' ),
		'email-invalid'      => __( 'Invalid email', 'edd-free-downloads' ),
		'fname-required'     => __( 'Please enter your first name', 'edd-free-downloads' ),
		'lname-required'     => __( 'Please enter your last name', 'edd-free-downloads' ),
		'username-required'  => __( 'Please enter a username', 'edd-free-downloads' ),
		'password-required'  => __( 'Please enter a password', 'edd-free-downloads' ),
		'password2-required' => __( 'Please confirm your password', 'edd-free-downloads' ),
		'password-unmatch'   => __( 'Password and password confirmation do not match', 'edd-free-downloads' ),
		'privacy-policy'     => __( 'Please agree to the privacy policy', 'edd-free-downloads' ),
	) );

	return $errors;
}


/**
 * Get an array of files for a given download
 *
 * @since       2.0.0
 * @param       int $download_id The download to fetch files for
 * @param       int $price_id An optional price ID for this download
 * @return      array $files The array of files for this download
 */
function edd_free_downloads_get_files( $download_id = 0, $price_id = null ) {
	$download_files = edd_get_download_files( $download_id, $price_id );
	$files          = array();

	if ( ! empty( $download_files ) && is_array( $download_files ) ) {
		foreach ( $download_files as $file_key => $file ) {
			$file_name = basename( $file['file'] );
			$file_hash = md5( $file['file'] );

			$files[ $file_hash ] = array( // Using an md5 of the hash as the key assures we just keep each file once.
				'file'        => $file['file'],
				'file_id'     => $file_key,
				'download_id' => $download_id,
				'file_name'   => $file_name,
			);
		}
	} elseif ( edd_is_bundled_product( $download_id ) ) {
		$downloads = edd_get_bundled_products( $download_id );

		foreach ( $downloads as $download ) {
			$download_files = edd_get_download_files( $download );

			if ( ! empty( $download_files ) && is_array( $download_files ) ) {
				foreach ( $download_files as $file_key => $file ) {
					$file_name = basename( $file['file'] );
					$file_hash = md5( $file['file'] );

					$files[ $file_hash ] = array( // Using an md5 of the hash as the key assures we just keep each file once.
						'file'        => $file['file'],
						'file_id'     => $file_key,
						'download_id' => $download,
						'file_name'   => $file_name,
					);
				}
			}
		}
	}

	return $files;
}

/**
 * Helper function to return the a zip file's complete URL.
 *
 * @since 2.2.0
 *
 * @param  integer $download_id Download post ID.
 * @param  string $bundle_id    An MD5 string created by the names of the files attached to the download post.
 * @return string               Complete URL to zip file.
 */
function edd_free_downloads_create_zip_name( $download_id, $bundle_id ) {
	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['basedir'] . '/edd-free-downloads-cache';
	$zip_name   = strtolower( str_replace( ' ', '-', get_bloginfo( 'name' ) ) ) . '-bundle-' . $download_id;

	$zip_file = apply_filters( 'edd_free_downloads_zip_name', $bundle_id . '-' . $zip_name . '.zip' );
	$zip_file = $upload_dir . '/' . $zip_file;

	return $zip_file;
}

/**
 * This function will delete the cached zip file for the download
 * post upon save_post.
 *
 * @since 2.2.0
 *
 * @param  integer $download_id Download post ID
 */
function edd_free_downloads_save_post_clear_file_cache( $download_id ) {

	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['basedir'] . '/edd-free-downloads-cache';

	$file_pattern = $upload_dir . '/*bundle-' . $download_id . '.zip';
	$found_files  = glob( $file_pattern );

	if ( ! empty( $found_files ) ) {
		foreach ( $found_files as $file ) {
			@unlink( $file );
		}
	}

} // End edd_free_downloads_save_post_clear_file_cache
add_action( 'save_post', 'edd_free_downloads_save_post_clear_file_cache', 10, 1 );

/**
 * Compress the files for a given download
 *
 * @since       2.0.0
 * @param       array $files The files to compress
 * @return      string $file The URL of the compressed file
 */
function edd_free_downloads_compress_files( $files = array(), $download_id = 0 ) {
	$file = false;

	if ( class_exists( 'ZipArchive' ) ) {

		$bundle_id = '';

		foreach( $files as $file => $file_data ) {
			$bundle_id .= $file_data['file_name'];
		}

		$bundle_id = md5( $bundle_id );
		$zip_file = edd_free_downloads_create_zip_name( $download_id, $bundle_id );

		/**
		 * Unsetting file on cache timeout setting
		 */
		$edd_invalidate_zip_file_interval = intval( edd_get_option( 'edd_free_downloads_purge_cache_timeout', 1 ) );
		if ( $edd_invalidate_zip_file_interval && file_exists( $zip_file ) ) {

			if ( $edd_invalidate_zip_file_interval ) {

				$file_expiration_time = filemtime( $zip_file ) + ( $edd_invalidate_zip_file_interval * HOUR_IN_SECONDS );

				/**
				 * If our file_expiration_time is less than the current server time
				 * we will unlink ( delete ) the zip file.
				 *
				 * Use `time()` instead of i18n'd time since we're dealing with server level timestamps, not user based.
				 *
				 * All time comparisons are in Unix timestamps.
				 */
				if ( $file_expiration_time < time() ) {
					edd_debug_log( 'Free Downloads - Existing file past expiration: ' . $zip_file );
					@unlink( $zip_file );
				}
			} // End if edd_free_downloads_purge_cache_timeout is set
		} // End unsetting file per edd_free_downloads_purge_cache_timeout

		// If caching is disabled, make sure file is deleted
		if ( file_exists( $zip_file ) && edd_get_option( 'edd_free_downloads_disable_cache', false ) ) {
			@unlink( $zip_file );
		}

		if ( ! file_exists( $zip_file ) ) {
			$zip = new ZipArchive();

			if ( $zip->open( $zip_file, ZIPARCHIVE::CREATE ) !== TRUE ) {
				edd_die( __( 'An unknown error occurred, please try again!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
				exit;
			}

			foreach ( $files as $file => $file_data ) {
				$file_path = $file_data['file'];
				// Is the file hosted locally?
				$hosted = edd_free_downloads_get_host( $file_path );

				if ( $hosted == 'local' ) {
					$file_path = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file_path );
				} else {
					$file_path = edd_free_downloads_fetch_remote_file( $file_path, $hosted );
				}

				$zip->addFile( $file_path, $file_data['file_name'] );
			}

			$zip->close();
		}

		$file = $zip_file;
	}

	return $file;
}


/**
 * Download a given file
 *
 * @since       2.0.0
 * @param       string $download_url The URL of the file to download
 * @return      void
 */
function edd_free_downloads_download_file( $download_url, $hosted ) {
	// If no file found, bail
	if ( ! $download_url ) {
		edd_die( __( 'An unknown error occurred, please try again!', 'edd-free-downloads' ), __( 'Oops!', 'edd-free-downloads' ) );
	}

	$file_name      = basename( $download_url );
	$file_extension = edd_get_file_extension( $download_url );
	$ctype          = edd_get_file_ctype( $file_extension );
	$method         = edd_get_file_download_method();

	if ( ! edd_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
		@set_time_limit(0);
	}

	if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() && version_compare( phpversion(), '5.4', '<' ) ) {
		set_magic_quotes_runtime(0);
	}

	@session_write_close();
	if ( function_exists( 'apache_setenv' ) ) {
		@apache_setenv('no-gzip', 1);
	}
	@ini_set( 'zlib.output_compression', 'Off' );

	nocache_headers();
	header("Robots: none");
	header("Content-Type: " . $ctype . "");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
	header("Content-Transfer-Encoding: binary");

	if ( 'x_sendfile' == $method && ( ! function_exists( 'apache_get_modules' ) || ! in_array( 'mod_xsendfile', apache_get_modules() ) ) ) {
		// If X-Sendfile is selected but is not supported, fallback to Direct
		$method = 'direct';
	}

	$file_details = parse_url( $download_url );
	$schemes      = array( 'http', 'https' ); // Direct URL schemes

	if ( ( ! isset( $file_details['scheme'] ) || ! in_array( $file_details['scheme'], $schemes ) ) && isset( $file_details['path'] ) && file_exists( $download_url ) ) {

		/**
		 * Download method is seto to Redirect in settings but an absolute path was provided
		 * We need to switch to a direct download in order for the file to download properly
		 */
		$method = 'direct';

	}

	if( $hosted === 'dropbox' || $hosted == 'amazon' ) {
		$method = 'redirect';
	}

	switch ( $method ) :

		case 'redirect' :
			// Redirect straight to the file
			edd_deliver_download( $download_url, true );
			break;
		case 'direct' :
		default:
			$direct    = false;
			$file_path = $download_url;

			if ( ( ! isset( $file_details['scheme'] ) || ! in_array( $file_details['scheme'], $schemes ) ) && isset( $file_details['path'] ) && file_exists( $download_url ) ) {
				/** This is an absolute path */
				$direct    = true;
				$file_path = $download_url;
			} elseif ( defined( 'UPLOADS' ) && strpos( $download_url, UPLOADS ) !== false ) {
				/**
				 * This is a local file given by URL so we need to figure out the path
				 * UPLOADS is always relative to ABSPATH
				 * site_url() is the URL to where WordPress is installed
				 */
				$file_path  = str_replace( site_url(), '', $download_url );
				$file_path  = realpath( ABSPATH . $file_path );
				$direct     = true;
			} elseif ( strpos( $download_url, content_url() ) !== false ) {
				/** This is a local file given by URL so we need to figure out the path */
				$file_path  = str_replace( content_url(), WP_CONTENT_DIR, $download_url );
				$file_path  = realpath( $file_path );
				$direct     = true;
			} elseif ( strpos( $download_url, set_url_scheme( content_url(), 'https' ) ) !== false ) {
				/** This is a local file given by an HTTPS URL so we need to figure out the path */
				$file_path  = str_replace( set_url_scheme( content_url(), 'https' ), WP_CONTENT_DIR, $download_url );
				$file_path  = realpath( $file_path );
				$direct     = true;
			} elseif ( strpos( content_url(), 'https://' ) !== false && strpos( $download_url, set_url_scheme( content_url(), 'http' ) ) !== false ) {
				/** This is a local file given by an HTTP URL - but it should be using HTTPS so we need to figure out the path */
				$file_path  = preg_replace( "/^http:/i", "https:", $download_url, 1 );
				$file_path  = str_replace( set_url_scheme( content_url(), 'https' ), WP_CONTENT_DIR, $file_path );
				$file_path  = realpath( $file_path );
				$direct     = true;
			}

			// Set the file size header
			header( "Content-Length: " . @filesize( $file_path ) );

			// Now deliver the file based on the kind of software the server is running / has enabled
			if ( stristr( getenv( 'SERVER_SOFTWARE' ), 'lighttpd' ) ) {
				header( "X-LIGHTTPD-send-file: $file_path" );
			} elseif ( $direct && ( stristr( getenv( 'SERVER_SOFTWARE' ), 'nginx' ) || stristr( getenv( 'SERVER_SOFTWARE' ), 'cherokee' ) ) ) {
				// We need a path relative to the domain
				$file_path = str_ireplace( realpath( $_SERVER['DOCUMENT_ROOT'] ), '', $file_path );
				header( "X-Accel-Redirect: /$file_path" );
			}

			if ( $direct ) {
				edd_deliver_download( $file_path );
			} else {
				// The file supplied does not have a discoverable absolute path
				edd_deliver_download( $download_url, true );
			}

			break;
	endswitch;

	edd_die();
}


/**
 * Fetch files that are remotely hosted and return new path
 *
 * @since       2.0.0
 * @param       string $file_path The remote path of the file
 * @param       string $hosted Where the file is hosted
 * @return      string $file_path The new local path of the file
 */
function edd_free_downloads_fetch_remote_file( $file_path, $hosted ) {
	$wp_upload_dir = wp_upload_dir();
	$filePath      = $wp_upload_dir['basedir'] . '/edd-free-downloads-cache/';

	if ( $hosted == 'amazon' && defined( 'EDD_AS3_VERSION' ) ) {
		// Handle S3
		if ( false !== ( strpos( $file_path, 'AWSAccessKeyId' ) ) ) {
			if ( $url = parse_url( $file_path ) ) {
				$file_path = ltrim( $url['path'], '/' );
			}
		}

		if( function_exists( 'edd_amazon_s3' ) ) {

			return edd_amazon_s3()->get_s3_url( $file_path, 25 );

		} else {

			return $GLOBALS['edd_s3']->get_s3_url( $file_path, 25 );

		}


	} elseif ( $hosted == 'dropbox' ) {
		if ( class_exists( 'EDDDropboxFileStore' ) ) {
			add_filter( 'edd_file_download_method', 'edd_free_downloads_set_download_method' );
			add_filter( 'edd_symlink_file_downloads', 'edd_free_downloads_disable_symlink' );

			$dfs = new EDDDropboxFileStore();
			$dfs->dbfsInit();
			return $dfs->getDownloadURL( $file_path );

		} else {
			return false;
		}
	} else {
		// Fallback
		$fileName = basename( $file_path );
	}

	// If caching is disabled, make sure file is deleted
	if ( file_exists( $filePath . remove_query_arg( 'dl', $fileName ) ) && edd_get_option( 'edd_free_downloads_disable_cache', false ) ) {
		unlink( $filePath . remove_query_arg( 'dl', $fileName ) );
	}

	if ( ! file_exists( $filePath . remove_query_arg( 'dl', $fileName ) ) ) {
		// Remote files must be downloaded to the local machine!
		$args = array(
			'timeout' => 300
		);

		$response = wp_remote_get( $file_path, $args );
		$new_file = wp_remote_retrieve_body( $response );

		file_put_contents( $filePath . urldecode( remove_query_arg( 'dl', $fileName ) ), $new_file );
	}

	return $filePath . remove_query_arg( 'dl', $fileName );
}


/**
 * The DBFS filetype only works with symlinking disabled,
 * disable it specifically for DBFS downloads
 *
 * @since       2.1.7
 * @param       bool $symlink Existing symlink setting
 * @return      false
 */
function edd_free_downloads_disable_symlink( $symlink ) {
	return false;
}


/**
 * The DBFS filetype only works with the redirect method,
 * enforce it specifically for DBFS downloads
 *
 * @since       2.1.7
 * @param       string $method Existing download method
 * @return      string 'redirect'
 */
function edd_free_downloads_set_download_method( $method ) {
	return 'redirect';
}


/**
 * Check if a file is locally or remotely hosted
 *
 * @since       2.1.0
 * @param       string $file_path The path to check
 * @return      string $hosted The hosting location
 */
function edd_free_downloads_get_host( $file_path ) {
	$hosted = '';

	if ( strpos( $file_path, site_url() ) !== false ) {
		$hosted = 'local';
	} elseif ( strpos( $file_path, ABSPATH ) !== false ) {
		$hosted = 'local';
	} elseif ( strpos( $file_path, 'edd-dbfs' ) !== false ) {
		$hosted = 'dropbox';
	} elseif ( filter_var( $file_path, FILTER_VALIDATE_URL ) === FALSE && $file_path[0] !== '/' ) {
		$hosted = 'amazon';
	} elseif ( strpos( $file_path, 'AWSAccessKeyId' ) !== false ) {
		$hosted = 'amazon';
	}

	return $hosted;
}

/**
 * Fallback in case the edd_debug_log function does not exist, which was added in EDD 2.8.7
 *
 * @since 2.2.0
 */
if( ! function_exists( 'edd_debug_log' ) ) {
	function edd_debug_log( $message = '' ) {
		error_log( $message, 3,  trailingslashit( wp_upload_dir() ) . 'edd-debug-log.txt' );
	}
 }
