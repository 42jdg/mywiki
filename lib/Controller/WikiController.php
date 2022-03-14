<?php
 namespace OCA\MyWiki\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Controller;

 use OCA\MyWiki\Service\WikiService;

 class WikiController extends Controller {

     private $service;
     private $userId;

     use Errors;

    public function __construct(string $AppName, IRequest $request, WikiService $service, $UserId){
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
    }

     /**
      * @NoAdminRequired
      */
      public function test() {
        $x = $this->service->test($this->userId);
        return new DataResponse(print_r($x,true));
      }

    /**
      * @NoAdminRequired
      */
     public function index() {
         return new DataResponse($this->service->findAll($this->userId));
     }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      */
     public function show(int $id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->find($id, $this->userId);
        });
     }

    

    /**
      * @NoAdminRequired
      *
      * @param string $title
      * @param string $fileId
      */
    public function create(string $title, int $fileId) {
        return $this->handleReadOnly(function () use ($title, $fileId) {
            return $this->service->create($title, $fileId, $this->userId);
        });     
    }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      * @param string $title
      */
     public function update(int $id, string $title) {
        return $this->handleNotFound(function () use ($id, $title) {
            return $this->service->update($id, $title, $this->userId);
        });     
    }

     /**
      * @NoAdminRequired
      *
      * @param int $id
      * @param bool $removeFiles
      */
     public function destroy(int $id, bool $removeFiles) {
        return $this->handleNotFound(function () use ($id, $removeFiles) {
            return $this->service->delete($id, $removeFiles, $this->userId);
        });
     }

 }