<?php

  namespace OCA\MyWiki\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version000000Date20220302210900 extends SimpleMigrationStep {

    /**
    * @param IOutput $output
    * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
    * @param array $options
    * @return null|ISchemaWrapper
    */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('mywiki')) {
            $table = $schema->createTable('mywiki');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('file_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'mywiki_user_id_index');
            $table->addIndex(['file_id'], 'mywiki_file_id_index');
        }

/*
        if (!$schema->hasTable('mywiki_pages')) {
            $table = $schema->createTable('mywiki_pages');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('pid', 'integer', [
                'notnull' => true
            ]);
            $table->addColumn('wiki_id', 'integer', [
                'notnull' => true
            ]);
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);
            $table->addColumn('path', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['wiki_id'], 'mywiki_wiki_id_index');
        }        
*/
        return $schema;
    }
}