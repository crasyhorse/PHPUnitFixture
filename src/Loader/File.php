<?php

namespace CrasyHorse\Testing\Loader;

use Carbon\Carbon;

/**
 * This class represents a file read by a Loader class. It provides methods to
 * access the file's attributes like filename, size or mime type.
 *
 * @author Florian Weidinger
 * @since 0.1.0
 */
class File
{
    /**
     * The contents of the file. This may be some type of string or binary content.
     *
     * @var mixed
     */
    protected $content;

    /**
     * The file size attribute.
     *
     * @var float
     */
    protected $size;

    /**
     * The file's mime type, e. g. 'applicaion/json'
     *
     * @var string|null
     */
    protected $mimeType;

    /**
     * The name of the file.
     *
     * @var string
     */
    protected $filename;

    /**
     *  The path to the file without the filename.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The file extension
     *
     * @var string
     */
    protected $extension;

    /**
     * Carbon object of the file's timestamp
     *
     * @var \Carbon\Carbon
     */
    protected $timestamp;

    public function __construct(string $filename, string $content, string $path = null, float $size = 0.0, string $mimeType = null, int $timestamp = 0)
    {
        $this->setFilename($filename);
        $this->path = $path;
        $this->content = $content;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->setTimestamp($timestamp);
    }

    /*
     * Getter / Setter
     */

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function setSize(float $size): void
    {
        $this->size = $size;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Sets the filename and cuts of its extension.
     *
     * @param string $filename The relative path to the file.
     *
     * @return void
     */
    public function setFilename(string $filename): void
    {
        $pathInfo = pathinfo($filename);

        $this->filename = $pathInfo['filename'] ?? '';
        $this->extension = $pathInfo['extension'] ?? '';
    }

    /**
     * Returns the file path without the filename.
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * Returns the file's mime type, e. g. 'application/json'.
     *
     * @return string|null
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = Carbon::createFromTimestamp($timestamp);
    }
}
