<?php

App::import('Core', 'Folder');
App::import('Core', 'Xml');

class MwbFile extends AppModel {
	
	var $useTable = false;
	
	public function findList(){
		$files = $this->__scanFolder();
		$plugins = App::objects('plugin');
		foreach($plugins as $plugin){
			$_files = $this->__scanFolder($plugin);
			foreach($_files as $name => $path){
				$files[$name] = $path;
			}
		}
		return $files;
	}
	
	private function __scanFolder($plugin = null){
		$schemaPath = 'config'.DS.'schema';
		$path = APP.$schemaPath;
		if($plugin){
			$path = App::pluginPath($plugin).$schemaPath;
		}
		$f = new Folder($path);
		$files = $f->find('.+\.mwb$');
		$_files = array();
		foreach($files as $k => $v){
			$name = current(explode('.', $v));
			if(preg_match('/^[a-z_]+$/', $name)){
				$name = Inflector::camelize($name);
				$name = $plugin ? $plugin.'.'.$name : $name;
				$_files[$name] = $path.DS.$v;
			}
			else{
				trigger_error(sprintf(__d('mwb', '"%s" is not a valid MWB file name', true), $path.DS.$v), E_USER_WARNING);
			}
		}
		return $_files;
	}
	
	public function read($name){
		list($plugin, $name) = pluginSplit($name);
		$underscored = Inflector::underscore($name);
		$schemaPath = 'config'.DS.'schema'.DS.$underscored.'.mwb';
		$path = APP.$schemaPath;
		if($plugin){
			$path = App::pluginPath($plugin).$schemaPath;
		}
		$data = array(
			'name' => $name,
			'path' => $path,
			'schema_path' => rtrim(dirname($path), '/\\').DS.$underscored.'.php',
			'status' => -1,
			'tables' => array()
		);
		if(file_exists($data['schema_path'])){
			$data['status'] = 0;
			if( filemtime($data['path']) > filemtime($data['schema_path']) ){
				$data['status'] = 1;
			}
		}
		
		
		if(!file_exists($path)){
			trigger_error(sprintf(__d('mwb', 'MWB file "%s" does not exists', true), $path), E_USER_WARNING);
		}
		else{
			$zip = new ZipArchive;
			if ($zip->open($path) !== TRUE) {
				trigger_error(sprintf(__d('mwb', 'Unable to open "%s"', true), $path), E_USER_WARNING);
			}
			else {
				$content = $zip->getFromName('document.mwb.xml');
				$zip->close();
				if(!$content){
					trigger_error(sprintf(__d('mwb', 'Unable to extract xml from archive "%s"', true), $path), E_USER_WARNING);
				}
				else {
					$xml = new Xml($content);
					$xmlData = $xml->toArray();
					$xmlData = $this->__cleanupValue($xmlData['Data']['Value']);
					$typeMap = array(
						'int' => 'integer',
						'varchar' => 'string',
						'text' => 'text',
						'binary' => 'binary',
						'float' => 'float',
						'tinyint' => 'bool',
						'datetime' => 'datetime',
						'time' => 'time',
						'date' => 'date'
					);
					foreach($xmlData['physicalModels']['catalog']['schemata']['tables'] as $table){
						$columns = array();
						foreach($table['columns'] as $column){
							$_column = array();
							$_column['null'] = $column['isNotNull'] ? false : true;
							$_column['type'] = 'string';
							if(isset($column['simpleType']['value'])){
								$_column['type'] = $typeMap[end(explode('.', $column['simpleType']['value']))];
							}
							if(isset($column['length']) && $column['length'] != -1){
								$_column['length'] = $column['length'];
							}
							if(isset($column['defaultValue'])){
								$_column['default'] = $column['defaultValue'];
							}
							if(isset($column['comment'])){
								$_column['comment'] = $column['comment'];
							}
							if($column['name'] == 'id'){
								$_column['primary'] = true;
							}
							$columns[$column['name']] = $_column;
						}
						$data['tables'][$table['name']] = $columns;
					}
				}
			}
		}
		return $data;
	}
	
	private function __cleanupValue($value){
		if(is_array($value) && isset($value['type'])){
			switch($value['type']){
				case 'object':
				case 'list':
					if(isset($value['Value'])){
						if(isset($value['Value'][0])){
							if(isset($value['Link'])){
								if(isset($value['Link'][0])){
									foreach($value['Link'] as $link){
										$value['Value'][] = $link;
									}
								}
								else{
									$value['Value'][] = $value['Link'];
								}
							}
							$keysToUnset = array();
							foreach($value['Value'] as $k => $_value){
								if(isset($_value['key'])){
									$keysToUnset[] = $k;
									$k = $_value['key'];
									unset($_value['key']);
								}
								$value['Value'][$k] = $this->__cleanupValue($_value);
							}
							foreach($keysToUnset as $k){
								unset($value['Value'][$k]);
							}
						}
						else{
							$value['Value'] = $this->__cleanupValue($value['Value']);
						}
						return $value['Value'];
					}
					return $value;
				case 'dict':
				case 'string':
				case 'real':
					if(isset($value['value'])){
						return $this->__cleanupValue($value['value']);
					}
					if(isset($value['Value'])){
						return $this->__cleanupValue($value['Value']);
					}
					return null;
				case 'int':
					if(isset($value['value'])){
						return (int) $this->__cleanupValue($value['value']);
					}
					if(isset($value['Value'])){
						return (int) $this->__cleanupValue($value['Value']);
					}
					return null;
				break;
				default:
					trigger_error(sprintf(__d('mwb', 'Undefined data type "%s"', true), $value['type']), E_USER_WARNING);
				break;
			}
		}
		return $value;
	}
}