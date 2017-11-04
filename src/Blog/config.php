<?php

use App\Blog\BlogModule;
use function \DI\object;
use function \DI\get;

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => DI\add([
        get(\App\Blog\BlogWidget::class)
    ])
];
