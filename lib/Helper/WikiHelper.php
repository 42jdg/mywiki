<?php
namespace OCA\MyWiki\Helper;

use OCP\Files\Folder;
use OC\Files\Node\File; 
/*
use OCA\MyWiki\Helper\WikiTree;
use OCA\MyWiki\Helper\WikiTreePage;
*/

class WikiHelper {
    private const WIKI_FILE = 'wiki.json';
    private const WIKI_FILE_CONTENT = 'Readme.md';
    private Folder $userFolder;
    private ?Folder $wikiFolder;

    private function getWikiFolder(Folder $folder, int $folderId): ?Folder {
        $nodes = $folder->getById($folderId); 
        return count($nodes)>0?$nodes[0]:null;
    }
    private function getFolderById(int $id): Folder {
        return $this->wikiFolder->getById($id)[0];
    }
    private function getFileById(int $id): File {
        return $this->wikiFolder->getById($id)[0];
    }
    private function getFileByName(string $name): File {
        return $this->wikiFolder->get($name);
    }
    private function sanitize_file_name(string $nameFile): string {
        return preg_replace("([^\w\s\d\-_~,;\[\]\(\)])", "", $nameFile);
    }
    private function scanFolder(Folder $folder, WikiTree $wikiTree, int $parentId=0) {
        $nodes = $folder->getDirectoryListing();
        foreach($nodes as $node) {
            if ($node->getType() == \OCP\Files\Node::TYPE_FOLDER) {
                $wikiPage = new WikiTreePage();
                $wikiPage->id = $node->getId();
                $wikiPage->pid = $parentId;
                $wikiPage->title = $node->getName();
                $wikiTree->set($wikiPage);
                $this->scanFolder($node, $wikiTree, $wikiPage->id);
            }
        }
    }
    private function rebuildWikiTree(): array {
        $wikiTree = new WikiTree(null);
        $this->scanFolder($this->userFolder, $wikiTree);
        return $wikiTree->getWikiPages();
    }


    public function __construct(Folder $folder) {
        $this->userFolder = $folder;
        $this->wikiFolder = null;
    }

    public function setFolderId(int $folderId): WikiHelper {
        $this->wikiFolder = $this->getWikiFolder($this->userFolder, $folderId); 
        return $this;
    }

    private function isWikiable() :bool {
        return $this->wikiFolder && $this->wikiFolder->getType() == \OCP\Files\Node::TYPE_FOLDER;
    }


    public function getWikiData(): ?array {
        try {
            $data = $this->getFileByName(self::WIKI_FILE)->getContent();
        } catch(\OCP\Files\NotFoundException $ex) {
            return null;
        }
        return json_decode($data, true);
    }

    public function setWikiData(array $wiki): bool {
        try {
            $data = json_encode($wiki);
            $path = $this->wikiFolder->getInternalPath().'/'.self::WIKI_FILE;
            if ( $this->wikiFolder->nodeExists($path) ) {
                $this->getFileByName(self::WIKI_FILE)->putContent($data);
            } else {
                $this->wikiFolder
                    ->newFile(self::WIKI_FILE, $data);
            }
        } catch(\Exception $ex) {
            return false;
        }
        return true;
    }

    public function createWikiData(): array {
        $wiki = [
            "title"=>$this->wikiFolder->getName(),
            "folderId"=>$this->wikiFolder->getId(),
            "pages"=>[]
        ];
        $wiki['pages'] = $this->rebuildWikiTree();
        return $wiki;
    }

    public function rebuildWiki($title): ?array {
        $wiki = $this->createWikiData(true);
        $wiki['title'] = $title;
        return $this->setWikiData($wiki) ? $wiki : null;
    }

    public function create(string $folderPath, string $title) :?int {
        $this->wikiFolder = $this->userFolder->get($folderPath);
        if ( !$this->isWikiable() ) {
            return null;
        }
        if ( $this->getWikiData() === null ) {
            if ( $this->rebuildWiki($title) === null ) {
                return null;
            }
        }
        return $this->wikiFolder->getId();
    }

    public function add(int $parentId, string $title, ?string $content) :int {
        if ($parentId>0) {
            $parentFolder = $this->getFolderById($parentId);
        } else {
            $parentFolder = $this->wikiFolder;
        }
        try {
            $folder = $parentFolder->newFolder($this->sanitize_file_name($title));

            $wikiTreePage = new WikiTreePage();
            $wikiTreePage->id = $folder->getId();
            $wikiTreePage->pid = $parentId;
            $wikiTreePage->title = $title;
            $wikiTreePage->sort = 0;                

            if ( !is_null($content) ) {
                $this->update($wikiTreePage->id, $content);
            }

            $wikiData = $this->getWikiData();
            if ($wikiData) {
                $wikiTree = new WikiTree($wikiData['pages']);
                $wikiTree->set($wikiTreePage);
                $wikiData['pages'] = $wikiTree->getWikiPages();
                $this->setWikiData($wikiData);
            }
        } catch(\Exception $ex) {
            return -1;
        }
        return $wikiTreePage->id; 
    }

    public function update(int $id, string $content) {
        try {
            $path = $this->wikiFolder->getInternalPath().'/'.self::WIKI_FILE_CONTENT;
            if ( $this->wikiFolder->nodeExists($path) ) {
                $this->getFileByName(self::WIKI_FILE_CONTENT)->putContent($content);
            } else {
                $this->wikiFolder
                    ->newFile(self::WIKI_FILE_CONTENT, $content);
            }
        } catch(\Exception $ex) {
            return false;
        }
        return true;
    }
    
    public function rename(int $id, string $title) {
        try {
            $folder = $this->getFolderById($id);
            $to = $folder->getParent()->getFullPath($this->sanitize_file_name($title));
            try {
                if ( !$folder->move($to) ) return false;

                $wikiData = $this->getWikiData();
                if ($wikiData) {
                    $wikiTree = new WikiTree($wikiData['pages']);
                    $wikiPage = $wikiTree->get($id);
                    if ($wikiPage) {
                        $wikiPage->title = $title;
                        $wikiTree->set($wikiPage);
                        $wikiData['pages'] = $wikiTree->getWikiPages();
                        $this->setWikiData($wikiData);
                    }
                }

            } catch(\OCP\Lock\LockedException $ex) {
                return false;
            }
        } catch(\Exception $ex) {
            return false;
        }
        return true;        
    }
    
    public function delete(int $id=null) {
        if ($id!==null) {
            $folder = $this->getFolderById($id);

            $wikiData = $this->getWikiData();
            if ($wikiData) {
                $wikiTree = new WikiTree($wikiData['pages']);
                $wikiTree->del($id);
                $wikiData['pages'] = $wikiTree->getWikiPages();
                $this->setWikiData($wikiData);
            }

        } else {
            $folder = $this->wikiFolder; 
        }
        try {
            $folder->delete();
        } catch(\Exception $ex) {
            return false;
        }
        return true;
    }
    
}