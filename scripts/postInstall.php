<?php

echo "Running LaceKit post-installation tasks...\n";

// Get the vendor directory path
$vendorDir = dirname(dirname(dirname(__DIR__))); // Go up from scripts to vendor dir
$projectRoot = dirname($vendorDir); // Project root directory

// Define source and destination paths
$lacekitPath = $vendorDir . '/memibeltrame/lacekit';

// First copy specific files to root
$filesToCopy = [
    'index.php' => 'index.php',
    'functions_controller.php' => 'functions_controller.php'
];

foreach ($filesToCopy as $sourceFile => $destFile) {
    $sourcePath = $lacekitPath . '/' . $sourceFile;
    $destPath = $projectRoot . '/' . $destFile;
    
    if (!file_exists($sourcePath)) {
        echo "Warning: Source file '$sourceFile' not found in LaceKit package\n";
        continue;
    }

    echo "Copying $sourceFile to project root...\n";
    
    try {
        if (copy($sourcePath, $destPath)) {
            echo "Successfully copied $sourceFile\n";
        } else {
            echo "Error: Failed to copy $sourceFile\n";
        }
    } catch (Exception $e) {
        echo "Error copying $sourceFile: " . $e->getMessage() . "\n";
    }
}

// Then copy folders as before
$foldersToInstall = ['core', 'snippets', 'assets'];

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
    }

    // Copy directory contents recursively
    try {
        copyDirectory($sourcePath, $destinationPath);
        echo "Successfully installed $folder\n";
    } catch (Exception $e) {
        echo "Error copying $folder: " . $e->getMessage() . "\n";
    }
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

echo "LaceKit installation completed successfully!\n"; 