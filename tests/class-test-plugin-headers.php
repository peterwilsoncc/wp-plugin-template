<?php
/**
 * Test Plugin Readme and PHP Headers
 *
 * @package WpPluginTemplate
 */

namespace PWCC\WpPluginTemplate\Tests;

use WP_UnitTestCase;

const OPTIONAL  = 0;
const REQUIRED  = 1;
const FORBIDDEN = 2;

const WP_ORG_ASSETS_DIR = __DIR__ . '/../.wordpress-org';

/**
 * Test Plugin Readme and PHP Headers
 */
class Test_Plugin_Headers extends WP_UnitTestCase {

	/**
	 * Readme headers specification
	 *
	 * @var array<string,int> Headers defined in the readme spec. Key: Header; Value: OPTIONAL, REQUIRED, FORBIDDEN.
	 */
	public static $readme_headers = array(
		'Contributors'      => REQUIRED,
		'Tags'              => OPTIONAL,
		'Donate link'       => OPTIONAL,
		'Tested up to'      => REQUIRED,
		'Stable tag'        => REQUIRED,
		'License'           => REQUIRED,
		'License URI'       => OPTIONAL,

		// Plugin file headers that do not belong in the readme.
		'Plugin Name'       => FORBIDDEN,
		'Plugin URI'        => FORBIDDEN,
		'Description'       => FORBIDDEN,
		'Version'           => FORBIDDEN,
		'Author'            => FORBIDDEN,
		'Author URI'        => FORBIDDEN,
		'Text Domain'       => FORBIDDEN,
		'Domain Path'       => FORBIDDEN,
		'Network'           => FORBIDDEN,
		'Update URI'        => FORBIDDEN,
		'Requires at least' => FORBIDDEN, // Both WP and the plugin directory prefer the version in the plugin file.
		'Requires PHP'      => FORBIDDEN, // Both WP and the plugin directory prefer the version in the plugin file.
		'Requires Plugins'  => FORBIDDEN,
	);

	/**
	 * Plugin headers specification
	 *
	 * @var array<string,int> Headers defined in the plugin spec. Key: Header; Value: OPTIONAL, REQUIRED, FORBIDDEN.
	 */
	public static $plugin_headers = array(
		'Plugin Name'       => REQUIRED,
		'Plugin URI'        => OPTIONAL,
		'Description'       => REQUIRED,
		'Version'           => REQUIRED,
		'Requires at least' => REQUIRED, // Not required by the spec but I'm enforcing it.
		'Requires PHP'      => REQUIRED, // Not required by the spec but I'm enforcing it.
		'Author'            => REQUIRED,
		'Author URI'        => OPTIONAL,
		'License'           => REQUIRED,
		'License URI'       => OPTIONAL,
		'Text Domain'       => OPTIONAL,
		'Domain Path'       => OPTIONAL,
		'Network'           => OPTIONAL,
		'Update URI'        => OPTIONAL,
		'Requires Plugins'  => OPTIONAL,

		// Readme file headers that do not belong in the plugin file.
		'Contributors'      => FORBIDDEN,
		'Tags'              => FORBIDDEN,
		'Donate link'       => FORBIDDEN,
		'Stable tag'        => FORBIDDEN,

		/*
		 * Opinionated: Allowed by the spec.
		 *
		 * The WordPress plugin directory will use the plugin file headers if
		 * it exists, and fall back to the readme file if it does not.
		 *
		 * However, the 10up Github Action for deploying updates to the
		 * directory will require a version bump if the plugin file is
		 * modified, so it's best to keep tested up to in the readme file.
		 *
		 * WordPress Core doesn't use the header, it pulls the data in
		 * from the plugin API.
		 */
		'Tested up to'      => FORBIDDEN,
	);

	/**
	 * Deprecated headers mapping.
	 *
	 * Opinionated: These headers are parsed correctly by the WordPress.org
	 * plugin repository but go against the recommended headers in the
	 * documentation.
	 *
	 * @var array<string,string> Mapping of deprecated header to current header.
	 */
	public static $deprecated_headers = array(
		'Tested'   => 'Tested up to',
		'Requires' => 'Requires at least',
	);

	/**
	 * Headers defined in the plugins readme.text file.
	 *
	 * @var string[] Headers defined in the readme spec Header => value.
	 */
	public static $defined_readme_headers = array();

	/**
	 * Headers defined in the plugin file.
	 *
	 * @var string[] Headers defined in the plugin spec Header => value.
	 */
	public static $defined_plugin_headers = array();

	/**
	 * Plugin file names.
	 *
	 * @var string[] The readme and plugin file names.
	 */
	public static $file_names = array();

	/**
	 * Set up shared fixtures.
	 */
	public static function wpSetupBeforeClass() {
		// Get the file names.
		self::$file_names['readme'] = __DIR__ . '/../readme.txt';

		$plugin_file_name = basename( dirname( __DIR__ ) ) . '.php';
		if ( ! file_exists( __DIR__ . "/../{$plugin_file_name}" ) ) {
			// Fallback to the generic plugin file name.
			$plugin_file_name = 'plugin.php';
		}

		self::$file_names['plugin'] = __DIR__ . "/../{$plugin_file_name}";

		// Get the readme headers.
		$readme_file_data = array();
		foreach ( self::$readme_headers as $header => $required ) {
			$readme_file_data[ $header ] = $header;
		}
		self::$defined_readme_headers = get_file_data(
			self::$file_names['readme'],
			$readme_file_data
		);
		self::$defined_readme_headers = array_filter( self::$defined_readme_headers );

		// Get the plugin headers.
		$plugin_file_data = array();
		foreach ( self::$plugin_headers as $header => $required ) {
			$plugin_file_data[ $header ] = $header;
		}

		self::$defined_plugin_headers = get_file_data(
			self::$file_names['plugin'],
			$plugin_file_data
		);
		self::$defined_plugin_headers = array_filter( self::$defined_plugin_headers );
	}

	/**
	 * Test that the readme file has all required headers.
	 *
	 * @dataProvider data_required_readme_headers
	 *
	 * @param string $header Header to test.
	 */
	public function test_required_readme_headers( $header ) {
		$this->assertArrayHasKey( $header, self::$defined_readme_headers, "The readme file header '{$header}' is missing." );
		$this->assertNotEmpty( self::$defined_readme_headers[ $header ], "The readme file header '{$header}' is empty." );
	}

	/**
	 * Data provider for test_required_readme_headers.
	 *
	 * @return array[] Data provider.
	 */
	public function data_required_readme_headers() {
		$required_headers = array_filter(
			self::$readme_headers,
			function ( $status ) {
				return REQUIRED === $status;
			}
		);
		$headers          = array();
		foreach ( $required_headers as $header => $required ) {
			$headers[ $header ] = array( $header );
		}
		return $headers;
	}

	/**
	 * Test that the readme file does not have any forbidden headers.
	 *
	 * @dataProvider data_forbidden_readme_headers
	 *
	 * @param string $header Header to test.
	 */
	public function test_forbidden_readme_headers( $header ) {
		$this->assertArrayNotHasKey( $header, self::$defined_readme_headers, "The readme file header '{$header}' is forbidden." );
	}

	/**
	 * Data provider for test_forbidden_readme_headers.
	 *
	 * @return array[] Data provider.
	 */
	public function data_forbidden_readme_headers() {
		$forbidden_headers = array_filter(
			self::$readme_headers,
			function ( $status ) {
				return FORBIDDEN === $status;
			}
		);
		$headers           = array();
		foreach ( $forbidden_headers as $header => $required ) {
			$headers[ $header ] = array( $header );
		}
		return $headers;
	}

	/**
	 * Test that the plugin file has all required headers.
	 *
	 * @dataProvider data_required_plugin_headers
	 *
	 * @param string $header Header to test.
	 */
	public function test_required_plugin_headers( $header ) {
		$this->assertArrayHasKey( $header, self::$defined_plugin_headers, "The plugin file header '{$header}' is missing." );
		$this->assertNotEmpty( self::$defined_plugin_headers[ $header ], "The readme file header '{$header}' is empty." );
	}

	/**
	 * Data provider for test_required_plugin_headers.
	 *
	 * @return array[] Data provider.
	 */
	public function data_required_plugin_headers() {
		$required_headers = array_filter(
			self::$plugin_headers,
			function ( $status ) {
				return REQUIRED === $status;
			}
		);
		$headers          = array();
		foreach ( $required_headers as $header => $required ) {
			$headers[ $header ] = array( $header );
		}
		return $headers;
	}

	/**
	 * Test that the plugin file does not have any forbidden headers.
	 *
	 * @dataProvider data_forbidden_plugin_headers
	 *
	 * @param string $header Header to test.
	 */
	public function test_forbidden_plugin_headers( $header ) {
		$this->assertArrayNotHasKey( $header, self::$defined_plugin_headers, "The plugin file header '{$header}' is forbidden." );
	}

	/**
	 * Data provider for test_forbidden_plugin_headers.
	 *
	 * @return array[] Data provider.
	 */
	public function data_forbidden_plugin_headers() {
		$forbidden_headers = array_filter(
			self::$plugin_headers,
			function ( $status ) {
				return FORBIDDEN === $status;
			}
		);
		$headers           = array();
		foreach ( $forbidden_headers as $header => $required ) {
			$headers[ $header ] = array( $header );
		}
		return $headers;
	}

	/**
	 * Test that headers defined in both the readme and plugin file match.
	 *
	 * @dataProvider data_common_headers_match
	 *
	 * @param string      $plugin_header_name Plugin file header name to test.
	 * @param string|null $readme_header_name Readme file header name to test. If null, the plugin header name will be used.
	 */
	public function test_common_headers_match( $plugin_header_name, $readme_header_name = null ) {
		$readme_header_name = $readme_header_name ?? $plugin_header_name;
		if ( empty( self::$defined_plugin_headers[ $plugin_header_name ] ) || empty( self::$defined_readme_headers[ $readme_header_name ] ) ) {
			// The header is not common to both files so the test passes.
			$this->assertTrue( true );
			return;
		}

		$plugin_header = self::$defined_plugin_headers[ $plugin_header_name ];
		$readme_header = self::$defined_readme_headers[ $readme_header_name ];

		$message = "The header '{$plugin_header_name}' does not match between the readme and plugin file.";
		if ( $plugin_header_name !== $readme_header_name ) {
			$message = "The plugin header '{$plugin_header_name}' does not match the readme header '{$readme_header_name}'.";
		}

		$this->assertSame( $plugin_header, $readme_header, $message );
	}

	/**
	 * Data provider for test_common_headers_match.
	 *
	 * @return array[] Data provider.
	 */
	public function data_common_headers_match() {
		// Can't use the defined headers as they are not defined until after this is called.
		$common_headers = array_intersect_key(
			self::$readme_headers,
			self::$plugin_headers
		);

		$headers = array();
		// Always test the version matches the stable tag.
		$headers['Stable tag matches version'] = array( 'Version', 'Stable tag' );

		foreach ( $common_headers as $header => $value ) {
			$headers[ $header ] = array( $header );
		}
		return $headers;
	}

	/**
	 * Test that no deprecated headers are used.
	 *
	 * @dataProvider data_no_deprecated_headers
	 *
	 * @param string $file              File to test, either 'readme' or 'plugin'.
	 * @param string $deprecated_header Deprecated header to test.
	 * @param string $correct_header    Correct header to use.
	 */
	public function test_no_deprecated_headers( $file, $deprecated_header, $correct_header ) {
		$file_name    = 'readme' === $file ? self::$file_names['readme'] : self::$file_names['plugin'];
		$file_data    = array(
			$deprecated_header => $deprecated_header,
		);
		$defined_data = get_file_data(
			$file_name,
			$file_data
		);
		$defined_data = array_filter( $defined_data );
		$this->assertArrayNotHasKey( $deprecated_header, $defined_data, "The {$file} file header '{$deprecated_header}' is deprecated. Use '{$correct_header}' instead." );
	}

	/**
	 * Data provider for test_no_deprecated_headers.
	 *
	 * @return array[] Data provider.
	 */
	public function data_no_deprecated_headers() {
		$files = array( 'readme', 'plugin' );
		foreach ( $files as $file ) {
			foreach ( self::$deprecated_headers as $deprecated_header => $correct_header ) {
				$test_name           = "{$file} - {$deprecated_header}";
				$tests[ $test_name ] = array( $file, $deprecated_header, $correct_header );
			}
		}

		return $tests;
	}

	/**
	 * Ensure that the plugin banner includes a low resolution version.
	 *
	 * Per the plugin asset guidelines, the high resolution (retina) banner can
	 * not be used alone, it must be accompanied by a low resolution version.
	 *
	 * @dataProvider data_banner_includes_low_res_version
	 *
	 * @param string $banner Hi-res banner file name to test.
	 */
	public function test_banner_includes_low_res_version( $banner ) {
		// Remove the extension from the banner file name.
		$high_res_banner_prefix = pathinfo( $banner, PATHINFO_FILENAME ) . '.';

		$low_res_banner_prefix = str_replace(
			'1544x500',
			'772x250',
			$high_res_banner_prefix
		);

		$file_list = scandir( WP_ORG_ASSETS_DIR );
		// Search for the low resolution banner file.
		$low_res_files = array_filter(
			$file_list,
			function ( $file ) use ( $low_res_banner_prefix ) {
				return str_starts_with( $file, $low_res_banner_prefix );
			}
		);

		$this->assertNotEmpty(
			$low_res_files,
			"Low resolution banner file for '{$banner}' does not exist."
		);
	}

	/**
	 * Data provider for test_banner_includes_low_res_version.
	 *
	 * @return array[] Data provider.
	 */
	public function data_banner_includes_low_res_version() {
		if ( ! is_dir( WP_ORG_ASSETS_DIR ) ) {
			// No assets directory, so no banners.
			return array();
		}

		$file_list = scandir( WP_ORG_ASSETS_DIR );

		if ( false === $file_list ) {
			// No files found, so no banners.
			return array();
		}

		// Filter out the files that do not begin with `banner-1544x500`.
		$banner_files = array_filter(
			$file_list,
			function ( $file ) {
				return str_starts_with( $file, 'banner-1544x500' );
			}
		);

		if ( empty( $banner_files ) ) {
			// No banners found.
			return array();
		}

		// Convert each file name to a data provider entry.
		$banner_data = array();
		foreach ( $banner_files as $banner_file ) {
			$banner_data[ $banner_file ] = array( $banner_file );
		}

		return $banner_data;
	}
}
