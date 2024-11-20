<?php

namespace Application\Opermitcare\Ticket\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class TicketForm extends Form implements InputFilterProviderInterface
{
    /**
     * TicketForm constructor.
     */
    public function __construct()
    {
        parent::__construct('ticket');
    }

    public function init() : void
    {
        $this->setName('ticketForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'ticketId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'ticketStatusId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'problemTypeId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'residentId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'dateCreated',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'title',
                       'options' => [
                           'label' => 'Title',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'description',
                       'options' => [
                           'label' => 'Description',
                       ],
                   ]);
        $this->add([
                       'name' => 'submit',
                       'type' => Submit::class,
                       'attributes' => [
                           'value' => 'Confirm',
                       ],
                   ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name' => 'ticketStatusId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'problemTypeId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'residentId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'title',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'description',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
        ];
    }
}
