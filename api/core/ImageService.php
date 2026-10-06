<?php

namespace Core;

use Exception;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Motor de processamento de imagens:
 * - redimensiona para no máximo 1920px de largura
 * - converte para WebP
 * - gera miniatura (400px) de baixo peso
 * - distribui os arquivos em pastas batch_N com no máximo 2500 arquivos cada
 */
class ImageService {
    private $baseUploadDir;
    private $baseUrl;
    private $manager = null;
    private $maxFilesPerDirectory = 2500;
    private $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function __construct() {
        $this->baseUploadDir = rtrim(Config::get('upload_dir'), '/\\') . '/';
        $this->baseUrl = rtrim(Config::get('upload_url', '/uploads'), '/');

        if (extension_loaded('gd')) {
            $this->gdLoaded = true;
            if (class_exists(ImageManager::class)) {
                $this->manager = new ImageManager(new Driver());
            }
        } else {
            $this->gdLoaded = false;
        }
    }

    /**
     * @param array  $file      Item de $_FILES
     * @param string $subFolder Subpasta principal (galleries, posts, memorial, content)
     * @return array image_path e thumbnail_path (URLs públicas relativas)
     */
    public function processAndSave($file, $subFolder = 'photos') {
        $this->validate($file);

        $targetDir = $this->getAvailableDirectory($subFolder);
        
        if ($this->gdLoaded) {
            $filename = bin2hex(random_bytes(6)) . '_' . time();
            $mainFilename = $filename . '.webp';
            $thumbFilename = $filename . '_thumb.webp';
            
            $mainPath = $targetDir['absolute'] . '/' . $mainFilename;
            $thumbPath = $targetDir['absolute'] . '/' . $thumbFilename;

            if ($this->manager) {
                $image = $this->manager->read($file['tmp_name']);
                if ($image->width() > 1920) $image->scale(width: 1920);
                $image->toWebp(80)->save($mainPath);

                $thumb = $this->manager->read($file['tmp_name']);
                if ($thumb->width() > 400) $thumb->scale(width: 400);
                $thumb->toWebp(65)->save($thumbPath);
            } else {
                $this->processWithGD($file['tmp_name'], $mainPath, 1920, 80);
                $this->processWithGD($file['tmp_name'], $thumbPath, 400, 65);
            }
        } else {
            // Fallback (Sem GD): Apenas copia o arquivo original sem otimização
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = bin2hex(random_bytes(6)) . '_' . time() . '.' . $extension;
            $mainFilename = $filename;
            $thumbFilename = $filename; // Usa a mesma imagem como thumbnail
            
            $mainPath = $targetDir['absolute'] . '/' . $mainFilename;
            move_uploaded_file($file['tmp_name'], $mainPath);
        }

        $urlBase = $this->baseUrl . '/' . $subFolder . '/' . $targetDir['folderName'] . '/';
        return [
            'image_path' => $urlBase . $mainFilename,
            'thumbnail_path' => $urlBase . $thumbFilename,
        ];
    }

    /** Miniatura correspondente a uma imagem principal gerada por este serviço. */
    public static function thumbFor($imagePath) {
        if (!$imagePath) {
            return null;
        }
        return preg_replace('/\.webp$/', '_thumb.webp', $imagePath);
    }

    private function validate($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Arquivo não recebido.');
        }
        $maxBytes = (int) Config::get('max_upload_mb', 25) * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new Exception('Arquivo maior que o limite permitido.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new Exception('Formato não suportado. Envie JPG, PNG, WebP ou GIF.');
        }
    }

    /**
     * Encontra (ou cria) a pasta batch_N mais recente com menos de 2500 arquivos.
     */
    private function getAvailableDirectory($subFolder) {
        $path = $this->baseUploadDir . $subFolder;
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }

        $directories = glob($path . '/batch_*', GLOB_ONLYDIR) ?: [];
        $latest = 1;
        foreach ($directories as $dir) {
            $latest = max($latest, (int) str_replace('batch_', '', basename($dir)));
        }

        $latestDir = $path . '/batch_' . $latest;
        if (!is_dir($latestDir)) {
            mkdir($latestDir, 0775, true);
        }

        // Cada upload gera 2 arquivos (principal + miniatura)
        $fileCount = count(glob($latestDir . '/*') ?: []);
        if ($fileCount + 2 > $this->maxFilesPerDirectory) {
            $latest++;
            $latestDir = $path . '/batch_' . $latest;
            mkdir($latestDir, 0775, true);
        }

        return ['absolute' => $latestDir, 'folderName' => 'batch_' . $latest];
    }

    private function processWithGD($sourcePath, $destinationPath, $maxWidth, $quality) {
        $info = getimagesize($sourcePath);
        switch ($info['mime']) {
            case 'image/jpeg': $image = imagecreatefromjpeg($sourcePath); break;
            case 'image/png':  $image = imagecreatefrompng($sourcePath); break;
            case 'image/gif':  $image = imagecreatefromgif($sourcePath); break;
            case 'image/webp': $image = imagecreatefromwebp($sourcePath); break;
            default: throw new Exception('Formato não suportado.');
        }

        $width = imagesx($image);
        $height = imagesy($image);
        if ($width > $maxWidth) {
            $newHeight = (int) floor($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        imagewebp($image, $destinationPath, $quality);
        imagedestroy($image);
    }
}
