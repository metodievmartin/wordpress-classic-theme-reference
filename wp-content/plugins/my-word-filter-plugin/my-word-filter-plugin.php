<?php

/*
 * Plugin Name: My Word Filter Plugin
 * Description: A simple plugin that takes a comma separated list of words and filters those from post content.
 * Version: 1.0
 * Author: Martin Metodiev
 * Author URI: https://github.com/metodievmartin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class My_Word_Filter_Plugin {

	// ========== Constants ==========

	// Admin Menu Page Slugs
	protected const MAIN_MENU_SLUG = 'my-word-filter';
	protected const SUBMENU_OPTIONS_SLUG = 'my-word-filter-options';

	// Settings Group
	protected const SETTINGS_GROUP = 'replacement-fields';

	// Section IDs
	protected const REPLACEMENT_TEXT_SECTION = 'replacement-text-section';

	// Database Option Names
	protected const OPTION_REPLACEMENT_TEXT = 'replacement-text';
	protected const OPTION_FILTER_WORDS = 'plugin_words_to_filter';

	// Default Values
	protected const DEFAULT_REPLACEMENT_TEXT = '****';

	// Nonce Fields
	protected const NONCE_SAVE_FILTER_WORDS = 'save_filter_words_nonce';
	protected const NONCE_SAVE_FILTER_WORDS_ACTION = 'save_filter_words';

	// ========== Static Methods ==========

	public static function fetch_replacement_text_option() {
		return get_option( self::OPTION_REPLACEMENT_TEXT, self::DEFAULT_REPLACEMENT_TEXT );
	}

	public static function fetch_filter_words_option() {
		return get_option( self::OPTION_FILTER_WORDS, '' );
	}

	// ========== Constructor ==========

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_to_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'the_content', array( $this, 'filter_content_logic' ), 100 );
	}

	// ========== Getters ==========

	protected function get_replacement_text() {
		return get_option( self::OPTION_REPLACEMENT_TEXT, self::DEFAULT_REPLACEMENT_TEXT );
	}

	protected function get_filter_words() {
		return get_option( self::OPTION_FILTER_WORDS, '' );
	}

	// ========== Setup Methods (Hook callbacks) ==========

	function add_to_admin_menu() {
		// two different approaches to including a custom SVG icon
		// - inline - WP will convert the colour to gray so that it matches the other icons in dashboard
		// - file include - will keep the original fill colour
		$svg_inline_content = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+';
		//$svg_file_include   = plugin_dir_url( __FILE__ ) . 'assets/plugin-icon.svg';

		$main_menu_page_hook = add_menu_page(
			'Words To Filter',
			'Word Filter',
			'manage_options',
			self::MAIN_MENU_SLUG,
			array( $this, 'word_filter_page_html' ),
			$svg_inline_content,
			100
		);

		add_submenu_page(
			self::MAIN_MENU_SLUG,
			'Word To Filter',
			'Words List',
			'manage_options',
			self::MAIN_MENU_SLUG,
			array( $this, 'word_filter_page_html' )
		);

		add_submenu_page(
			self::MAIN_MENU_SLUG,
			'Word Filter Options',
			'Options',
			'manage_options',
			self::SUBMENU_OPTIONS_SLUG,
			array( $this, 'word_filter_options_page_html' )
		);

		add_action( "load-{$main_menu_page_hook}", array( $this, 'load_main_menu_page_assets' ) );
	}

	function load_main_menu_page_assets() {
		wp_enqueue_style(
			'word-filter-admin-styles',
			plugin_dir_url( __FILE__ ) . 'styles/word-filter-admin.css'
		);
	}

	function register_settings() {
		add_settings_section(
			self::REPLACEMENT_TEXT_SECTION,
			null,
			null,
			self::SUBMENU_OPTIONS_SLUG
		);

		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_REPLACEMENT_TEXT
		);

		add_settings_field(
			self::OPTION_REPLACEMENT_TEXT,
			'Replacement Text',
			array( $this, 'replacement_text_fields_html' ),
			self::SUBMENU_OPTIONS_SLUG,
			self::REPLACEMENT_TEXT_SECTION
		);
	}

	// ========== Handlers & Processing Logic ==========

	function filter_content_logic( $content ) {
		$filter_words = $this->get_filter_words();

		if ( $filter_words ) {
			$filter_words         = explode( ',', $filter_words );
			$filter_words_trimmed = array_map( 'trim', $filter_words );

			return str_ireplace(
				$filter_words_trimmed,
				esc_html( $this->get_replacement_text() ),
				$content
			);
		}

		return $content;
	}

	protected function handle_form() {
		// verifies the Nonce and also checks the current user has the necessary permissions
		if (
			! isset( $_POST[ self::NONCE_SAVE_FILTER_WORDS ] )
			|| ! wp_verify_nonce( $_POST[ self::NONCE_SAVE_FILTER_WORDS ], self::NONCE_SAVE_FILTER_WORDS_ACTION )
			|| ! current_user_can( 'manage_options' )
		) {
			$this->render_error_message( 'Sorry, you do not have permission to perform that action.' );

			return;
		}

		if ( ! isset( $_POST[ self::OPTION_FILTER_WORDS ] ) ) {
			$this->render_error_message( 'Bad request.' );

			return;
		}

		update_option( self::OPTION_FILTER_WORDS, sanitize_text_field( $_POST[ self::OPTION_FILTER_WORDS ] ) );

		$this->render_success_message( 'Your filtered words were saved.' );
	}

	// ========== HTML Generators ==========

	function word_filter_page_html() {
		?>

        <div class="wrap">
            <h1>Word Filter</h1>

			<?php

			if ( isset( $_POST['justsubmitted'] ) == 'true' ) {
				$this->handle_form();
			}

			?>

            <form method="post">
                <input type="hidden" name="justsubmitted" value="true">

				<?php wp_nonce_field( self::NONCE_SAVE_FILTER_WORDS_ACTION, self::NONCE_SAVE_FILTER_WORDS ) ?>

                <label for="<?php echo self::OPTION_FILTER_WORDS ?>">
                    <p>Enter a <strong>comma-separated</strong> list of words to filter from your site's content</p>
                </label>
                <div class="word-filter__flex-container">
                    <textarea name="<?php echo self::OPTION_FILTER_WORDS ?>"
                              id="<?php echo self::OPTION_FILTER_WORDS ?>"
                              placeholder="bad, mean, awful, horrible"
                    ><?php echo esc_html( $this->get_filter_words() ) ?></textarea>
                </div>
                <input type="submit" id="submit" class="button button-primary" value="Save Changes">
            </form>
        </div>

		<?php
	}

	function replacement_text_fields_html() {
		?>

        <input type="text" name="<?php echo self::OPTION_REPLACEMENT_TEXT ?>"
               value="<?php echo esc_attr( $this->get_replacement_text() ); ?>">
        <p class="description">Leave blank to simply remove the filtered words.</p>

		<?php
	}

	function word_filter_options_page_html() {
		?>

        <div class="wrap">
            <h1>Word Filter Options</h1>
            <form action="options.php" method="post">
				<?php

				// here we need to call settings_errors() manually in order to show a success/error notice
				// in the settings section this was not necessary because WP will call it automatically
				settings_errors();

				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( self::SUBMENU_OPTIONS_SLUG );
				submit_button();

				?>
            </form>
        </div>

		<?php
	}

	// ========== Helpers ==========

	protected function render_success_message( $message ) {
		$this->display_admin_notice_html( 'updated', $message );
	}

	protected function render_error_message( $message ) {
		$this->display_admin_notice_html( 'error', $message );
	}

	protected function display_admin_notice_html( $type, $message ) {
		?>

        <div class="<?php echo esc_attr( $type ); ?> notice is-dismissible">
            <p><?php echo esc_html( $message ); ?></p>
        </div>

		<?php
	}

}

$my_word_filter_plugin = new My_Word_Filter_Plugin();

// Exposed functions

/**
 * Get the list of words to filter from the plugin options.
 *
 * @return string The list of words, comma-separated.
 */
function wfp_get_filter_words() {
	return My_Word_Filter_Plugin::fetch_filter_words_option();
}

/**
 * Get the replacement text for filtered words.
 *
 * @return string The replacement text.
 */
function wfp_get_replacement_text() {
	return My_Word_Filter_Plugin::fetch_replacement_text_option();
}