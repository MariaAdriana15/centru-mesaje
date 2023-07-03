<?php

namespace UPWPForms;

defined( 'ABSPATH' ) || exit();

class Google_Drive_Uploader {

	/**
	 * @var null
	 */
	protected static $instance = null;

	public $form_data;
	public $form_id;
	public $field_id;
	public $field_data;

	/**
	 * @throws \Exception
	 */
	public function __construct() {

		// Get upload direct url
		add_action( 'wp_ajax_upwpforms_get_google_drive_upload_url', [ $this, 'get_upload_url' ] );
		add_action( 'wp_ajax_nopriv_upwpforms_get_google_drive_upload_url', [ $this, 'get_upload_url' ] );

		add_filter( 'wpforms_process_after_filter', [ $this, 'upload_complete' ], 10, 3 );
	}


	public function upload_complete( $fields, $entry, $form_data ) {

		if ( ! empty( wpforms()->get( 'process' )->errors[ $form_data['id'] ] ) ) {
			return $fields;
		}

		$this->form_data = $form_data;

		foreach ( $fields as $field_id => $field ) {
			if ( empty( $field['type'] )
			     || ! in_array( $field['type'], [ 'google_drive_upload' ] ) ) {
				continue;
			}

			$this->form_id    = absint( $form_data['id'] );
			$this->field_id   = $field_id;
			$this->field_data = ! empty( $this->form_data['fields'][ $field_id ] ) ? $this->form_data['fields'][ $field_id ] : [];
			$is_visible       = ! isset( wpforms()->get( 'process' )->fields[ $field_id ]['visible'] ) || ! empty( wpforms()->get( 'process' )->fields[ $field_id ]['visible'] );

			$fields[ $field_id ]['visible'] = $is_visible;

			if ( ! $is_visible ) {
				continue;
			}

			$files = ! empty( $field['value'] ) ? $field['value'] : [];
			$files = $this->sanitize_files_input( $files );

			if ( empty( $files ) ) {
				$fields[ $field_id ] = $field;
				continue;
			}

			$processed_field = $field;

			$data = [];

			foreach ( $files as $file ) {
				$data[] = $this->generate_file_data( $file );
			}

			$data                         = array_filter( $data );
			$processed_field['value_raw'] = $data;
			$processed_field['value']     = wpforms_chain( $data )
				->map(
					static function ( $file ) {

						return $file['webViewLink'];
					}
				)
				->implode( "\r\n" )
				->value();

			$fields[ $field_id ] = $processed_field;
		}

		return $fields;
	}

	protected function generate_file_data( $file ) {

		return [
			'name'        => sanitize_text_field( $file['name'] ),
			'size'        => absint( $file['size'] ),
			'iconLink'    => esc_url_raw( $file['iconLink'] ),
			'webViewLink' => esc_url_raw( $file['webViewLink'] ),
		];
	}

	private function sanitize_files_input( $files ) {
		$files = json_decode( $files, true );

		if ( empty( $files ) || ! is_array( $files ) ) {
			return [];
		}

		return array_filter( array_map( [ $this, 'sanitize_file' ], $files ) );
	}

	private function sanitize_file( $file ) {

		if ( empty( $file['name'] ) ) {
			return [];
		}

		$sanitized_file = [];
		$rules          = [
			'name'        => 'sanitize_file_name',
			'size'        => 'absint',
			'iconLink'    => 'esc_url_raw',
			'webViewLink' => 'esc_url_raw',
		];

		foreach ( $rules as $rule => $callback ) {
			$file_attribute          = isset( $file[ $rule ] ) ? $file[ $rule ] : '';
			$sanitized_file[ $rule ] = $callback( $file_attribute );
		}

		return $sanitized_file;
	}

	public function get_upload_url() {
		$default_error = esc_html__( 'Something went wrong, please try again.', 'upload-fields-for-wpforms' );

		$validated_form_field = $this->ajax_validate_form_field();
		if ( empty( $validated_form_field ) ) {
			wp_send_json_error( $default_error, 403 );
		}

		if ( empty( $_POST['name'] ) ) {
			wp_send_json_error( $default_error, 403 );
		}

		$extension = strtolower( pathinfo( $_POST['name'], PATHINFO_EXTENSION ) );

		$errors = wpforms_chain( array() )
			->array_merge( (array) $this->validate_size() )
			->array_merge( (array) $this->validate_extension( $extension ) )
			->array_filter()
			->array_unique()
			->value();

		if ( count( $errors ) ) {
			wp_send_json_error( implode( ',', $errors ), 400 );
		}

		$url = App::instance()->get_resume_url( $_POST );

		if ( ! $url ) {
			wp_send_json_error( $default_error, 400 );
		}

		wp_send_json_success( $url );
	}

	protected function ajax_validate_form_field() {

		if ( empty( $_POST['form_id'] ) || empty( $_POST['field_id'] ) ) {
			return [];
		}

		$form_data = wpforms()->form->get( (int) $_POST['form_id'], [ 'content_only' => true ] );

		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return [];
		}

		$field_id = (int) $_POST['field_id'];

		// Make data available everywhere in the class, so we don't need to pass it manually.
		$this->form_data  = $form_data;
		$this->form_id    = $this->form_data['id'];
		$this->field_id   = $field_id;
		$this->field_data = $this->form_data['fields'][ $this->field_id ];

		return [
			'form_data' => $form_data,
			'field_id'  => $field_id,
		];
	}

	protected function validate_size() {
		$size     = ! empty( $_POST['size'] ) ? (int) $_POST['size'] : 0;
		$max_size = $this->max_file_size();

		if ( $size > $max_size ) {
			return sprintf( /* translators: $s - allowed file size in MB. */
				esc_html__( 'File exceeds max size allowed (%s).', 'upload-fields-for-wpforms' ),
				size_format( $max_size )
			);
		}

		return false;
	}

	public function max_file_size() {

		if ( ! empty( $this->field_data['max_size'] ) ) {

			// Strip any suffix provided (eg M, MB etc), which leaves us with the raw MB value.
			$max_size = preg_replace( '/[^0-9.]/', '', $this->field_data['max_size'] );

			return wpforms_size_to_bytes( $max_size . 'M' );
		}

		return wpforms_max_upload( true );
	}

	protected function validate_extension( $ext ) {

		$extensions = ! empty( $this->field_data['extensions'] ) ? explode( ',', $this->field_data['extensions'] ) : [];

		if ( empty( $extensions ) ) {
			return false;
		}

		// Make sure file has an extension first.
		if ( empty( $ext ) ) {
			return esc_html__( 'File must have an extension.', 'upload-fields-for-wpforms' );
		}

		//remove spaces from extensions
		$extensions = array_map( 'trim', $extensions );

		// Validate extension against all allowed values.
		if ( ! in_array( $ext, $extensions, true ) ) {
			return esc_html__( 'File type is not allowed.', 'upload-fields-for-wpforms' );
		}

		return false;
	}

	/**
	 * @return Google_Drive_Uploader|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Google_Drive_Uploader::instance();