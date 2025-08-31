<?php
// config.php
return [
  'db' => [
    // Using root locally (XAMPP default). Switch later when GRANT works.
    'dsn'  => 'mysql:host=127.0.0.1;dbname=todo_app;charset=utf8mb4',
    'user' => 'root',
    'pass' => '',
  ],
  'app' => [
    'name' => 'FocusList : To-Do',
    'base_path' => '/todo/public', // URL path to /public
    'items_per_page' => 8
  ]
];
