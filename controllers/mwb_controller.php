<?php

class MwbController extends MwbAppController {
	
	var $uses = array(
		'Mwb.MwbFile'
	);
	
	function admin_index(){
		$this->set('mwbFiles', $this->MwbFile->findList());
	}
	
	function admin_view($name = null){
		if($name == null){
			$this->Session->setFlash(__d('mwb', 'Invalid MWB file', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('mwbFile', $this->MwbFile->read($name));
	}
	
	function admin_generate($name = null){
		if($name == null){
			$this->Session->setFlash(__d('mwb', 'Invalid MWB file', true));
			$this->redirect(array('action' => 'index'));
		}
		$mwbFile = $this->MwbFile->read($name);
		$this->set('mwbFile', $mwbFile);
		$this->autoRender = false;
		$output = $this->render(null, false);
		if(file_put_contents($mwbFile['schema_path'], $output)){
			touch($mwbFile['schema_path'], filemtime($mwbFile['path']));
			$this->Session->setFlash(__d('mwb', 'Schema created', true));
			$this->redirect(array('action' => 'index'));
		}
		else{
			$this->Session->setFlash(__d('mwb', 'Unable to create schema', true));
			$this->redirect(array('action' => 'index'));
		}
	}
	
}