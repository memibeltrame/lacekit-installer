<?php

echo "Running LaceKit post-update tasks...\n";

// Get the vendor directory path
$vendorDir = dirname(dirname(dirname(__DIR__))); // Go up from scripts to vendor dir
$projectRoot = dirname($vendorDir); // Project root directory

// Define source and destination paths
$lacekitPath = $vendorDir . '/memibeltrame/lacekit';



// Then copy folders as before
$foldersToInstall = ['core', 'docs'];

foreach ($foldersToInstall as $folder) {
    $sourcePath = $lacekitPath . '/' . $folder;
    $destinationPath = $projectRoot . '/' . $folder;
    
    if (!file_exists($sourcePath)) {
        echo "Warning: Source folder '$folder' not found in LaceKit package\n";
        continue;
    }

    echo "Installing $folder folder...\n";
    
    // Create destination directory if it doesn't exist
    if (!is_dir($destinationPath)) {
        if (!mkdir($destinationPath, 0755, true)) {
            echo "Error: Failed to create directory '$destinationPath'\n";
            continue;
        }
    } else {
        if(!rmdir($destinationPath)){
            echo "Error: Failed to delete directory '$destinationPath'\n";
            continue;
        } else {
            if (!mkdir($destinationPath, 0755, true)) {
                echo "Error: Failed to create directory '$destinationPath'\n";
                continue;
            }
        }
    }

    // Copy directory contents recursively
    try {
        copyDirectory($sourcePath, $destinationPath);
        echo "Successfully installed $folder\n";
    } catch (Exception $e) {
        echo "Error copying $folder: " . $e->getMessage() . "\n";
    }
}

// add start script
$rootComposerJson = getcwd() . '/composer.json';
$composerData = json_decode(file_get_contents($rootComposerJson), true);

if (!isset($composerData['scripts']['start'])) {
    $composerData['scripts']['start'] = array( 'Composer\\Config::disableProcessTimeout', 'php -S localhost:8000' );

    file_put_contents($rootComposerJson, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Added 'start' script to the root composer.json\n";
}


// Helper function to copy directories recursively
function copyDirectory($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcFile = $source . '/' . $file;
            $destFile = $destination . '/' . $file;
            
            if (is_dir($srcFile)) {
                copyDirectory($srcFile, $destFile);
            } else {
                if (!copy($srcFile, $destFile)) {
                    throw new Exception("Failed to copy $srcFile to $destFile");
                }
            }
        }
    }
    closedir($dir);
}

echo "🍾 LaceKit update completed successfully!\n"; 