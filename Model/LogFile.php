<?php
class LogFile extends LogDisplayAppModel {
	public $name = 'LogFile';
	public $useTable = false;
	
	private $logDir = null;
	
	const MAX_MEMORY = '1G';
	
	public function __construct($id = false, $table = null, $ds = null) {
		if (empty($this->logDir)) {
			$this->logDir = APP . 'tmp' . DS . 'logs' . DS;
		}
		parent::__construct($id, $table, $ds);
	}
	
	public function findByName($name) {
		return $this->find('first', compact('name'));
	}
	
	public function getLineCount($name) {
		$content = $this->getContent($name);
		return count($content);
	}
	
	public function getLines($name) {
		ini_set('memory_limit', self::MAX_MEMORY);
		$path = $this->logDir . $name;
	//	$content = file_get_contents($path);
	//	return explode("\n", $content);
		return file($path);
	}
	
	public function getContent($name, $params = array()) {
		$params = array_merge(array(
			'limit' => 0,
		), $params);
		extract($params);
		
		if (!is_array($limit)) {
			$limit = array(0, $limit);
		}
		list($offset, $length) = $limit;
		
		$lines = $this->getLines($name);
		//krsort($lines);			//Reads from bottom so newest goes on top
		$lines = array_reverse($lines);
		
		$result = array();
		$key = count($lines);	//The current index key in our result array
		
		$lineCount = 0;			//How many lines into the log file we are
		$resultCount = 0;		//How many lines we've stored in our result so far
		
		foreach ($lines as $line) {
			if (preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}) (.*)/', $line, $matches)) {
				if ($lineCount++ < $offset) {
					unset($result[$key]);
					continue;
				}
				
				$result[$key]['date'] = $matches[1];
				$result[$key]['text'] = $matches[2];
				$resultCount = count($result);
				
				if (!empty($length) && $resultCount >= $length) {
					break;
				}
				$key--;
			} else if (preg_match('/^Request URL: (.*)/', $line, $matches)) {
				$result[$key]['url'] = $matches[1];
			} else if (preg_match('/^#([0-9]+) (.*)/', $line, $matches)) {
				$result[$key]['stack'][$matches[1]] = $matches[2];
			}
		}
		return $result;
	}
	
	public function find($findType = 'first', $params = array()) {
		$params = array_merge(array(
			'type' => null,
			'name' => null,
			'dir' => $this->logDir,
		), $params);
		extract($params);

		if (substr($dir, -1) != DS) {
			$dir .= DS;
		}
		
		$fh = opendir($dir);
		$result = array();
		$types = array();
		
		$modifiedOrder = array();
		
		$key = 0;
		while (($file = readdir($fh)) !== false) {
			if ($file == '.' || $file == '..' || $file == 'empty') {
				continue;
			}
			$logType = preg_match('/^([^\.]+)\.log/', $file, $matches) ? $matches[1] : null;
			if (!empty($type) && $type != $logType) {
				continue;
			}
			if (!empty($name) && $name != $file) {
				continue;
			}
			
			$path = $dir . $file;
			$modified =  filemtime($path);
			
			$row = array(
				'name' => $file, 
				'filesize' => filesize($path),
				'type' => $logType,
				'created' => filectime($path),
			) + compact('path', 'modified');
			
			if (empty($types[$logType])) {
				$types[$logType] = array(
					'type' => $logType,
					'count' => 1,
					'modified' => $modified,
				);
			} else {
				$types[$logType]['count']++;
				if ($modified > $types[$logType]['modified']) {
					$types[$logType]['modified'] = $modified;
				}
			}				
			if ($findType == 'first') {
				return $row;
			} else {
				$modifiedOrder[$key] = $modified;
				$result[$key++] = $row;
			}
		}
		closedir($fh);
		
		if ($findType == 'types') {
			return array_values($types);
		} else {
			//Makes sure they're returned base on last modified
			arsort($modifiedOrder);
			$modifiedOrder = array_keys($modifiedOrder);
			
			$orderedResult = array();
			foreach($modifiedOrder as $key) {
				$orderedResult[] = $result[$key];
			}
			return $orderedResult;
		}
	}	
}