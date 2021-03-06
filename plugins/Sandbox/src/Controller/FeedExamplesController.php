<?php
namespace Sandbox\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Text;

class FeedExamplesController extends SandboxAppController {

	public $components = [
		'RequestHandler' => [
			'viewClassMap' => [
				'rss' => 'Feed.Rss']]];

	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

		$this->Auth->allow();
	}

	/**
	 * List of all examples.
	 *
	 * @return void
	 */
	public function index() {
	}

	/**
	 * Main RSS example.
	 *
	 * @return void
	 */
	public function feed() {
		if (empty($this->request->params['_ext']) || $this->request->params['_ext'] !== 'rss') {
			throw new NotFoundException();
		}
		// This is only needed without the viewClassMap setting for RequestHandler
		//$this->viewClass = 'Feed.Rss';

		$news = $this->_feedData();

		$items = [];
		foreach ($news as $key => $val) {
			$content = nl2br(h($val['content']));
			$link = ['action' => 'feedview', $val['id']];
			$guidLink = ['action' => 'view', $val['id']];

			$items[] = [
				'title' => $val['title'],
				'link' => $link,
				'guid' => ['url' => $guidLink, '@isPermaLink' => 'true'],
				'description' => Text::truncate($val['content']),
				'dc:creator' => $val['User']['username'],
				'pubDate' => $val['published'],
				'content:encoded' => $content
			];
		}

		$atomLink = ['action' => 'feed', 'ext' => 'rss'];

		$channel = [
			'title' => __('News/Updates') . '', 'link' => '/',
			'atom:link' => ['@href' => $atomLink],
			'description' => __('Most recent news articles'),
			'language' => 'en-en',
			'image' => [
				'url' => '/img/statics/logo_rss.png',
				'link' => '/'
			]
		];

		$data = [
			'document' => [
			],
			'channel' => $channel,
			'items' => $items
		];
		$this->set(['channel' => $data, '_serialize' => 'channel']);
	}

	protected function _feedData() {
		$records = [
			[
				'id' => 1,
				'title' => 'Foo',
				'content' => '<b>Bold text</b>',
				'published' => '2012-01-04 11:12:13',
			],
			[
				'id' => 2,
				'title' => 'Bar',
				'content' => '<i>Italic text</b>',
				'published' => '2012-07-04 11:12:13',
			],
		];
		$res = [];
		foreach ($records as $k => $v) {
			$v['User'] = [
				'username' => 'Some user'
			];
			$res[] = $v;
		}
		return $res;
	}

	/**
	 * RssExamplesController::feedview()
	 *
	 * @param string $id
	 * @return void
	 */
	public function feedview($id = null) {
		if (!$id) {
			throw new NotFoundException();
		}
		$this->response->body('Example of web frontend for ' . $id);
		$this->autoRender = false;
	}

}
