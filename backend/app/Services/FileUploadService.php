<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileUploadService
{
    /**
     * Validate and store an uploaded file.
     *
     * Performs mime/extension and file-size validation, generates a unique
     * filename, stores the file under storage/app/public/{$directory}, and
     * returns the relative path (suitable for Storage::url()).
     *
     * @param  UploadedFile  $file
     * @param  string        $directory     Sub-directory inside public disk, e.g. "avatars"
     * @param  string[]      $allowedMimes  Allowed file extensions (without dot)
     * @param  int           $maxSize       Maximum allowed size in kilobytes
     * @return string                       Relative path, e.g. "avatars/uuid.jpg"
     *
     * @throws InvalidArgumentException  When validation fails
     */
    public function upload(
        UploadedFile $file,
        string $directory,
        array $allowedMimes = ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        int $maxSize = 2048
    ): string {
        // Validate extension using the client-provided extension (guessExtension
        // works even without a real MIME library by falling back to the original
        // extension).
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedMimes, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file type "%s". Allowed types: %s.',
                    $extension,
                    implode(', ', $allowedMimes)
                )
            );
        }

        // Validate size (UploadedFile::getSize() returns bytes).
        $fileSizeKb = (int) ceil($file->getSize() / 1024);

        if ($fileSizeKb > $maxSize) {
            throw new InvalidArgumentException(
                sprintf(
                    'File size %d KB exceeds the maximum allowed size of %d KB.',
                    $fileSizeKb,
                    $maxSize
                )
            );
        }

        // Generate a collision-resistant filename.
        $filename = Str::uuid()->toString() . '.' . $extension;

        // Store on the public disk (storage/app/public/{directory}/{filename}).
        $file->storeAs($directory, $filename, 'public');

        return $directory . '/' . $filename;
    }

    /**
     * Delete a file from the public disk.
     *
     * Silently succeeds if the file does not exist.
     *
     * @param  string|null  $path  Relative path previously returned by upload()
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }
}
