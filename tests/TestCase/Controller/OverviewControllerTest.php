<?php

namespace App\Test\TestCase\Controller;

use App\Controller\OverviewController;
use Cake\ORM\TableRegistry;
use Tools\TestSuite\IntegrationTestCase;

/**
 * OverviewControllerTest
 */
class OverviewControllerTest extends IntegrationTestCase {

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();

		TableRegistry::clear();
	}

	/**
	 * Test index method
	 *
	 * @return void
	 */
	public function testIndex() {
		$this->get(array('controller' => 'Overview', 'action' => 'index'));

		$this->assertResponseCode(200);
		$this->assertNoRedirect();
	}

}
