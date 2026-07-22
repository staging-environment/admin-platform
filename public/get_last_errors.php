<?php
$logFile = __DIR__.'/../storage/logs/laravel.log';
if (!file_exists($logFile)) {
    die("No log file found");
}
$lines = file($logFile);
$lastLines = array_slice($lines, -150);
echo "<pre>";
foreach ($lastLines as $line) {
    if (str_contains($line, 'error') || str_contains($line, 'Exception') || str_contains($line, 'Stack trace') || str_contains($line, 'TypeError') || str_contains($line, 'Error') || str_contains($line, 'FAIL')) {
        echo htmlspecialchars($line);
    }
}
echo "</pre>";
