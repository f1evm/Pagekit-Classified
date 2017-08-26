<?php

use fevm\Classified\Content\ReadmorePlugin;
use fevm\Classified\Event\AdListener;
use fevm\Classified\Event\RouteListener;

return [

    'name' => 'classified',

    'autoload' => [

        'fevm\\Classified\\' => 'src'

    ],

    'nodes' => [

        'classified' => [
            'name' => '@classified',
            'label' => 'Classified',
            'controller' => 'fevm\\Classified\\Controller\\SiteController',
            'protected' => true,
            'frontpage' => true
        ]

    ],

    'routes' => [

        '/classified' => [
            'name' => '@classified/admin',
            'controller' => 'fevm\\Classified\\Controller\\ClassifiedController'
        ],
        '/classified/category' => [
            'name' => '@category',
            'controller' => 'fevm\\Classified\\Controller\\CategoryController'
        ],

        '/api/classified' => [
            'name' => '@classified/api',
            'controller' => 'fevm\\Classified\\Controller\\AdApiController'
        ],

        '/api/classified/category' => [
            'name' => '@category/api',
            'controller' => 'fevm\\Classified\\Controller\\CategoryApiController'
        ]

    ],

    'permissions' => [

        'classified: manage own ads' => [
            'title' => 'Manage own ads',
            'description' => 'Create, edit, delete and publish ads of their own'
        ],
        'classified: manage all ads' => [
            'title' => 'Manage all ads',
            'description' => 'Create, edit, delete and publish ads by all users'
        ],
        'classified: manage categories' => [
          'title' => 'Manage Categories',
          'description' => 'Create, edit, delete and publish categories'
        ]


    ],

    'menu' => [

        'classified' => [
            'label' => 'Classified',
            'icon' => 'classified:icon.svg',
            'url' => '@classified/admin/ad',
            'active' => '@classified/admin/ad*',
            'access' => 'classified: manage own ads || classified: manage all ads || system: access settings',
            'priority' => 110
        ],
        'classified: ads' => [
            'label' => 'Ads',
            'parent' => 'classified',
            'url' => '@classified/admin/ad',
            'active' => '@classified/admin/ad*',
            'access' => 'classified: manage own ads || classified: manage all ads'
        ],
        'classified: categories' => [
            'label' => 'Categories',
            'parent' => 'classified',
            'url' => '@category',
            'active' => '@category*',
            'access' => 'classified: manage categories'
        ],
        'classified: settings' => [
            'label' => 'Settings',
            'parent' => 'classified',
            'url' => '@classified/admin/settings',
            'active' => '@classified/admin/settings*',
            'access' => 'system: access settings'
        ]

    ],

    'settings' => '@classified/admin/settings',

    'config' => [


      'ads' => [

            'ads_per_page' => 20,
            'markdown_enabled' => true

        ],
      'photos' => [
            'photos_enabled' => true,
            'photos_per_ad' => 6
      ],

        'permalink' => [
            'type' => '',
            'custom' => '{slug}'
        ],

        'feed' => [
            'type' => 'rss2',
            'limit' => 20
        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(
                new RouteListener,
                new AdListener(),
                new ReadmorePlugin
            );
        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('link-classified', 'classified:app/bundle/link-classified.js', '~panel-link');
            $scripts->register('ad-meta', 'classified:app/bundle/ad-meta.js', '~ad-edit');
            $scripts->register('ad-photos', 'classified:app/bundle/ad-photos.js', '~ad-edit');
        }

    ]

];
