<?php
/**
 * @var array{class_name: string, content: string, wp_block: WP_Block} $args;
 */
?>
<div class="<?php echo esc_attr( $args['class_name'] ); ?>"><?php echo esc_html( $args['content'] ); ?></div>
