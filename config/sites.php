<?php
return [
  'sites' => [
    'site1' => [
      'domain' => 'site1.testsite.test',
      'db_connection' => 'mysql_site1',
      'theme' => 'theme1',
    ],
    'site2' => [
      'domain' => 'site2.test',
      'db_connection' => 'mysql_site2',
      'theme' => 'theme2',
    ],
  ],
  'default_site' => 'site1', // Varsayılan site
];
