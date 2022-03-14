<?php
namespace OCA\MyWiki\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\MyWiki\Db\Wiki;
use OCA\MyWiki\Db\WikiMapper;
use OCA\MyWiki\Helper\WikiHelper;

use \OCP\Files\Storage;
use \OCP\Files\IRootFolder;

use \OCP\IUserSession;

class WikiService {
 
    private $mapper;
    private $storage;
    private $userSession;

    public function __construct(WikiMapper $mapper, IRootFolder $storage) {
        // , IUserSession $userSession ) {
        $this->mapper = $mapper;
		// $this->userSession = $userSession;
		$this->storage = $storage;

        // , IUserSession $userSession
    }

    public function test(string $userId) {
        return WikiHelper::isWiki($this->storage, 208);
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

        // in order to be able to plug in different storage backends like files
        // for instance it is a good idea to turn storage related exceptions
        // into service related exceptions so controllers and service users
        // have to deal with only one type of exception
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create(string $title, int $fileId, string $userId) {
        if ( !WikiHelper::isFolder($fileId) ) {
            throw new ReadOnlyException('The folder is not valid');
        }
        if ( !WikiHelper::isWiki($fileId) ) {
            if ( !WikiHelper::initWiki($fileId, $title) ) {
                throw new ReadOnlyException('Error creating wiki');
            } 
        }

        $wiki = new Wiki();
        $wiki->setTitle($title);
        $wiki->setFileId($fileId);
        $wiki->setUserId($userId);
        return $this->mapper->insert($wiki);
    }

    public function update(int $id, string $title, string $userId) {
        try {
            $wiki = $this->mapper->find($id, $userId);
            $wiki->setTitle($title);
            return $this->mapper->update($wiki);
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
                WikiHelper::removePage($fileId, true);
            }
            $this->mapper->delete($wiki);
            return $wiki;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}