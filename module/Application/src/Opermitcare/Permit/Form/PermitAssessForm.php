<?php

namespace Application\Opermitcare\Permit\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class PermitAssessForm extends Form implements InputFilterProviderInterface
{
    /**
     * PermitAssessForm constructor.
     */
    public function __construct()
    {
        parent::__construct('permitAssess');
    }

    public function init() : void
    {
        $this->setName('permitAssessForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'permitId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'agentId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'permitStatusId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'mayorsFee',
                       'options' => [
                           'label' => 'Mayor\'s Fee',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'licenseFee',
                       'options' => [
                           'label' => 'License Fee',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'garbageFee',
                       'options' => [
                           'label' => 'Garbage Fee',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'zoningFee',
                       'options' => [
                           'label' => 'Zoning Fee',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'processingFee',
                       'options' => [
                           'label' => 'Processing Fee',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'remarks',
                       'options' => [
                           'label' => 'Remarks',
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
                           'value' => 'Confirm',
                       ],
                   ]);
    }

    public function getInputFilterSpecification() : array
    {
        return [
            [
                'name' => 'processingFee',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            [
                'name' => 'remarks',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
        ];
    }
}
