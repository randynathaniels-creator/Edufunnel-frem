<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$data = DB::table('dashboard_data')->get();
echo json_encode($data, JSON_PRETTY_PRINT);
