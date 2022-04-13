<?php
 namespace OCA\MyWiki\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Controller;

 use OCA\MyWiki\Service\WikiPageService;

 class WikiPageController extends Controller {

     private $service;
     private $userId;

     use Errors;

    public function __construct(string $AppName, IRequest $request, WikiPageService $service, $UserId){
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
    }

    /**
      * @NoAdminRequired
      *
      * @param int $wikiId
      */
     public function index(int $wikiId) {
        return $this->handleNotFound(function () use ($wikiId) {
            return $this->service->findAll($wikiId, $this->userId);
        });
     }

     /**
      * @NoAdminRequired
      *
      * @param int $wikiId
      * @param int $id
      */
     public function show(int $wikiId, int $id) {
        return $this->handleNotFound(function () use ($wikiId, $id) {
            return $this->service->find($wikiId, $id, $this->userId);
        });
     }
    

    /**
      * @NoAdminRequired
      *
      * @param int $wikiId
      * @param int $parentFolderId
      * @param string $title
      * @param ?string $content
      */    
    public function create(int $wikiId, int $pid, string $title, ?string $content) {
        return $this->handleReadOnly(function () use ($wikiId, $pid, $title, $content) {
            return $this->service->create($wikiId, $pid, $title, $content, $this->userId);
        });     
    }

     /**
      * @NoAdminRequired
      *
      * @param int $wikiId
      * @param int $id
      * @param string $title
      * @param string $content
      */
     public function update(int $wikiId, int $id, ?string $title, ?string $content) {
        return $this->handleNotFound(function () use ($wikiId, $id, $title, $content) {
            return $this->service->update($wikiId, $id, $title, $content, $this->userId);
        });     
    }

     /**
      * @NoAdminRequired
      *
      * @param int $wikiId
      * @param int $id
      * @param bool $removeFiles
      */
     public function destroy(int $wikiId, int $id) {
        return $this->handleNotFound(function () use ($wikiId, $id) {
            return $this->service->delete($wikiId, $id, $this->userId);
        });
     }

 }