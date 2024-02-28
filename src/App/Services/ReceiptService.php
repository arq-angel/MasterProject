<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationExceptions;
use App\Config\Paths;

class ReceiptService
{
    public function __construct(private Database $db)
    {
    }

    public function validateFile(?array $file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationExceptions([
                'receipt' => ['Failed to upload file']
            ]);
        }

        $maxFileSizeMB = 3 * 1024 * 1024;

        if ($file['size'] > $maxFileSizeMB) {
            throw new ValidationExceptions([
                'receipt' => ['File upload is too large']
            ]);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationExceptions([
                'receipt' => ['Invalid Filename']
            ]);
        }

        $clientMimeType = $file['type'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array($clientMimeType, $allowedMimeTypes)) {
            throw new ValidationExceptions([
                'receipt' => ['Invalid file type']
            ]);
        }

    }

    public function upload(array $file, int $transaction)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = bin2hex(random_bytes(16)) . "." . $fileExtension;

        $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ValidationExceptions([
                'receipt' => 'Failed to upload file'
            ]);
        }

        $this->db->query(
            "INSERT INTO receipts(transaction_id, original_filename, storage_filename, media_type)
            VALUES(:transaction_id, :original_filename, :storage_filename, :media_type) ",
            [
                'transaction_id' => $transaction,
                'original_filename' => $file['name'],
                'storage_filename' => $newFilename,
                'media_type' => $file['type']
            ]
        );
    }

    public function getReceipt(string $id)
    {
        $receipt = $this->db->query(
            "SELECT * FROM receipts WHERE id = :id",
            [
                'id' => $id
            ]
        )->find();

        return $receipt;
    }

    public function read(array $receipt)
    {
        $filepath = Paths::STORAGE_UPLOADS . '/' . $receipt['storage_filename'];

        if (!file_exists($filepath)) {
            redirectTo('/');
        }

        header("Content-Disposition: inline;filename={$receipt['original_filename']}");
        header("Content-Type: {$receipt['media_type']}");

        readFile($filepath);
    }

    public function delete(array $receipt)
    {
        $filepath = Paths::STORAGE_UPLOADS . "/" . $receipt['storage_filename'];

        unlink($filepath);

        $this->db->query(
            "DELETE FROM receipts WHERE id= :id",
            [
                'id' =>$receipt['id']
            ]
        );
    }
}
