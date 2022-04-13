<?php
namespace OCA\MyWiki\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Files\IRootFolder;
use OCA\MyWiki\Db\Wiki;
use OCA\MyWiki\Db\WikiMapper;
use OCA\MyWiki\Helper\WikiHelper;

class WikiService {
 
    private $mapper;
    private $userId;
    private $wikiHelper;

    public function __construct(WikiMapper $mapper, IRootFolder $storage, $UserId) {
        $this->mapper = $mapper;
        $this->userId = $UserId;
		
        $userFolder = $storage->getUserFolder($this->userId);
        $this->wikiHelper = new WikiHelper($userFolder);
    }

    public function test() {
        $folderId = 880;
        return  $this->wikiHelper->setFolderId($folderId)->reloadWikiTree();
        return  $this->wikiHelper->setFolderId($folderId)->rename(707, 'UnoMasUno');
        return  $this->wikiHelper->setFolderId($folderId)->rename(647, 'RenameTest3');

        return  $this->wikiHelper->setFolderId(395)->delete(395) ? 'Yes' : 'No';
        $this->wikiHelper->setFolderId($folderId)->initWiki("First Wiki");
        return print_r($this->wikiHelper->setFolderId($folderId)->getWikiTree(), true);
        return $this->wikiHelper->setFolderId($folderId)->isWiki() ? 'Yes' : 'No';
    }

    public function findAll(string $userId) {
        return $this->mapper->findAll($userId);
    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new NotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find(int $id, string $userId) {
        try {
            return $this->mapper->find($id, $userId);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create(string $folderPath, string $title, string $userId) {
        $folderId = $this->wikiHelper->create($folderPath, $title);
        if ( $folderId === null ) {
            throw new ReadOnlyException('Error creating wiki');
        }

        $wikis = $this->mapper->findAll($userId, ['fileId'=>$folderId]);
        if ( count($wikis)>0 ) {
            return $wikis[0];
        }

        $wiki = new Wiki();
        $wiki->setTitle($title);
        $wiki->setFileId($folderId);
        $wiki->setUserId($userId);
        return $this->mapper->insert($wiki);
    }

    public function update(int $id, string $title, string $userId) {
        try {
            $wiki = $this->mapper->find($id, $userId);
            $wiki->setTitle($title);
            $res = $this->mapper->update($wiki);
            return $res;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete(int $id, bool $removeFiles, string $userId) {
        try {
            $wiki = $this->mapper->find($id, $userId);
            if ($removeFiles) {
                $fileId = $wiki->getFileId();
                $this->mapper->usersCount($fileId);
                $this->wikiHelper->setFolderId($fileId)->delete();
            }
            $this->mapper->delete($wiki);
            return $wiki;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}