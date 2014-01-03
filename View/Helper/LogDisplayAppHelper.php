<?php
class LogDisplayAppHelper extends AppHelper {
	public function beforeRender($viewFile) {
		$this->Html->css('LogDisplay.style', null, array('inline' => false));
		$this->Html->script('LogDisplay.script', array('inline' => false));
		return parent::beforeRender($viewFile);
	}
}