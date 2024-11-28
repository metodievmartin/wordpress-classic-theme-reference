<?php

/*
 * Plugin Name: Word Count & Read Time Plugin
 * Description: A simple plugin that calculates word and character count and read time for posts.
 * Version: 1.0
 * Author: Martin Metodiev
 * Author URI: https://github.com/metodievmartin
 * Text Domain: wcp-domain
 * Domain Path: /languages
 */

class Word_Count_And_Read_Time_Plugin {
	private $has_word_count;
	private $has_character_count;
	private $has_readtime;
	private $content_location;
	private $section_id;
	private $page;

	public function __construct() {
		$this->section_id          = 'wcp_first_section';
		$this->page                = 'word-count-settings-page';
		$this->has_word_count      = get_option( 'wcp_wordcount', '1' );
		$this->has_character_count = get_option( 'wcp_charactercount', '1' );
		$this->has_readtime        = get_option( 'wcp_readtime', '1' );
		$this->content_location    = get_option( 'wcp_location', '0' );

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'init', array( $this, 'load_languages' ) );
		add_filter( 'the_content', array( $this, 'filter_the_content' ) );
	}

	function add_settings_page() {
		add_options_page(
			esc_html__( 'Word Count Settings', 'wcp-domain' ),
			esc_html__( 'Word Count', 'wcp-domain' ),
			'manage_options',
			$this->page,
			array( $this, 'settings_page_html' ),
		);
	}

	function settings_init() {
		add_settings_section( $this->section_id, null, null, $this->page );

		// Initialises the 'Display Location' setting
		$this->register_location_setting();

		// Initialises the 'Headline Text' setting
		$this->register_headline_setting();

		// Initialises the 'Word Count' setting
		$this->register_word_count_setting();

		// Initialises the 'Character Count' setting
		$this->register_character_count_setting();

		// Initialises the 'Read Time' setting
		$this->register_read_time_setting();
	}

	function filter_the_content( $content ) {
		if ( $this->should_apply_filter() ) {
			return $this->add_html_to_content( $content );
		}

		return $content;
	}

	function load_languages() {
		load_plugin_textdomain(
			'wcp-domain',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	// =========== Helper functions ===========

	private function should_apply_filter() {
		$at_least_one_option_is_checked = ( $this->has_word_count || $this->has_readtime || $this->has_character_count );

		return ( is_main_query() && is_single() && $at_least_one_option_is_checked );
	}

	function sanitize_location( $input ) {
		if ( $input != 0 && $input != '1' ) {
			add_settings_error( 'wcp_location', 'wcp_location_error', 'Display Location must be either beginning or end.' );

			// in case of invalid value fallback to the previously saved value
			return get_option( 'wcp_location', '0' );
		}

		return $input;
	}

	// =========== A single setting registration functions ===========

	private function register_location_setting() {
		add_settings_field(
			'wcp_location',
			esc_html__( 'Display Location', 'wcp-domain' ),
			array( $this, 'display_location_html' ),
			$this->page,
			$this->section_id
		);

		register_setting(
			'wordcountplugin',
			'wcp_location',
			array(
				'sanitize_callback' => array( $this, 'sanitize_location' ),
				'default'           => '0',
			)
		);
	}

	private function register_headline_setting() {
		add_settings_field(
			'wcp_headline',
			esc_html__( 'Headline Text', 'wcp-domain' ),
			array( $this, 'headline_text_html' ),
			$this->page,
			$this->section_id
		);

		register_setting(
			'wordcountplugin',
			'wcp_headline',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Post Statistics',
			)
		);
	}

	private function register_word_count_setting() {
		add_settings_field(
			'wcp_wordcount',
			esc_html__( 'Word Count', 'wcp-domain' ),
			array( $this, 'checkbox_html' ),
			$this->page,
			$this->section_id,
			array( 'option_name' => 'wcp_wordcount' )
		);

		register_setting(
			'wordcountplugin',
			'wcp_wordcount',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '0',
			)
		);
	}

	private function register_character_count_setting() {
		add_settings_field(
			'wcp_charactercount',
			esc_html__( 'Character Count', 'wcp-domain' ),
			array( $this, 'checkbox_html' ),
			$this->page,
			$this->section_id,
			array( 'option_name' => 'wcp_charactercount' )
		);

		register_setting(
			'wordcountplugin',
			'wcp_charactercount',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '0',
			)
		);
	}

	private function register_read_time_setting() {
		add_settings_field(
			'wcp_readtime',
			esc_html__( 'Read Time', 'wcp-domain' ),
			array( $this, 'checkbox_html' ),
			$this->page,
			$this->section_id,
			array( 'option_name' => 'wcp_readtime' )
		);

		register_setting(
			'wordcountplugin',
			'wcp_readtime',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '0',
			)
		);
	}

	// =========== HTML generating functions ===========

	private function add_html_to_content( $content ) {
		$html       = '<h3>' . esc_html( get_option( 'wcp_headline', 'Post Statistic' ) ) . '</h3><p>';
		$word_count = 0;

		if ( $this->has_word_count || $this->has_readtime ) {
			$word_count = str_word_count( strip_tags( $content ) );
		}

		if ( $this->has_word_count ) {
			$html .= 'This post has ' . $word_count . ' words.<br>';
		}

		if ( $this->has_character_count ) {
			$html .= 'This post has ' . strlen( strip_tags( $content ) ) . ' characters.<br>';
		}

		if ( $this->has_readtime ) {
			// assuming that an average adult reads about 225 per minute
			$html .= 'This post will take about ' . round( $word_count / 225 ) . ' minute(s) to read.<br>';
		}

		$html .= '</p>';

		// Adds the HTML at the top of the document
		if ( $this->content_location == '0' ) {
			return $html . $content;
		}

		// Adds the HTML at the bottom of the document
		return $content . $html;
	}

	function settings_page_html() {
		?>

        <div class="wrap">
            <h1><?php esc_html_e( 'Word Count Settings', 'wcp-domain' ) ?></h1>
            <form action="options.php" method="post">

				<?php
				settings_fields( 'wordcountplugin' );
				do_settings_sections( $this->page );
				submit_button();
				?>

            </form>
        </div>

		<?php
	}

	function display_location_html() {
		?>

        <select name="wcp_location">
            <option value="0" <?php selected( get_option( 'wcp_location' ), '0' ); ?>>Beginning of post</option>
            <option value="1" <?php selected( get_option( 'wcp_location' ), '1' ); ?>>End of post</option>
        </select>

		<?php
	}

	function headline_text_html() {
		?>

        <input type="text" name="wcp_headline" value="<?php echo esc_attr( get_option( 'wcp_headline' ) ); ?>">

		<?php
	}

	function checkbox_html( $args ) {
		?>

        <input type="checkbox" name="<?php echo $args['option_name']; ?>"
               value="1" <?php checked( get_option( $args['option_name'], '1' ) ); ?>>

		<?php
	}
}

$word_count_and_read_time_plugin = new Word_Count_And_Read_Time_Plugin();

