<?php

namespace Application\Opermitcare\Faq\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class FaqForm extends Form implements InputFilterProviderInterface
{
    /**
     * FaqForm constructor.
     */
    public function __construct()
    {
        parent::__construct('faq');
    }

    public function init()
    : void
    {
        $this->setName('faqForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'faqId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'faqName',
                       'options' => [
                           'label' => 'Faq Name',
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
                'name' => 'faqId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'faqName',
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
