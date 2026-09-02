<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service responsible for handling file uploads
 */
class FileUploadService
{
    private string $baseUploadDir;
    
    public function __construct(string $baseUploadDir = 'public_html/Anexos')
    {
        $this->baseUploadDir = $baseUploadDir;
    }
    
    /**
     * Uploads files and returns information about uploaded files
     * 
     * @param int $pqrId ID of the PQRSF to associate files with
     * @param array $files FILES array from form submission
     * @return array Array of uploaded file information ['path', 'name', 'filename']
     */
    public function uploadPqrsfFiles(int $pqrId, array $files): array
    {
        $uploadedFiles = [];
        
        if (!isset($files['attachments']) || empty($files['attachments']['name'][0])) {
            return $uploadedFiles;
        }
        
        // Create upload directory based on current date
        $datePath = date("Y") . "/" . date("m") . "/" . date("d");
        $uploadDir = $this->baseUploadDir . "/" . $datePath;
        
        // Create directories recursively if they don't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Failed to create upload directory: {$uploadDir}");
                return $uploadedFiles;
            }
        }
        
        // Process each uploaded file
        foreach ($files['attachments']['tmp_name'] as $key => $tmpName) {
            if ($files['attachments']['error'][$key] !== UPLOAD_ERR_OK) {
                continue; // Skip files with upload errors
            }
            
            $uploadedFile = $this->processFile(
                $tmpName,
                $files['attachments']['name'][$key],
                $uploadDir,
                $pqrId,
                $key
            );
            
            if ($uploadedFile !== null) {
                $uploadedFiles[] = $uploadedFile;
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Process a single file upload
     * 
     * @param string $tmpName Temporary file path
     * @param string $originalName Original filename
     * @param string $uploadDir Target directory
     * @param int $pqrId PQRSF ID
     * @param int $fileIndex File index
     * @return array|null File information or null on failure
     */
    private function processFile(
        string $tmpName,
        string $originalName,
        string $uploadDir,
        int $pqrId,
        int $fileIndex
    ): ?array {
        // Extract file information
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sanitize filename and create the new name
        $safeBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "", $baseName);
        $newFileName = "{$pqrId}_{$safeBaseName}_{$fileIndex}.{$extension}";
        $destination = $uploadDir . "/" . $newFileName;
        
        // Move uploaded file to destination
        if (!move_uploaded_file($tmpName, $destination)) {
            error_log("Failed to move uploaded file: {$originalName}");
            return null;
        }
        
        return [
            'path' => $destination,
            'name' => $originalName,
            'filename' => $newFileName,
            'directory' => $uploadDir . '/'
        ];
    }
    
    /**
     * Validates if a file exists and is readable
     * 
     * @param string $filePath Path to the file
     * @return bool True if file exists and is readable
     */
    public function fileExists(string $filePath): bool
    {
        return file_exists($filePath) && is_readable($filePath);
    }
    
    /**
     * Gets the base upload directory
     * 
     * @return string Base upload directory path
     */
    public function getBaseUploadDir(): string
    {
        return $this->baseUploadDir;
    }
}
