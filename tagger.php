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

// 4. Git Operations
echo "Committing version update..." . PHP_EOL;
exec("git add $composer");
exec("git commit -m \"Release $tagName\"");

echo "Creating tag $tagName..." . PHP_EOL;
exec("git tag $tagName", $output, $resultCode);

if ($resultCode === 0) {
    echo "Pushing commit and tag to origin..." . PHP_EOL;
    exec("git push origin main"); // Change 'main' to your branch name if needed
    exec("git push origin $tagName");
    echo "Deployment of $tagName completed successfully!" . PHP_EOL;
} else {
    die("Error: Could not create git tag. Does it already exist?" . PHP_EOL);
}
