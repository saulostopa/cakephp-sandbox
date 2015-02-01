<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Tools\Controller\Controller;

/**
 * Application Controller
 */
class AppController extends Controller {

	public $components = array('Shim.Session', 'RequestHandler', 'Tools.Common',
		'Tools.Flash', 'Auth', 'Tools.AuthUser');

	public $helpers = array('Session', 'Html',
		'Tools.Form', 'Tools.Common', 'Tools.Flash', 'Tools.Format',
		'Tools.Time', 'Tools.Number', 'Tools.AuthUser');

	/**
	 * AppController::constructClasses()
	 *
	 * @return void
	 */
	public function initialize() {
		parent::initialize();
	}

	/**
	 * AppController::beforeFilter()
	 *
	 * @return void
	 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		$this->Auth->authenticate = array(
			'Authenticate.MultiColumn' => array(
				'passwordHasher' => Configure::read('Passwordable.authType'),
				'fields' => array(
					'username' => 'login',
					'password' => 'password'
				),
				'columns' => array('username', 'email'),
				'userModel' => 'User',
			)
		);
		$this->Auth->authorize = array(
			'Tools.Tiny' => array()
		);
		$this->Auth->logoutRedirect = array(
			'plugin' => false,
			'admin' => false,
			'controller' => 'overview',
			'action' => 'index');
		$this->Auth->loginRedirect = array(
			'plugin' => false,
			'admin' => false,
			'controller' => 'account',
			'action' => 'index');
		$this->Auth->loginAction = array(
			'plugin' => false,
			'admin' => false,
			'controller' => 'account',
			'action' => 'login');

		// Short-cicuit Auth for some controllers
		if (in_array($this->viewPath, array('Pages'))) {
			$this->Auth->allow();
		}

		// Make sure you can't access login etc when already logged in
		$allowed = array('Account' => array('login', 'lost_password', 'register'));
		if (!$this->AuthUser->id()) {
			return;
		}
		foreach ($allowed as $controller => $actions) {
			if ($this->name === $controller && in_array($this->request->action, $actions)) {
				$this->Flash->message('The page you tried to access is not relevant if you are already logged in. Redirected to main page.', 'info');
				return $this->redirect($this->Auth->loginRedirect);
			}
		}
	}

	/**
	 * AppController::beforeRender()
	 *
	 * @return void
	 */
	public function beforeRender(Event $event) {
		/*
		if ($this->request->is('ajax') && $this->layout === 'default') {
			$this->layout = 'ajax';
		}
		*/

		// default title
		/*
		if (empty($this->pageTitle)) {
			$this->pageTitle = __(Inflector::humanize($this->request->action)) . ' | ' . __($this->name);
		}

		$this->set('title_for_layout', $this->pageTitle);
		*/

		$this->disableCache();

		parent::beforeRender($event);
	}

}
