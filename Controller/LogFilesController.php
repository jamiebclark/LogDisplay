<?php
App::uses('LogDisplayAppController', 'LogDisplay.Controller');
class LogFilesController extends LogDisplayAppController {
	public $name = 'LogFiles';
	public $helpers = array('Number');
	
	public function admin_index() {
		$logFileTypes = $this->LogFile->find('types');
		$this->set(compact('logFileTypes'));
	}
	
	public function admin_view($logName = null) {
		$logFile = $this->LogFile->findByName($logName);
		$this->_paginateContent($logName);
		
		if (empty($logFile)) {
			$this->redirect(array('action' => 'index'));
		}
		
		$typeName = $logFile['type'];
		$logFiles = $this->LogFile->find('all', array('type' => $typeName));
		$this->set(compact('typeName', 'logFiles', 'logFile'));
	}
	
	public function admin_type($typeName = null) {
		$logFiles = $this->LogFile->find('all', array('type' => $typeName));
		if (empty($logFiles)) {
			$this->redirect(array('action' => 'index'));
		}
		if (empty($logName)) {
			$logName = $logFiles[0]['name'];
		}
		$logFile = $this->LogFile->findByName($logName);
		$this->_paginateContent($logName);

		$this->set(compact('typeName', 'logFiles', 'logFile'));
		$this->render('admin_view');	
	}
	
	private function _paginateContent($logName, $params = array()) {
		$perPage = 100;
		if (!($lineCount = $this->LogFile->getLineCount($logName))) {
			return false;
		}
		
		$currentPage = !empty($this->request->named['page']) ? $this->request->named['page'] : 0;
		$totalPages = ceil($lineCount / $perPage);
				
		if ($currentPage > $totalPages) {
			$currentPage = 0;
		}
		
		$params['limit'] = array($currentPage * $perPage, $perPage);
		$logFileContent = $this->LogFile->getContent($logName, $params);

		$this->set(compact('logFileContent', 'totalPages', 'currentPage', 'perPage', 'lineCount'));
		return $logFileContent;
	}
}