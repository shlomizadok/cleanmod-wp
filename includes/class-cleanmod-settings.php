<?php
/**
 * CleanMod Settings Page
 *
 * Handles the admin settings page for CleanMod configuration.
 *
 * @package CleanMod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CleanMod_Settings
 */
class CleanMod_Settings {

	/**
	 * Option name for storing settings
	 *
	 * @var string
	 */
	const OPTION_NAME = 'cleanmod_settings';

	/**
	 * Initialize settings page
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Add settings page to WordPress admin
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'CleanMod Settings', 'cleanmod' ),
			__( 'CleanMod', 'cleanmod' ),
			'manage_options',
			'cleanmod',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings and fields
	 */
	public function register_settings() {
		register_setting(
			'cleanmod_settings_group',
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'enabled'       => true,
					'api_key'       => '',
					'behavior_flag' => 'hold',
					'behavior_block' => 'spam',
				),
			)
		);

		add_settings_section(
			'cleanmod_main_section',
			__( 'Configuration', 'cleanmod' ),
			array( $this, 'render_section_description' ),
			'cleanmod'
		);

		add_settings_field(
			'cleanmod_enabled',
			__( 'Enable CleanMod', 'cleanmod' ),
			array( $this, 'render_enabled_field' ),
			'cleanmod',
			'cleanmod_main_section'
		);

		add_settings_field(
			'cleanmod_api_key',
			__( 'API Key', 'cleanmod' ),
			array( $this, 'render_api_key_field' ),
			'cleanmod',
			'cleanmod_main_section'
		);

		add_settings_section(
			'cleanmod_behavior_section',
			__( 'Moderation Behavior', 'cleanmod' ),
			array( $this, 'render_behavior_section_description' ),
			'cleanmod'
		);

		add_settings_field(
			'cleanmod_behavior_flag',
			__( 'When decision is "flag"', 'cleanmod' ),
			array( $this, 'render_behavior_flag_field' ),
			'cleanmod',
			'cleanmod_behavior_section'
		);

		add_settings_field(
			'cleanmod_behavior_block',
			__( 'When decision is "block"', 'cleanmod' ),
			array( $this, 'render_behavior_block_field' ),
			'cleanmod',
			'cleanmod_behavior_section'
		);
	}

	/**
	 * Sanitize settings input
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		$sanitized['enabled']       = isset( $input['enabled'] ) && $input['enabled'];
		$sanitized['api_key']       = isset( $input['api_key'] ) ? sanitize_text_field( trim( $input['api_key'] ) ) : '';
		$sanitized['behavior_flag'] = isset( $input['behavior_flag'] ) && in_array( $input['behavior_flag'], array( 'no_change', 'hold' ), true ) ? $input['behavior_flag'] : 'hold';
		$sanitized['behavior_block'] = isset( $input['behavior_block'] ) && in_array( $input['behavior_block'], array( 'hold', 'spam' ), true ) ? $input['behavior_block'] : 'spam';

		return $sanitized;
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( self::OPTION_NAME, array() );
		$api_key  = isset( $settings['api_key'] ) ? trim( $settings['api_key'] ) : '';
		$enabled  = isset( $settings['enabled'] ) ? $settings['enabled'] : true;

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php if ( ! empty( $api_key ) && $enabled ) : ?>
				<div class="notice notice-success inline">
					<p><?php esc_html_e( 'CleanMod is enabled. New comments will be checked using your current settings.', 'cleanmod' ); ?></p>
				</div>
			<?php else : ?>
				<div class="notice notice-info inline">
					<p><?php esc_html_e( 'Add your CleanMod API key to start moderating comments.', 'cleanmod' ); ?></p>
				</div>
			<?php endif; ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'cleanmod_settings_group' );
				do_settings_sections( 'cleanmod' );
				submit_button( __( 'Save Settings', 'cleanmod' ) );
				?>
			</form>

			<div class="cleanmod-help-section" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
				<p>
					<?php
					printf(
						/* translators: %s: URL to CleanMod dashboard */
						esc_html__( 'Get an API key from your %s.', 'cleanmod' ),
						'<a href="https://cleanmod.dev/dashboard/api-keys" target="_blank" rel="noopener noreferrer">' . esc_html__( 'CleanMod dashboard', 'cleanmod' ) . '</a>'
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render section description
	 */
	public function render_section_description() {
		?>
		<p><?php esc_html_e( 'Use an API key from your CleanMod dashboard. Comments are sent to CleanMod\'s moderation API before they\'re approved.', 'cleanmod' ); ?></p>
		<?php
	}

	/**
	 * Render behavior section description
	 */
	public function render_behavior_section_description() {
		?>
		<p><?php esc_html_e( 'CleanMod doesn\'t replace your existing moderation rules – it adds an extra safety net.', 'cleanmod' ); ?></p>
		<?php
	}

	/**
	 * Render enabled field
	 */
	public function render_enabled_field() {
		$settings = get_option( self::OPTION_NAME, array() );
		$enabled  = isset( $settings['enabled'] ) ? $settings['enabled'] : true;
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled]" value="1" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Enable CleanMod moderation', 'cleanmod' ); ?>
		</label>
		<?php
	}

	/**
	 * Render API key field
	 */
	public function render_api_key_field() {
		$settings = get_option( self::OPTION_NAME, array() );
		$api_key  = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[api_key]" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Your CleanMod API key. Required for the plugin to work.', 'cleanmod' ); ?></p>
		<?php
	}

	/**
	 * Render behavior flag field
	 */
	public function render_behavior_flag_field() {
		$settings       = get_option( self::OPTION_NAME, array() );
		$behavior_flag  = isset( $settings['behavior_flag'] ) ? $settings['behavior_flag'] : 'hold';
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[behavior_flag]">
			<option value="no_change" <?php selected( $behavior_flag, 'no_change' ); ?>><?php esc_html_e( 'No change (pass through)', 'cleanmod' ); ?></option>
			<option value="hold" <?php selected( $behavior_flag, 'hold' ); ?>><?php esc_html_e( 'Hold for moderation', 'cleanmod' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'What to do when CleanMod flags a comment for review.', 'cleanmod' ); ?></p>
		<?php
	}

	/**
	 * Render behavior block field
	 */
	public function render_behavior_block_field() {
		$settings        = get_option( self::OPTION_NAME, array() );
		$behavior_block  = isset( $settings['behavior_block'] ) ? $settings['behavior_block'] : 'spam';
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[behavior_block]">
			<option value="hold" <?php selected( $behavior_block, 'hold' ); ?>><?php esc_html_e( 'Hold for moderation', 'cleanmod' ); ?></option>
			<option value="spam" <?php selected( $behavior_block, 'spam' ); ?>><?php esc_html_e( 'Mark as spam', 'cleanmod' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'What to do when CleanMod blocks a comment as toxic.', 'cleanmod' ); ?></p>
		<?php
	}

	/**
	 * Enqueue admin styles
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_styles( $hook ) {
		if ( 'settings_page_cleanmod' !== $hook ) {
			return;
		}

		$css_file = CLEANMOD_PLUGIN_DIR . 'assets/css/admin.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'cleanmod-admin',
				CLEANMOD_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				CLEANMOD_VERSION
			);
		}
	}
}

