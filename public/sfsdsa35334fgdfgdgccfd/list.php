<?php
$directory = '../../keys';

// Open the directory
if ($handle = opendir($directory)) {
    // Loop through the directory
    while (false !== ($file = readdir($handle))) {
        // Skip the current and parent directory entries
        if ($file != "." && $file != "..") {
            // Get the full path of the file
            $filePath = $directory . '/' . $file;

            // Check if it's a file (not a directory)
            if (is_file($filePath)) {
                // Get the content of the file
                $content = file_get_contents($filePath);
                echo "Content of $file:\n$content\n\n";
            }
        }
    }
    // Close the directory
    closedir($handle);
} else {
    echo "Unable to open the directory.";
}