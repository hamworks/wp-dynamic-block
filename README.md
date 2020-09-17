# wp-dynamic-block

## HAMWORKS\WP\Dynamic_Block\Dynamic_Block_Factory

### Dynamic_Block_Factory::__construct(  $file_or_folder, $args = array() );

`$file_or_folder`:  Path to the JSON file with metadata definition for the block or path to the folder where the `block.json` file is located.

`$args`: Optional. Array of block type arguments.


```php
$dynamic_block = new HAMWORKS\WP\Dynamic_Block\Dynamic_Block_Factory( __DIR__ );
```

## Template

```
wp-content/themes/your-theme/template-parts/blocks/block-namespace/blockname.php
```

## Template hierarchy

1. template-parts/blocks/block-namespace/blockname-blockstyle.php
2. template-parts/blocks/block-namespace/blockname.php
