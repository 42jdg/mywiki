<?php
namespace OCA\MyWiki\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Wiki extends Entity implements JsonSerializable {

    protected $title;
    protected $fileId;
    protected $userId;

    public function __construct() {
        $this->addType('id','integer');
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_id' => $this->file_id
        ];
    }
}