<?php
/**
 * Sujan Top-Up - Secure file upload helper.
 *
 * Centralises upload validation so every endpoint enforces the same rules:
 *  - extension allowlist
 *  - real MIME sniffing (not the client-supplied type)
 *  - size limit
 *  - randomised filename
 *  - no PHP/script uploads
 *  - traversal-safe destination inside a fixed directory
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

if (!function_exists('sujan_store_upload')) {
    /**
     * Validate and move an uploaded file into $destDir.
     *
     * @param array  $file    Entry from $_FILES
     * @param string $destDir Absolute destination directory
     * @param array  $allowed map of extension => list of allowed MIME types
     * @param int    $maxBytes
     * @param string $prefix
     * @return array{ok: bool, path?: string, error?: string}
     */
    function sujan_store_upload(array $file, string $destDir, array $allowed, int $maxBytes, string $prefix = 'upload_'): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['ok' => false, 'error' => 'Invalid upload payload.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload failed (code ' . (int) $file['error'] . ').'];
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'Invalid upload source.'];
        }

        if (($file['size'] ?? 0) > $maxBytes) {
            return ['ok' => false, 'error' => 'File exceeds the maximum allowed size.'];
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if ($ext === '' || !array_key_exists($ext, $allowed)) {
            return ['ok' => false, 'error' => 'File type is not allowed.'];
        }

        // Sniff the real MIME type from the file contents.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowed[$ext], true)) {
            return ['ok' => false, 'error' => 'File contents do not match an allowed type.'];
        }

        if (!is_dir($destDir) && !@mkdir($destDir, 0775, true)) {
            return ['ok' => false, 'error' => 'Upload directory is not writable.'];
        }

        $safeName = $prefix . bin2hex(random_bytes(16)) . '.' . $ext;
        $target = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['ok' => false, 'error' => 'Could not store the uploaded file.'];
        }

        @chmod($target, 0644);

        return ['ok' => true, 'path' => $target];
    }
}

if (!function_exists('sujan_image_allowlist')) {
    /** Default allowlist for image uploads. */
    function sujan_image_allowlist(): array
    {
        return [
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
            'gif'  => ['image/gif'],
            'webp' => ['image/webp'],
        ];
    }
}

if (!function_exists('sujan_video_allowlist')) {
    /** Default allowlist for video uploads. */
    function sujan_video_allowlist(): array
    {
        return [
            'mp4'  => ['video/mp4'],
            'webm' => ['video/webm'],
            'mov'  => ['video/quicktime'],
        ];
    }
}