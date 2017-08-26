<?php
use Pagekit\Filesystem\Filesystem;

return [

    'install' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@classified_ad') === false) {
            $util->createTable('@classified_ad', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('user_id', 'integer', ['unsigned' => true, 'length' => 10, 'default' => 0]);
                $table->addColumn('slug', 'string', ['length' => 255]);
                $table->addColumn('title', 'string', ['length' => 255]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('date', 'datetime', ['notnull' => false]);
                $table->addColumn('modified', 'datetime');
                $table->addColumn('content', 'text');
                $table->addColumn('excerpt', 'text');
                $table->addColumn('categ_id', 'integer', ['notnull' => false, 'unsigned' => true, 'length' => 10, 'default' => 0]);
                $table->addColumn('photos', 'simple_array', ['notnull' => false]);
                $table->addColumn('ad_photo', 'integer', ['notnull' => false, 'unsigned' => true, 'length' => 10, 'default' => 0]);

                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->addColumn('roles', 'simple_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addUniqueIndex(['slug'], '@CLASSIFIED_AD_SLUG');
                $table->addIndex(['title'], '@CLASSIFIED_AD_TITLE');
                $table->addIndex(['user_id'], '@CLASSIFIED_AD_USER_ID');
                $table->addIndex(['date'], '@CLASSIFIED_AD_DATE');

            });
        }


        if ($util->tableExists('@classified_category') === false) {
            $util->createTable('@classified_category', function ($table) {
                  $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                  $table->addColumn('slug', 'string', ['length' => 255]);
                  $table->addColumn('name', 'string', ['length' => 255]);
                  $table->addColumn('status', 'smallint');
                  $table->addColumn('date', 'datetime', ['notnull' => false]);
                  $table->addColumn('modified', 'datetime');
                  $table->addColumn('description', 'text');
                  $table->addColumn('parent_id', 'integer', ['unsigned' => true, 'length'=> 10, 'default' => 0]);
                  $table->addColumn('priority', 'integer', ['unsigned' => true, 'length'=> 11, 'default' => 0]);
                  $table->addColumn('data', 'json_array', ['notnull' => false]);
                  $table->setPrimaryKey(['id']);
                  $table->addUniqueIndex(['slug'], '@CLASSIFIED_CATEGORY_SLUG');
                  $table->addIndex(['name'], '@CLASSIFIED_CATEGORY_NAME');
                  $table->addIndex(['date'], '@CLASSIFIED_CATEGORY_DATE');
            });
        }


        $fs = new Filesystem();
        if (!$fs->exists($app['path.storage'] . '/classified/')){
          $fs->makeDir($app['path.storage'] . '/classified/');
        }

    },

    'uninstall' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@classified_ad')) {
            $util->dropTable('@classified_ad');
        }

        if ($util->tableExists('@classified_category')) {
            $util->dropTable('@classified_category');
        }
    },

    'updates' => [


    ]

];
