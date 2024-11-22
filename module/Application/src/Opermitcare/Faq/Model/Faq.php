<?php

namespace Application\Opermitcare\Faq\Model;

class Faq
{
    /** @var int $faqId */
    public $faqId;
    /** @var string|null $faqName */
    public $faqName;
    /** @var int $active*/
    public $active;

    public function exchangeArray($data)
    {
        $this->faqId = !empty($data['faqId']) ? $data['faqId'] : null;
        $this->faqName = !empty($data['faqName']) ? $data['faqName'] : null;
        $this->active= !empty($data['active']) ? $data['active'] : 0;
    }
}
