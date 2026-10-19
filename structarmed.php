<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Plugin', 'src/Plugin/src')
    ->layer('App', 'src/App/src')
    ->layer('User', 'src/User/src')
    ->layer('Contact', 'src/Contact/src')
    ->layer('Page', 'src/Page/src')
    ->ruleset([
        'Plugin'  => [],
        'App'     => ['User'],
        'User'    => ['+App', 'Plugin'],
        'Contact' => ['+User'],
        'Page'    => [],
    ]);
