<?php
/**
 * Test Plugin Versions match.
 *
 * @package WpPluginTemplate
 */

namespace PWCC\WpPluginTemplate\Tests;

use WP_UnitTestCase;

use const PWCC\WpPluginTemplate\PLUGIN_VERSION;

/**
 * Test Plugin Readme and PHP Headers
 */
class Test_Plugin_Versions extends WP_UnitTestCase {

	const PLUGIN_ROOT_DIR = __DIR__ . '/../..';

	/**
	 * Test Stable Tag in readme.txt matches plugin version.
	 */
	public function test_stable_tag_matches_plugin_version() {
		$readme_file = self::PLUGIN_ROOT_DIR . '/readme.txt';
		$readme_data = get_file_data(
			$readme_file,
			array(
				'Stable tag' => 'Stable tag',
			)
		);

		$this->assertSame( PLUGIN_VERSION, $readme_data['Stable tag'], 'The Stable tag in readme.txt does not match the plugin version.' );
	}

	/**
	 * Test version header in the plugin file matches plugin version constant.
	 */
	public function test_plugin_version_header() {
		// Get the plugin headers.
		// Plugin name.
		$plugin_file_name = basename( realpath( self::PLUGIN_ROOT_DIR ) ) . '.php';
		if ( ! file_exists( self::PLUGIN_ROOT_DIR . "/{$plugin_file_name}" ) ) {
			// Fallback to the generic plugin file name.
			$plugin_file_name = 'plugin.php';
		}

		$plugin_file_data = get_file_data(
			self::PLUGIN_ROOT_DIR . "/{$plugin_file_name}",
			array(
				'Version' => 'Version',
			)
		);

		$this->assertSame( PLUGIN_VERSION, $plugin_file_data['Version'], 'The Version header in the plugin file does not match the plugin version constant.' );
	}

	/**
	 * Test the plugin version in package.json matches the plugin version constant.
	 */
	public function test_package_json_version() {
		$package_file = self::PLUGIN_ROOT_DIR . '/package.json';
		if ( ! file_exists( $package_file ) ) {
			// Package file does not exist, consider this test passed.
			$this->assertTrue( true );
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fine for the tests.
		$package_data = json_decode( file_get_contents( $package_file ), true );
		$this->assertSame( PLUGIN_VERSION, $package_data['version'], 'The version in package.json does not match the plugin version constant.' );
	}

	/**
	 * Test the plugin version in package-lock.json matches the plugin version constant.
	 */
	public function test_package_lock_json_version() {
		$package_lock_file = self::PLUGIN_ROOT_DIR . '/package-lock.json';
		if ( ! file_exists( $package_lock_file ) ) {
			// Package lock file does not exist, consider this test passed.
			$this->assertTrue( true );
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fine for the tests.
		$package_lock_data = json_decode( file_get_contents( $package_lock_file ), true );
		$this->assertSame( PLUGIN_VERSION, $package_lock_data['version'], 'The version in package-lock.json does not match the plugin version constant.' );
		$this->assertSame( PLUGIN_VERSION, $package_lock_data['packages']['']['version'], "The packages['']['version'] in package-lock.json packages does not match the plugin version constant." );
	}

	/**
	 * Ensure that composer.json does not have a version key.
	 *
	 * Per the docs:
	 *
	 * > In most cases this is not required and should be omitted (see below).
	 * >
	 * > Packagist uses VCS repositories, so the statement above is very much true for Packagist
	 * > as well. Specifying the version yourself will most likely end up creating problems at
	 * > some point due to human error.
	 */
	public function test_composer_version_is_not_present() {
		$composer_file = self::PLUGIN_ROOT_DIR . '/composer.json';
		if ( ! file_exists( $composer_file ) ) {
			// Composer file does not exist, consider this test passed.
			$this->assertTrue( true );
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fine for the tests.
		$composer_data = json_decode( file_get_contents( $composer_file ), true );
		$this->assertArrayNotHasKey( 'version', $composer_data, 'The version key should not be present in composer.json.' );
	}
}
