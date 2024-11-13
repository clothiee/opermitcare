<?php

namespace Application\PermitStatus\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class PermitStatusForm extends Form implements InputFilterProviderInterface
{
    /**
     * UserForm constructor.
     */
    public function __construct()
    {
        parent::__construct('permitstatus');
    }

    public function init() : void
    {
        $this->setName('permitStatusForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'permitStatusId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'permitStatusName',
                       'options' => [
                           'label' => 'Permit Status Name',
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
                'name' => 'permitStatusId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'permitStatusName',
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
