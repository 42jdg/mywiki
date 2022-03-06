<?php
namespace OCA\MyWiki\Tests\Integration\Controller;

use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\App;
use Test\TestCase;

use OCA\MyWiki\Db\Wiki;

/**
 * @group DB
 */
class MyWikiIntegrationTest extends TestCase {

    private $controller;
    private $mapper;
    private $userId = 'john';

	protected function setUp(): void {
        parent::setUp();
        $app = new App('mywiki');
        $container = $app->getContainer();

        // only replace the user id
        $container->registerService('UserId', function($c) {
            return $this->userId;
        });

        $this->controller = $container->query(
            'OCA\MyWiki\Controller\WikiController'
        );

        $this->mapper = $container->query(
            'OCA\MyWiki\Db\WikiMapper'
        );
    }

    public function e($x) { echo "\n>>>$x<<<"; }

    public function testJDG() {

        $x = \OC\Files\Filesystem::getLocalFolder('\\');

        $this->e(print_r($x,true));
    }
/*
    public function testUpdate() {
        // create a new note that should be updated
        $wiki = new Wiki();
        $wiki->setTitle('old_title');
        $wiki->setFileId(4321);
        $wiki->setUserId($this->userId);

        $id = $this->mapper->insert($wiki)->getId();

        // fromRow does not set the fields as updated
        $updatedWiki = Wiki::fromRow([
            'id' => $id,
            'user_id' => $this->userId
        ]);
        $updatedWiki->setTitle('title');
        $updatedWiki->setFileId(1234);

        $result = $this->controller->update($id, 'title', 'file_id');

        $this->assertEquals($updatedWiki, $result->getData());

        // clean up
        $this->mapper->delete($result->getData());
    }
*/
}