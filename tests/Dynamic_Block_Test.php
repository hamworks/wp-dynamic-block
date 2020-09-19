<?php

namespace HAMWORKS\WP\Dynamic_Block\Tests;

use HAMWORKS\WP\Dynamic_Block\Dynamic_Block;

/**
 * Test for Dynamic_Block
 */
class Dynamic_Block_Test extends \WP_UnitTestCase {

	/**
	 * @var array
	 */
	private $metadata;

	/**
	 * Setup
	 */
	public function setUp(): void {
		parent::setUp();
		$this->metadata = json_decode( file_get_contents( __DIR__ . '/block/block.json' ), true ); //phpcs:ignore
	}

	/**
	 * Teardown
	 */
	public function tearDown(): void {
		parent::tearDown();

		if ( \WP_Block_Type_Registry::get_instance()->is_registered( $this->metadata['name'] ) ) {
			\WP_Block_Type_Registry::get_instance()->unregister( $this->metadata['name'] );
		}
	}


	/**
	 * Block registration test.
	 *
	 * @test
	 */
	public function test_registration() {
		$dynamic_block = new Dynamic_Block( __DIR__ . '/block' );
		$this->assertEquals( $dynamic_block->get_name(), $this->metadata['name'] );
	}

	/**
	 * @test
	 */
	public function test_render_template() {
		new Dynamic_Block( __DIR__ . '/block' );
		$content = '<!-- wp:' . $this->metadata['name'] . ' {"className":"your-class-name"} /-->';
		$this->assertEquals( '<div class="your-class-name"></div>', trim( do_blocks( $content ) ) );
		$content = '<!-- wp:' . $this->metadata['name'] . ' {"className":"your-class-name"} -->hello<!-- /wp:' . $this->metadata['name'] . ' -->';
		$this->assertEquals( '<div class="your-class-name">hello</div>', trim( do_blocks( $content ) ) );
	}
}
