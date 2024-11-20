<?php

namespace Application\Opermitcare\Reply\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class ReplyForm extends Form implements InputFilterProviderInterface
{
    /**
     * ReplyForm constructor.
     */
    public function __construct()
    {
        parent::__construct('reply');
    }

    public function init() : void
    {
        $this->setName('replyForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'replyId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'ticketId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'senderId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'message',
                       'options' => [
                           'label' => 'Message',
                       ],
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'dateCreated',
                   ]);
        $this->add([
                       'name' => 'submit',
                       'type' => Submit::class,
                       'attributes' => [
                           'value' => 'Send',
                       ],
                   ]);
    }

    public function getInputFilterSpecification() : array
    {
        return [
            [
                'name' => 'ticketId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'senderId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'message',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
        ];
    }
}
