<?php
/**
 * Provides the possibility to easily create file downloads in PHP
 *
 * @author Jannik Zschiesche <hello@apfelbox.net>
 * @version 1.0
 * @license MIT
 */

namespace Apfelbox\FileDownload;

use Skyzyx\Components\Mimetypes\Mimetypes;

/**
 * Provides a simple way to create file downloads in PHP
 */
class FileDownload
{
    /**
     * The pointer to the file to download
     *
     * @var resource
     */
    private $filePointer;



    /**
     * Constructs a new file download
     *
     * @param resource $filePointer
     *
     * @throws \InvalidArgumentException
     */
    public function __construct ($filePointer)
    {
        if (!is_resource($filePointer))
        {
            throw new \InvalidArgumentException("You must pass a file pointer to the ctor");
        }

        $this->filePointer = $filePointer;
    }



    /**
     * Sends the download to the browser
     *
     * @param string $filename
     * @param bool $forceDownload
     *
     * @throws \RuntimeException is thrown, if the headers are already sent
     */
    public function sendDownload ($filename, $forceDownload = true)
    {
        if (headers_sent())
        {
            throw new \RuntimeException("Cannot send file to the browser, since the headers were already sent.");
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: {$this->getMimeType($filename)}");

        if ($forceDownload)
        {
            header("Content-Disposition: attachment; filename=\"{$filename}\";" );
        }
        else
        {
            header("Content-Disposition: filename=\"{$filename}\";" );
        }

        header("Content-Transfer-Encoding: binary");
        header("Content-Length: {$this->getFileSize()}");

        @ob_clean();

        rewind($this->filePointer);
        fpassthru($this->filePointer);
    }



    /**
     * Returns the mime type of a file name
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getMimeType ($fileName)
    {
        $fileExtension  = pathinfo($fileName, PATHINFO_EXTENSION);
        $mimeTypeHelper = Mimetypes::getInstance();
        $mimeType       = $mimeTypeHelper->fromExtension($fileExtension);

        return !is_null($mimeType) ? $mimeType : "application/force-download";
    }



    /**
     * Returns the file size of the file
     *
     * @return int
     */
    private function getFileSize ()
    {
        $stat = fstat($this->filePointer);
        return $stat['size'];
    }



    /**
     * Creates a new file download from a file path
     *
     * @static
     *
     * @param string $filePath
     *
     * @throws \InvalidArgumentException is thrown, if the given file does not exist or is not readable
     *
     * @return FileDownload
     */
    public static function createFromFilePath ($filePath)
    {
        if (!is_file($filePath))
        {
            throw new \InvalidArgumentException("File does not exist");
        }
        else if (!is_readable($filePath))
        {
            throw new \InvalidArgumentException("File to download is not readable.");
        }

        return new FileDownload(fopen($filePath, "rb"));
    }



    /**
     * Creates a new file download helper with a given content
     *
     * @static
     *
     * @param string $content the file content
     *
     * @return FileDownload
     */
    public static function createFromString ($content)
    {
        $file = tmpfile();
        fwrite($file, $content);

        return new FileDownload($file);
    }
}