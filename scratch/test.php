<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$import = new App\Imports\PhlAttendanceImport(1);
$result = $import->model(['emp_no' => '649', 'tanggal' => '21/03/2026', 'scan_masuk' => null, 'scan_pulang' => null]);
dump($result);
