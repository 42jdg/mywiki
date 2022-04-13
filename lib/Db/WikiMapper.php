<?php
namespace OCA\MyWiki\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class WikiMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'mywiki', Wiki::class);
    }

    public function usersCount(int $folderId) {
        $qb = $this->db->getQueryBuilder();
        // ToDo - get the count
        $qb->select($qb->createFunction('COUNT()'))
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('folderId', $qb->createNamedParameter($folderId))
        );
        return $qb->getSQL();
    }

    public function find(int $id, string $userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
                 ->where(
                        $qb->expr()->eq('id', $qb->createNamedParameter($id))
                 )->andWhere(
                        $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
                 );
        return $this->findEntity($qb);
    }
    
    public function findAll(string $userId, ?array $filter=null) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where(
            $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
           );

        if ($filter) {
            if (array_key_exists('title',$filter) ) {
                $qb->where(
                    $qb->expr()->eq('title', $qb->createNamedParameter($filter['title']))
                );
            }                    
            if (array_key_exists('fileId',$filter) ) {
                $qb->where(
                    $qb->expr()->eq('file_id', $qb->createNamedParameter($filter['fileId']))
                );
            }      
        }
        return $this->findEntities($qb);
    }

}