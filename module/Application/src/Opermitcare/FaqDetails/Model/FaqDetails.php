<?php

namespace Application\Opermitcare\FaqDetails\Model;

class FaqDetails
{
    /** @var int $faqDetailsId */
    public $faqDetailsId;
    /** @var int $faqId */
    public $faqId;
    /** @var string|null $title */
    public $title;
    /** @var string|null $description */
    public $description;
    /** @var int $active*/
    public $active;

    public function exchangeArray($data)
    {
        $this->faqDetailsId = !empty($data['faqDetailsId']) ? $data['faqDetailsId'] : null;
        $this->faqId = !empty($data['faqId']) ? $data['faqId'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->description = !empty($data['description']) ? $data['description'] : null;
        $this->active= !empty($data['active']) ? $data['active'] : 0;
    }
}
