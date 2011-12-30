<?php
list($plugin, $name) = pluginSplit($mwbFile['name']); 
echo '<?php'; 
if(!function_exists('mwbBuildTableSchema')){
	function mwbBuildTableSchema($conf){
		$default = array(
			'type' => 'string',
			'length' => null,
			'default' => null,
			'null' => null,
			'primary' => false
		);
		$conf = array_merge($conf);
		$out = array();
		if(isset($conf['type'])){
			$out[] = "'type' => '{$conf['type']}'";
		}
		else{
			$out[] = "'type' => 'string'";
		}
		if(isset($conf['length'])){
			$out[] = "'length' => {$conf['length']}";
		}
		if(isset($conf['default'])){
			$out[] = "'default' => '{$conf['type']}'";
		}
		if(isset($conf['null'])){
			$null = $conf['null'] ? 'true' : 'false';
			$out[] = "'null' => $null";
		}
		if(isset($conf['primary'])){
			$out[] = "'primary' => 'key'";
		}
		return join(', ', $out);
	}
}
?> 

class <?php echo $name; ?>Schema extends CakeSchema {
<?php foreach($mwbFile['tables'] as $table => $columns): ?>
	
	var $<?php echo $table; ?> = array(
<?php foreach($columns as $column => $conf): ?>
		'<?php echo $column; ?>' => array(<?php echo mwbBuildTableSchema($conf); ?>),
<?php endforeach; ?>
	);
<?php endforeach; ?>
	
}