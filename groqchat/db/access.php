<?php
$capabilities = [
    'local/groqchat:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => ['student' => CAP_ALLOW, 'teacher' => CAP_ALLOW],
    ],
];
