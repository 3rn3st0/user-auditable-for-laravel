<?php
/**
 * Tagger
 * Increments the patch version, updates composer.json, and pushes git tags.
 */

$composer = 'composer.json';

// 1. Load and Parse
if (!file_exists($composer)) {
    die("Error: $composer not found." . PHP_EOL);
}

$data = json_decode(file_get_contents($composer), true);
$currentVersion = $data['version'] ?? '1.0.0';

// 2. Increment Version (Patch)
$parts = explode('.', $currentVersion);
if (count($parts) === 3) {
    $parts[2]++; // Increment the last digit
} else {
    die("Error: Version format in $composer must be X.Y.Z" . PHP_EOL);
}

$newVersion = implode('.', $parts);
$tagName = "v$newVersion";

// 3. Update composer.json
$data['version'] = $newVersion;
file_put_contents($composer, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Successfully updated $composer to version $newVersion" . PHP_EOL;

// 33: Git Operations
// Detectar rama actual
exec('git branch --show-current', $branchOut, $branchCode);
$branch = trim($branchOut[0] ?? 'main');

echo "Committing version update..." . PHP_EOL;
exec("git add " . escapeshellarg($composer), $output, $resultCode);
if ($resultCode !== 0) {
    die("Error: git add failed." . PHP_EOL);
}

exec("git commit -m \"Release $tagName\"", $output, $resultCode);
if ($resultCode !== 0) {
    die("Error: git commit failed." . PHP_EOL);
}

echo "Creating tag $tagName..." . PHP_EOL;
exec("git tag " . escapeshellarg($tagName), $output, $resultCode);

if ($resultCode === 0) {
    echo "Pushing commit and tag to origin on branch $branch..." . PHP_EOL;
    exec("git push origin " . escapeshellarg($branch), $output, $pushCode);
    exec("git push origin " . escapeshellarg($tagName), $output, $tagPushCode);
    
    if ($pushCode === 0 && $tagPushCode === 0) {
        echo "Deployment of $tagName completed successfully!" . PHP_EOL;
    } else {
        echo "Error: Git push failed." . PHP_EOL;
    }
} else {
    die("Error: Could not create git tag. Does it already exist?" . PHP_EOL);
}
