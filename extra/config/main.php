<?php

/*
 * Config override to use the findcontact-ripe
 */

return [
    'external' => [
        'prefer_local'                      => true,
        'findcontact'                       => [
            'id' => [
                [
                    'class'                     => 'Ripe',
                    'method'                    => 'getContactById',
                ],
            ],
            'ip' => [
                [
                    'class'                     => 'Ripe',
                    'method'                    => 'getContactByIp',
                ],
            ],
            'domain' => [
                [
                    'class'                     => 'Ripe',
                    'method'                    => 'getContactByDomain',
                ],
            ],
        ],
    ],
];
