<?php
/**
 * RT-Importer
 *
 * @package RT-Importer
 */

/**
 * RT_Importer
 */
class RT_Importer extends WXR_Importer {

	/**
	 * //
	 *
	 * @var array
	 */
	protected $cached_attachments = array();

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment.
	 *
	 * @param array  $post Attachment post details from WXR.
	 * @param string $meta //.
	 * @param string $remote_url URL to fetch attachment from.
	 * @return int|WP_Error Post ID on success, WP_Error otherwise.
	 */
	protected function process_attachment( $post, $meta, $remote_url ) {
		if ( ! empty( $this->options['dummy_attachments'] ) ) {
			return $this->process_dummy_attachment( $post, $meta, $remote_url );
		}

		return parent::process_attachment( $post, $meta, $remote_url );
	}

	/**
	 * //
	 *
	 * @param array  $post       //.
	 * @param string $meta       //.
	 * @param string $remote_url //.
	 * @return int|WP_Error
	 */
	protected function process_dummy_attachment( $post, $meta, $remote_url ) {
		$metadata = $this->get_attachment_meta( $meta, '_wp_attachment_metadata' );

		if ( ! isset( $metadata['image_meta'] ) ) {
			return parent::process_attachment( $post, $meta, $remote_url );
		}

		$post['upload_date'] = $post['post_date'];
		if ( preg_match( '/^[0-9]{4}\/[0-9]{2}/', $this->get_attachment_meta( $meta, '_wp_attached_file' ), $matches ) ) {
			$post['upload_date'] = $matches[0];
		}

		// Get placeholder file in the upload dir.
		$upload_dir = wp_upload_dir( $post['upload_date'] );

		// Image name, link, path.
		$image_name = $this->get_dummy_image_name( $remote_url, $metadata );
		$image_path = $upload_dir['path'] . '/' . $image_name;
		$image_link = $upload_dir['url'] . '/' . $image_name;

		if ( ! file_exists( $image_path ) ) {
			$this->make_dummy_image( $metadata, $image_path );
		}

		$info = wp_check_filetype( $image_path );
		$post['post_mime_type'] = $info['type'];

		// WP really likes using the GUID for display. Allow updating it.
		// See https://core.trac.wordpress.org/ticket/33386.
		if ( $this->options['update_attachment_guids'] ) {
			$post['guid'] = $image_link;
		}

		// As per wp-admin/includes/upload.php file.
		$attachment_id = wp_insert_attachment( $post, $image_path );
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		if ( isset( $this->cached_attachments[ $image_name ] ) ) {
			$attachment_metadata = $this->cached_attachments[ $image_name ];
		} else {
			$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $image_path );
			$this->cached_attachments[ $image_name ] = $attachment_metadata;
		}

		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		// Map this image URL later if we need to.
		$this->url_remap[ $remote_url ] = $image_link;

		// If we have a HTTPS URL, ensure the HTTP URL gets replaced too.
		if ( substr( $remote_url, 0, 8 ) === 'https://' ) {
			$insecure_url = 'http' . substr( $remote_url, 5 );
			$this->url_remap[ $insecure_url ] = $image_link;
		}

		return $attachment_id;
	}

	/**
	 * //
	 *
	 * @param  array  $metadata //.
	 * @param  string $save     //.
	 * @return array|false
	 */
	protected function make_dummy_image( $metadata, $save ) {
		$dummy_image = plugin_dir_path( __FILE__ ) . 'img-holder.png';
		$image = wp_get_image_editor( $dummy_image );

		if ( ! is_wp_error( $image ) ) {
			$image->resize( $metadata['width'], $metadata['height'], true );
			return $image->save( $save );
		}

		return false;
	}

	/**
	 * //
	 *
	 * @param  string $url      //.
	 * @param  array  $metadata //.
	 * @return string
	 */
	protected function get_dummy_image_name( $url, $metadata ) {
		$pathinfo = pathinfo( $url );

		return "{$metadata['width']}x{$metadata['height']}.{$pathinfo['extension']}";
	}

	/**
	 * //
	 *
	 * @param  array  $meta //.
	 * @param  string $key  //.
	 * @return mixed
	 */
	protected function get_attachment_meta( $meta, $key ) {
		$return = null;

		foreach ( $meta as $meta_item ) {
			if ( $key === $meta_item['key'] ) {
				$return = $meta_item['value'];
				break;
			}
		}

		return maybe_unserialize( $return );
	}
}
