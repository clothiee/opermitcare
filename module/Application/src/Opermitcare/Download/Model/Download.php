<?php

namespace Application\Opermitcare\Download\Model;

class Download
{
    /** @var int $downloadId */
    public $downloadId;
    /** @var int $fileId */
    public $fileId;
    /** @var string|null $title */
    public $title;
    /** @var string|null $type */
    public $type;
    /** @var int $active */
    public $active;

    public function exchangeArray($data)
    {
        $this->downloadId = !empty($data['downloadId']) ? $data['downloadId'] : null;
        $this->fileId = !empty($data['fileId']) ? $data['fileId'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->type = !empty($data['type']) ? $data['type'] : null;
        $this->active = !empty($data['active']) ? $data['active'] : null;
    }
}
