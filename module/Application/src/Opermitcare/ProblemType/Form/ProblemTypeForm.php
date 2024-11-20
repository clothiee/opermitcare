<?php

namespace Application\Opermitcare\ProblemType\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class ProblemTypeForm extends Form implements InputFilterProviderInterface
{
    /**
     * ProblemTypeForm constructor.
     */
    public function __construct()
    {
        parent::__construct('problemtype');
    }

    public function init() : void
    {
        $this->setName('problemTypeForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'problemTypeId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'problemTypeName',
                       'options' => [
                           'label' => 'Problem Type Name',
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
                'name' => 'problemTypeId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'problemTypeName',
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
