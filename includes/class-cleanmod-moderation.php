<?php
/**
 * CleanMod Comment Moderation
 *
 * Handles WordPress comment moderation using CleanMod API.
 *
 * @package CleanMod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CleanMod_Moderation
 */
class CleanMod_Moderation {

	/**
	 * Initialize moderation hooks
	 */
	public function init() {
		// Hook into comment approval process
		add_filter( 'pre_comment_approved', array( $this, 'filter_pre_comment_approved' ), 10, 2 );
	}

	/**
	 * Filter comment approval based on CleanMod decision
	 *
	 * This hook runs before a comment is saved, allowing us to influence
	 * the comment_approved value based on CleanMod's decision.
	 *
	 * @param int|string|WP_Error $approved The approval status. Accepts 1, 0, 'spam', 'trash', or WP_Error.
	 * @param array               $commentdata Comment data array.
	 * @return int|string|WP_Error Modified approval status.
	 */
	public function filter_pre_comment_approved( $approved, $commentdata ) {
		// Get settings
		$settings = get_option( CleanMod_Settings::OPTION_NAME, array() );
		$enabled  = isset( $settings['enabled'] ) ? $settings['enabled'] : true;
		$api_key  = isset( $settings['api_key'] ) ? trim( $settings['api_key'] ) : '';

		// No-op if disabled or not configured
		if ( ! $enabled || empty( $api_key ) ) {
			return $approved;
		}

		// Skip if already approved/trashed/spam (admin actions, etc.)
		// We only want to moderate new comments from the front-end
		if ( is_admin() && ! wp_doing_ajax() ) {
			// Allow admin bulk actions to pass through
			return $approved;
		}

		// Extract comment text
		$text = isset( $commentdata['comment_content'] ) ? wp_strip_all_tags( $commentdata['comment_content'] ) : '';

		// Skip empty comments
		if ( '' === $text ) {
			return $approved;
		}

		// Call CleanMod API
		$client = new CleanMod_Client( $api_key );
		$result = $client->moderate( $text );

		// Fail-open: if API fails, don't break comment submission
		if ( is_wp_error( $result ) ) {
			// Allow other plugins/themes to log errors via filter
			do_action( 'cleanmod_api_error', $result, $commentdata );
			return $approved;
		}

		// Extract decision from response
		$decision = isset( $result['decision'] ) ? $result['decision'] : 'allow';

		// Get behavior settings
		$behavior_flag  = isset( $settings['behavior_flag'] ) ? $settings['behavior_flag'] : 'hold';
		$behavior_block = isset( $settings['behavior_block'] ) ? $settings['behavior_block'] : 'spam';

		// Apply decision
		switch ( $decision ) {
			case 'block':
				// Block decision: mark as spam or hold
				if ( 'spam' === $behavior_block ) {
					return 'spam';
				}
				return 0; // Hold for moderation

			case 'flag':
				// Flag decision: hold or no change
				if ( 'hold' === $behavior_flag ) {
					return 0; // Hold for moderation
				}
				return $approved; // No change

			case 'allow':
			default:
				// Allow decision: no change
				return $approved;
		}
	}
}

