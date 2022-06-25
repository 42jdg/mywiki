<?php
namespace OCA\MyWiki\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Files\IRootFolder;
use OCA\MyWiki\Db\Wiki;
use OCA\MyWiki\Db\WikiMapper;
use OCA\MyWiki\Helper\WikiHelper;

class WikiPageService {
 
    private $mapper;
    private $userId;
    private $wikiHelper;

    public function __construct(WikiMapper $mapper, IRootFolder $storage, $UserId) {
        $this->mapper = $mapper;
        $this->userId = $UserId;
		
        $userFolder = $storage->getUserFolder($this->userId);
        $this->wikiHelper = new WikiHelper($userFolder);
    }

    public function findAll(int $wikiId, string $userId) {
        try {
            $wiki = $this->mapper->find($wikiId, $userId);
            return $this->wikiHelper->setFolderId($wiki->getFileId())->getWikiData();
        } catch(Exception $e) {
            $this->handleException($e);
        }        
    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new NotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find(int $wikiId, int $id, string $userId) {
        try {
            $wiki = $this->mapper->find($wikiId, $userId);
            $wikiPageContent = $this->wikiHelper
                                    ->setFolderId($wiki->getFileId())
                                    ->getWikiPageContent($id);
        } catch(Exception $e) {
            $this->handleException($e);
        }        
        return ['content'=>$wikiPageContent];
    }

    public function create(int $wikiId, int $parentFolderId, string $title, ?string $content, string $userId):array {
        try {
            $wiki = $this->mapper->find($wikiId, $userId);
            $pageId = $this->wikiHelper->setFolderId($wiki->getFileId())->add($parentFolderId,$title,$content);
            if ( $pageId <= 0 ) {
                throw new ReadOnlyException('Error renaming wiki page');
            }
        } catch(Exception $e) {
            $this->handleException($e);
        }
        return ["pageId"=>$pageId];
    }

    public function update(int $wikiId, int $id, ?string $title, ?string $content, string $userId) {
        try {
            $wiki = $this->mapper->find($wikiId, $userId);

            $this->wikiHelper->setFolderId($wiki->getFileId());
            if (!is_null($title)) {
                if ( !$this->wikiHelper->rename($id, $title) ) {
                    throw new ReadOnlyException('Error renaming wiki page');
                }
            }
            if (!is_null($content)) {
                if ( !$this->wikiHelper->update($id, $content) ) {
                    throw new ReadOnlyException('Error updating wiki content');
                }
            }
        } catch(Exception $e) {
            $this->handleException($e);
        }
        return true;
    }

    public function delete(int $wikiId, int $id, string $userId) {
        try {
            $wiki = $this->mapper->find($wikiId, $userId);
            if ( !$this->wikiHelper->setFolderId($wiki->getFileId())->delete($id) ) {
                throw new ReadOnlyException('Error deleting wiki page');
            }
        } catch(Exception $e) {
            $this->handleException($e);
        }
        return true;
    }

}