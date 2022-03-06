<?php
namespace OCA\MyWiki\Helper;

use OCP\AppFramework\Files\Folder;

class WikiHelper {
    public static function isFolder(int $folderId) :bool {
        $mount = \OC\Files\Filesystem::getMountsForFileId($folderId);
        /*
        isReadable()    
        getById($folderId) 
        isCreatable()
        isUpdateable()
        lock()

$config = new \OC\Config('config/');
 $base_path = $config->getValue('datadirectory')

datadirectory is the key in the array defined in config.php that contains the base directory.

$basepath now contains a path like /var/www/html/nextcloud/data.        
        */
        // ToDo
        $nodes = \OC\Files\Node\Folder::getById($folderId);

        return true;
    }
    public static function isWiki(int $folderId) :bool {
        return \OC\Files\Filesystem::nodeExists('.wiki');
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