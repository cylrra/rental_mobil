<?php
$dir = new RecursiveDirectoryIterator('.');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    if (strpos($path, 'vendor') !== false || strpos($path, 'scanner.php') !== false) continue;
    $content = file_get_contents($path);
    // Find mysqli_query with variables inside the string
    if (preg_match('/mysqli_query\s*\(\s*\$conn\s*,\s*["\'][^"\']*?\$[^"\']*?["\']\s*\)/i', $content)) {
        echo "Vulnerable: $path\n";
    }
}
?>
