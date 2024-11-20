<?php

namespace Application\Opermitcare\File\Model;

class File
{
    /** @var int $fileId */
    public $fileId;
    /** @var int $ticketId */
    public $ticketId;
    /** @var string|null $fileName */
    public $fileName;
    /** @var string|null $filePath */
    public $filePath;

    public function exchangeArray($data)
    {
        $this->fileId = !empty($data['fileId']) ? $data['fileId'] : null;
        $this->ticketId = !empty($data['ticketId']) ? $data['ticketId'] : null;
        $this->fileName = !empty($data['fileName']) ? $data['fileName'] : null;
        $this->filePath = !empty($data['filePath']) ? $data['filePath'] : null;
    }
}
