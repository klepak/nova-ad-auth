<?php

return [

    /**
     * Permissions definition
     *
     * Defines all the available individual permissions on the given guards.
     * All permissions need to be defined here before they are assigned to a role or AD group.
     *
     */
    'permissions' => [
        'GUARD' => [
            'perm1','perm2'
        ]
    ],

    /**
     * Roles definition
     *
     * Defines all available roles, and associates them to the correct permissions on the given guards.
     * All roles need to be defined here before they are assigned to an AD group.
     *
     */
    'roles' => [
        'GUARD' => [
            'ROLE' => [
                'perm1','perm2'
            ],
        ]
    ],

    'ad_groups' => [

        /**
         * AD group to individual permission mapping
         *
         * Defines what individual permissions should be assigned to an authenticating user based on the AD groups
         * the user belongs to, specific to the given guards.
         *
         */
        'permissions' => [
            'GUARD' => [
                'GROUP' => ['role1','role2'],
            ]
        ],

        /**
         * AD group to role mapping
         *
         * Defines what roles should be assigned to an authenticating user based on the AD groups
         * the user belongs to, specific to the given guards.
         *
         */
        'roles' => [
            'GUARD' => [
                'GROUP' => ['role1','role2'],
            ]
        ]
    ]
];
