<?php
namespace OCA\MyWiki\Helper;

use OCP\Files\IRootFolder;

class WikiHelper {
    public static function isFolder(IRootFolder $storage, int $folderId) :bool {
        $nodes = $storage->getById($folderId);
        if ( count($nodes)>0 ) {
            return $nodes[0]->getType() == \OCP\Files\Node::TYPE_FOLDER;
        }
        return false;
    }
    public static function isWiki(IRootFolder $storage, int $folderId) :string {
        $nodes = $storage->getById($folderId);
        if ( count($nodes)>0 ) {
            $nodeStorage = $nodes[0]->getStorage();
            return $nodeStorage->file_get_contents('/wiki.yaml');
            // getPath()
            // getStorage()
        }
        return false;
    }
    public static function initWiki(int $folderId, string $title) :bool {
        // ToDo
        // create file ".wiki"
        // title: $title
        // pages: 
        return true;
    }
    public static function removePage(int $folderId, bool $includeSubfolders) {
        // ToDo :: Remove this folder and all subfolders
    }
}