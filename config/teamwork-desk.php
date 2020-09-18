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
     | Tickets List and Ticket Details URL Segments
     |--------------------------------------------------------------------------
     |
     | This options are used when building the notification messages.
     |
     | E.g. for tickets list: support-tickets
     | Translates to: https://domain.com/support-tickets
     |
     | E.g. for ticket details: ticket
     | Translates to: https://domain.com/support-tickets/ticket
     |
     */
    'tickets_paths' => [
        'list'    => 'tickets',
        'details' => 'ticket'
    ],

    /*
     |--------------------------------------------------------------------------
     | Permissions Middleware
     |--------------------------------------------------------------------------
     |
     | Set the rules for user permissions.
     | This will be used in the controller middleware as:
     | $this->middleware('role:user')...
     |
     */
    'authorization' => 'role:user',

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
     | This is the secret token saved on each webhook on WebHooks Settings page.
     | See: https://your-domain.teamwork.com/desk/settings/general/webhooks
     |
     */
    'webhook_token' => env('TEAMWORK_DESK_WEBHOOK_TOKEN'),

    /*
     |--------------------------------------------------------------------------
     | Teamwork Desk Inbox
     |--------------------------------------------------------------------------
     |
     | The Inbox is the name of the Teamwork Desk Inbox name.
     | See: https://your-domain.teamwork.com/desk/inboxes for a list of
     | available inboxes.
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
        'web' => [
            'domain'     => null,
            'as'         => null,
            'prefix'     => null,
            'middleware' => 'web'
        ],

        'api' => [
            'domain'     => null,
            'as'         => 'api.',
            'prefix'     => 'api',
            'middleware' => ['api', 'auth:api']
        ],
    ],
];
