<?php
namespace Sandbox\Controller;

use MiniAsset\Filter\ScssFilter;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;

class AssetCompressExamplesController extends SandboxAppController {

	public function beforeFilter(Event $event) {
		$this->Auth->allow();

		$this->_cssDir = Plugin::path('Sandbox') . 'files' . DS . 'AssetCompress' . DS;

		parent::beforeFilter($event);
	}

	public function index() {
		$actions = $this->_getActions($this);

		$this->set(compact('actions'));
	}

	public function sass() {
		if (!file_exists($this->_cssDir . 'test.scss')) {
			throw new \Exception('Cannot find scss test file.');
		}

		$this->filter = new ScssFilter();
		$settings = (array)Configure::read('Sass');
		$this->filter->settings($settings);

		$source = file_get_contents($this->_cssDir . 'test.scss');

		try {
			$result = $this->filter->input($this->_cssDir . 'test.scss', $source);
		} catch (\RuntimeException $e) {
			$this->Flash->error('SASS Parsing error: ' . $e->getMessage());
			$result = [];
		}
		$expected = file_get_contents($this->_cssDir . 'compiled_scss.css');
		if (!$result) {
			$result  = $expected;
			$expected = null;
		}

		$result = trim(str_replace("\r\n", "\n", $result));
		$expected = trim(str_replace("\r\n", "\n", $expected));
		if ($expected && $expected !== $result) {
			$this->Flash->warning('Actual result is not quite the expected one.');
		}

		$this->set(compact('source', 'result', 'expected'));
	}

}
