<?php
namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{


    protected $path;


    protected $formats;


    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }


    /**
     * Method for uploading files
     * Check if directory exist and create it otherwise
     *
     * @param UploadedFileInterface $file
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string
    {
        $this->delete($oldFile);
        $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
        $file->moveTo($targetPath);
        $this->generateFormat($targetPath);
        return pathinfo($targetPath)['basename'];
    }


    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }


    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }


    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }


    private function generateFormat($targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $destination = $this->getPathWithSuffix($targetPath, $format);
            $manager = new ImageManager(['driver' => 'gd']);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
            return $this->addCopySuffix($targetPath);
        }
    }
}
