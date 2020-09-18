<?php
/**
 * Dynamic Block
 */

namespace HAMWORKS\WP\Dynamic_Block;

/**
 * Dynamic_Block class.
 **/
class Dynamic_Block {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Path to the folder where the `block.json` file is located.
	 *
	 * @var string
	 */
	private $dir;

	/**
	 * Additional arguments passed to the template.
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Block constructor.
	 *
	 * @param string $file_or_folder Path to the JSON file with metadata definition for
	 *                               the block or path to the folder where the `block.json` file is located.
	 * @param array  $args {
	 *        Optional. Array of block type arguments. Accepts any public property of `WP_Block_Type`.
	 *        Any arguments may be defined, however the ones described below are supported by default.
	 *        Default empty array.
	 *
	 * @type callable $render_callback Callback used to render blocks of this block type.
	 * }
	 */
	public function __construct( $file_or_folder, $args = array() ) {
		$filename      = 'block.json';
		$metadata_file = ( substr( $file_or_folder, - strlen( $filename ) ) !== $filename ) ?
			trailingslashit( $file_or_folder ) . $filename :
			$file_or_folder;
		if ( ! file_exists( $metadata_file ) ) {
			return false;
		}

		$metadata = json_decode( file_get_contents( $metadata_file ), true ); // phpcs:ignore
		if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
			return false;
		}

		$this->dir = dirname( $metadata_file );

		$this->name = $metadata['name'];
		register_block_type_from_metadata(
			$file_or_folder,
			array_merge(
				array(
					'render_callback' => array( $this, 'render' ),
				),
				$args
			)
		);
	}

	/**
	 * Block name getter.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Render callback
	 *
	 * @param array $attributes block attributes.
	 *
	 * @return false|string
	 */
	public function render( $attributes ) {
		return $this->get_content_from_template( $attributes );
	}

	/**
	 * Get html class names.
	 *
	 * @param array $attributes block attributes.
	 *
	 * @return array
	 */
	private function get_class_names( $attributes ): array {
		$class_names = array();
		if ( ! empty( $attributes['className'] ) ) {
			$class_names = explode( ' ', $attributes['className'] );
		}
		if ( ! empty( $attributes['align'] ) ) {
			$class_names[] = 'align' . $attributes['align'];
		}

		return $class_names;
	}

	/**
	 * Get template part directory.
	 *
	 * @return string
	 */
	private function get_template_parts_dir() {
		$template_part_dir = 'template-parts/blocks';
		$template_part_dir = apply_filters( "hw_dynamic_block_template_parts_dir_to_{$this->name}", $template_part_dir, $this );

		return trim( $template_part_dir, '/\\' );
	}

	/**
	 * Loads a template part into a template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $args Optional. Additional arguments passed to the template.
	 *                       Default empty array.
	 *
	 * @return string
	 */
	private function get_template_part( $slug, $name = null, $args = array() ) {
		ob_start();
		get_template_part( $slug, $name, $args );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Set template argument.
	 *
	 * @param string $key variable name.
	 * @param mixed  $value variable value.
	 */
	public function set_template_argument( $key, $value ) {
		$this->args[ $key ] = $value;
	}

	/**
	 * Get content from template.
	 *
	 * Examples:
	 *
	 *   1. template-parts/blocks/{namespace}/{name}-{style}.php
	 *   2. template-parts/blocks/{namespace}/{name}.php
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return string
	 */
	protected function get_content_from_template( $attributes ) {
		$class_name = join( ' ', $this->get_class_names( $attributes ) );
		$path       = array(
			$this->get_template_parts_dir(),
			$this->name,
		);

		$this->set_template_argument( 'class_name', $class_name );

		/**
		 * Fires after set template argument.
		 *
		 * @param array<string, mixed> $arguments  An dictionary of additional arguments.
		 * @param array<string, mixed> $attributes block attributes.
		 */
		$arguments                     = array();
		$additional_template_arguments = apply_filters( "hw_dynamic_block_template_arguments_to_{$this->name}", $arguments, $attributes, $this );
		foreach ( $additional_template_arguments as $key => $value ) {
			$this->set_template_argument( $key, $value );
		}

		$output = $this->get_template_part( join( '/', $path ), $this->get_style_name( $class_name ), $this->args );

		if ( $output ) {
			return $output;
		}

		$template_path = $this->dir . '/template.php';
		$template_path = apply_filters( "hw_dynamic_block_fallback_template_path_to_{$this->name}", $template_path, $this );

		if ( file_exists( $template_path ) ) {
			ob_start();
			load_template( $template_path, false, $this->args );
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

		return '';
	}

	/**
	 * Get component style name.
	 *
	 * @param string $class_name class strings.
	 *
	 * @return string
	 */
	protected function get_style_name( $class_name ) {
		$classes = explode( ' ', $class_name );
		$styles  = array_filter(
			$classes,
			function ( $class ) {
				return strpos( $class, 'is-style-' ) !== false;
			}
		);

		if ( ! empty( $styles ) && is_array( $styles ) ) {
			$style = reset( $styles );

			return str_replace( 'is-style-', '', $style );
		}

		return '';
	}
}
