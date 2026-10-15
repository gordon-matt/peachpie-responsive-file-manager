<?php
/**
 * Language API endpoint
 * Returns language translations for a specific language as JSON
 */

header('Content-Type: application/json');
header('Cache-Control: max-age=1800'); // Cache for 30 minutes

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en_EN';

// Sanitize language code
$lang = preg_replace('/[^a-zA-Z0-9_-]/', '', $lang);

try {
    // Try to load the language file
    $langFile = null;
    if (file_exists("../lang/{$lang}.php")) {
        $langFile = "../lang/{$lang}.php";
    } elseif (file_exists("../../lang/{$lang}.php")) {
        $langFile = "../../lang/{$lang}.php";
    }
    
    if ($langFile && is_readable($langFile)) {
        $lang_vars = include $langFile;
        
        if (!is_array($lang_vars)) {
            $lang_vars = [];
        }
        
        echo json_encode($lang_vars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        // Fall back to English if requested language not found
        if (file_exists('../lang/en_EN.php')) {
            $lang_vars = include '../lang/en_EN.php';
        } elseif (file_exists('../../lang/en_EN.php')) {
            $lang_vars = include '../../lang/en_EN.php';
        } else {
            $lang_vars = [];
        }
        
        if (!is_array($lang_vars)) {
            $lang_vars = [];
        }
        
        echo json_encode($lang_vars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load language data']);
}
?>