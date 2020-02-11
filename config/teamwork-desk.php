<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Teamwork Desk Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable SaaS package.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Teamwork Desk Key
    |--------------------------------------------------------------------------
    |
    | The Teamwork Desk API Key can be generated at:
    | https://your-domain.teamwork.com/desk/#myprofile/apikeys
    |
    */
    'key' => env('TEAMWORK_DESK_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Teamwork Desk Domain Name
    |--------------------------------------------------------------------------
    |
    | The domain is the site address you have set on the Teamwork account.
    | To find the domain name just login to http://teamwork.com.
    | Then you will see the browser URL changing to:
    | https://your-domain.teamwork.com/launchpad/welcome
    |
    */
    'domain' => env('TEAMWORK_DESK_DOMAIN'),

    /*
     |--------------------------------------------------------------------------
     | Teamwork Desk Webhook Token
     |--------------------------------------------------------------------------
     |
     |
     */
    'webhook_token' => env('TEAMWORK_DESK_WEBHOOK_TOKEN'),

    /*
     |--------------------------------------------------------------------------
     | Teamwork Desk Site ID
     |--------------------------------------------------------------------------
     |
     |
     */
    'site_id' => env('TEAMWORK_DESK_SITE_ID'),

    /*
     |--------------------------------------------------------------------------
     | Teamwork Desk Inbox
     |--------------------------------------------------------------------------
     |
     |
     */
    'inbox' => env('TEAMWORK_DESK_INBOX'),

    /*
     |--------------------------------------------------------------------------
     | Route Group
     |--------------------------------------------------------------------------
     |
     | Route groups allow you to share route attributes, such as middleware
     | or namespaces, across a large number of routes without needing to define
     | those attributes on each individual route.
     | See: https://laravel.com/docs/6.x/routing#route-groups
     |
     */
    'route_group' => [
        'api' => [
            'domain'     => null,
            'as'         => 'api.',
            'prefix'     => 'api',
            'middleware' => ['api', 'auth:api']
        ],
    ],
];
