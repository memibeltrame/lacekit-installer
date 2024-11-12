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
    'functions_controller.php' => 'functions_controller.php',
    'testpage.php' => 'testpage.php'
];

foreach ($filesToCopy as $sourceFile => $destFile) {
    $sourcePath = $lacekitPath . '/' . $sourceFile;
    $destPath = $projectRoot . '/' . $destFile;
    
    if (!file_exists($sourcePath)) {
        echo "Warning: Source file '$sourceFile' not found in LaceKit package\n";
        continue;
    }
    if(!file_exists($destPath)):
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
    endif;
}

// Then copy folders as before
$foldersToInstall = ['core', 'snippets', 'assets', 'docs'];
$foldersToUpdate = ['core', 'docs'];

foreach ($foldersToInstall as $folder) {
    $sourcePath = $lacekitPath . '/' . $folder;
    $destinationPath = $projectRoot . '/' . $folder;
    
    if (!file_exists($sourcePath)) {
        echo "Warning: Source folder '$folder' not found in LaceKit package\n";
        continue;
    }

    echo "Installing $folder folder...\n";
    
    // // Create destination directory if it doesn't exist
    // if (!is_dir($destinationPath)) {
    //     if (!mkdir($destinationPath, 0755, true)) {
    //         echo "Error: Failed to create directory '$destinationPath'\n";
    //         continue;
    //     }
    // } else {
    //     if(in_array($folder, $foldersToUpdate )){
    //         if(!removeDir($destinationPath)){
    //             echo "Error: Failed to delete directory '$destinationPath'\n";
    //             continue;
    //         } else {
    //             if (!mkdir($destinationPath, 0755, true)) {
    //                 echo "Error: Failed to create directory '$destinationPath'\n";
    //                 continue;
    //             }
    //         }
    //     }
    // }
    // check if destination folder exists
    if (!is_dir($destinationPath)) {
    // Copy directory contents recursively
        try {
            copyDirectory($sourcePath, $destinationPath);
            echo "Successfully installed $folder\n";
        } catch (Exception $e) {
            echo "Error copying $folder: " . $e->getMessage() . "\n";
        }
    } else {
        if(in_array($folder, $foldersToUpdate )){
            try {
                copyDirectory($sourcePath, $destinationPath);
                echo "Successfully installed $folder\n";
            } catch (Exception $e) {
                echo "Error copying $folder: " . $e->getMessage() . "\n";
            }
        }
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

function removeDir(string $dir): void {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($dir);
}
echo "🍾 LaceKit installation completed successfully!\n"; 