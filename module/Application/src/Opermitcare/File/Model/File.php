<?php

namespace Application\Opermitcare\File\Model;

class File
{
    /** @var int $fileId */
    public $fileId;
    /** @var string $string */
    public $tag;
    /** @var string|null $fileName */
    public $fileName;
    /** @var string|null $filePath */
    public $filePath;
    /** @var string|null $dateCreated */
    public $dateCreated;

    public function exchangeArray($data)
    {
        $this->fileId = !empty($data['fileId']) ? $data['fileId'] : null;
        $this->tag = !empty($data['tag']) ? $data['tag'] : null;
        $this->fileName = !empty($data['fileName']) ? $data['fileName'] : null;
        $this->filePath = !empty($data['filePath']) ? $data['filePath'] : null;
        $this->dateCreated = !empty($data['dateCreated']) ? $data['dateCreated'] : null;
    }
}
