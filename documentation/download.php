<?php

declare(strict_types=1);

$documentPath = dirname(__DIR__) . '/Hotel_Maintenance_Documentation.docx';
if (!is_file($documentPath) || !is_readable($documentPath)) {
    http_response_code(404);
    exit('Documentation file not found.');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="Hotel_Maintenance_Documentation.docx"');
header('Content-Length: ' . (string) filesize($documentPath));
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: 0');

readfile($documentPath);
exit;
