<?php
// sudo ./vendor/phpunit/phpunit/phpunit tests/Unit/Helper/WikiTreeHelperTest.php 

namespace OCA\MyWiki\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use OCA\MyWiki\Helper\WikiTree;
use OCA\MyWiki\Helper\WikiTreePage;

class WikiTreeHelperTest extends TestCase {
        public function testGetSetDel() {
                $wikiTree = new WikiTree(null);

                $wikiPage = new WikiTreePage();
                $wikiPage->id = 1;
                $wikiPage->pid = 0;
                $wikiPage->title = 'Page1';
                $wikiPage->sort = 0;
                $wikiTree->set($wikiPage);

                $wikiPage->id = 2;
                $wikiPage->pid = 0;
                $wikiPage->title = 'Page3';
                $wikiPage->sort = 0;
                $wikiTree->set($wikiPage);

                $wikiPage->title = 'Page2';
                $wikiTree->set($wikiPage);

                $wikiPage->id = 11;
                $wikiPage->pid = 1;
                $wikiPage->title = 'Page1.1';
                $wikiPage->sort = 0;
                $wikiTree->set($wikiPage);

                $pages = $wikiTree->getWikiPages();
                $this->assertEquals(3, count($pages));
                $this->assertEquals('Page2', $pages[1]['title']);

                $wikiTree->del(1);
                $pages = $wikiTree->getWikiPages();
                $this->assertEquals(1, count($pages));

                $page = $wikiTree->get(2);
                $this->assertEquals(2, $page->id);
                $this->assertEquals(0, $page->pid);
                $this->assertEquals('Page2', $page->title);
                $this->assertEquals(2, $page->sort);
	}
}
