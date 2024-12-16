<?php

return [

  'paths' => [
    module_path('MultiSite', 'Resources/views'),
    module_path('Blog', 'Resources/views'),
    module_path('News', 'Resources/views'),
  ],

  'compiled' => env(
    'VIEW_COMPILED_PATH',
    realpath(storage_path('framework/views'))
  ),

];
