<?php
/**
 * CleanMod API Client
 *
 * Handles HTTP communication with the CleanMod moderation API.
 *
 * @package CleanMod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CleanMod_Client
 */
class CleanMod_Client {

	/**
	 * API key for authentication
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * Base URL for the CleanMod API
	 *
	 * @var string
	 */
	protected $base_url;

	/**
	 * Constructor
	 *
	 * @param string $api_key API key for authentication.
	 * @param string $base_url Base URL for the API (defaults to CLEANMOD_API_BASE).
	 */
	public function __construct( $api_key, $base_url = CLEANMOD_API_BASE ) {
		$this->api_key  = $api_key;
		$this->base_url = untrailingslashit( $base_url );
	}

	/**
	 * Moderate text using CleanMod API
	 *
	 * @param string $text Text to moderate.
	 * @param string $model Model to use (default: 'english-basic').
	 * @return array|WP_Error Response data with 'decision' key, or WP_Error on failure.
	 */
	public function moderate( $text, $model = 'english-basic' ) {
		if ( empty( $this->api_key ) || empty( $text ) ) {
			return new WP_Error( 'cleanmod_missing_data', 'Missing API key or text.' );
		}

		$response = wp_remote_post(
			$this->base_url . '/api/v1/moderate',
			array(
				'timeout' => 5,
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'text'  => $text,
						'model' => $model,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( $code < 200 || $code >= 300 ) {
			$error_data = json_decode( $body, true );
			$error_msg  = isset( $error_data['error'] ) ? $error_data['error'] : 'CleanMod API error: ' . $code;
			return new WP_Error( 'cleanmod_bad_status', $error_msg, array( 'status' => $code ) );
		}

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || ! isset( $data['decision'] ) ) {
			return new WP_Error( 'cleanmod_invalid_body', 'Invalid response from CleanMod API.' );
		}

		return $data;
	}
}

