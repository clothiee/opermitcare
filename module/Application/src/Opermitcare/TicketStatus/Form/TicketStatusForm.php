<?php

namespace Application\Opermitcare\TicketStatus\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class TicketStatusForm extends Form implements InputFilterProviderInterface
{
    /**
     * TicketStatusForm constructor.
     */
    public function __construct()
    {
        parent::__construct('ticketstatus');
    }

    public function init() : void
    {
        $this->setName('ticketStatusForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'ticketStatusId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'ticketStatusName',
                       'options' => [
                           'label' => 'Ticket Status Name',
                       ],
                   ]);
        $this->add([
                       'type' => Select::class,
                       'name' => 'active',
                       'options' => [
                           'label' => 'Active',
                       ],
                   ]);
        $this->add([
                       'name' => 'submit',
                       'type' => Submit::class,
                       'attributes' => [
                           'value' => 'Save',
                       ],
                   ]);
    }

    public function getInputFilterSpecification() : array
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
                'name' => 'ticketStatusName',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'active',
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}
