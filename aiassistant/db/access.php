<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'mod/aiassistant:addinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ]
    ],
];
