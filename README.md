# wp-dynamic-block

Library for Custom Dynamic Block.

## HAMWORKS\WP\Dynamic_Block\Dynamic_Block

### Dynamic_Block::__construct(  $file_or_folder, $args = array() );

`$file_or_folder`:  Path to the JSON file with metadata definition for the block or path to the folder where the `block.json` file is located.

`$args`: Optional. Array of block type arguments.


```php
$dynamic_block = new HAMWORKS\WP\Dynamic_Block\Dynamic_Block( __DIR__ );
```

## Template

```
wp-content/themes/your-theme/template-parts/blocks/block-namespace/blockname.php
```

## Template hierarchy

1. template-parts/blocks/block-namespace/blockname-blockstyle.php
2. template-parts/blocks/block-namespace/blockname.php


## How to adding template arguments.

```php
use HAMWORKS\WP\Dynamic_Block\Dynamic_Block;
add_filter( "hw_dynamic_block_template_arguments_to_{$block_name_in_block_json}", function ( array $arguments, array $attributes, Dynamic_Block $block_instance ) {
	$arguments['foo'] = 'bar';
	return $arguments;
}, 10, 3 );
```

## Example

see [https://github.com/team-hamworks/terms-block](https://github.com/team-hamworks/terms-block)
