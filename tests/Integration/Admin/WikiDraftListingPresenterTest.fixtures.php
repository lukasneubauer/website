<?php

return [
    App\Entities\UserEntity::class => [
        'user_admin' => [
            'username'        => 'johndoe',
            'email'           => 'john.doe@example.com',
            'avatar'          => null,
            'forename'        => 'John',
            'surname'         => 'Doe',
            'password'        => '$2y$10$D7DlW8aCiF0JZfvXCpxdeeMbklNC0nJ7IcvdpwIgQoHtWTLQ1UVK2',
            'salt'            => 'X1QWzJRBy3',
            'role'            => App\Entities\UserEntity::ROLE_ADMINISTRATOR,
            'token'           => null,
            'tokenCreatedAt'  => null,
            'isAuthenticated' => true,
        ],
    ],
    App\Entities\TagEntity::class => [
        'tag_article_novinky' => [
            'name' => 'Novinky',
            'slug' => 'novinky',
        ],
    ],
    App\Entities\WikiEntity::class => [
        'wiki_game_1' => [
            'tag'           => '@tag_article_novinky',
            'related'       => [],
            'contributors'  => ['@user_admin'],
            'drafts'        => [],
            'createdBy'     => '@user_admin',
            'lastUpdatedBy' => '@user_admin',
            'name (unique)' => '<sentence(3)>',
            'slug (unique)' => '<slug()>',
            'perex'         => '<sentences(4, true)>',
            'text'          => '',
            'type'          => App\Entities\WikiEntity::TYPE_GAME,
            'isActive'      => false,
            'createdAt'     => '<dateTimeBetween(\'2000-01-01 00:00:01\', \'2000-06-30 23:59:59\')>',
            'updatedAt'     => '80%? <dateTimeBetween($createdAt, \'2000-06-30 23:59:59\')> : <dateTime($createdAt)>',
        ],
    ],
];