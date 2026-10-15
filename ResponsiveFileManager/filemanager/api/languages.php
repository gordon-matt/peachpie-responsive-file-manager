<?php
/**
 * Languages API endpoint
 * Returns available languages as JSON
 */

header('Content-Type: application/json');
header('Cache-Control: max-age=3600'); // Cache for 1 hour

try {
    if (file_exists('../lang/languages.php')) {
        $languages = include '../lang/languages.php';
    } else {
        $languages = include '../../lang/languages.php';
    }
    
    if (!is_array($languages)) {
        $languages = ['en_EN' => 'English'];
    }
    
    echo json_encode($languages, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load languages']);
}
?>