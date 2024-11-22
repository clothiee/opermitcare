<?php

namespace Application\Opermitcare\FaqDetails\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class FaqDetailsForm extends Form implements InputFilterProviderInterface
{
    /**
     * FaqDetailsForm constructor.
     */
    public function __construct()
    {
        parent::__construct('faqdetails');
    }

    public function init()
    : void
    {
        $this->setName('faqDetailsForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'faqDetailsId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'faqId',
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

    public function getInputFilterSpecification()
    : array
    {
        return [
            [
                'name' => 'faqDetailsId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'faqId',
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
            [
                'name' => 'active',
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}
