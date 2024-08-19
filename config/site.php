<?php

return [
    'site' => [
        'title' => 'Bone Framework',
        'domain' => $_ENV['DOMAIN_NAME'],
        'baseUrl' => 'https://' . $_ENV['DOMAIN_NAME'],
        'contactEmail' => 'abc@' . $_ENV['DOMAIN_NAME'],
        'serverEmail' => 'noreply@' . $_ENV['DOMAIN_NAME'],
        'company' => 'Pirates Inc.',
        'address' => '1 Big Tree, Booty Island',
        'logo' => '/img/skull_and_crossbones.png',
        'emailLogo' => '/img/emails/logo.png',
    ],
];
