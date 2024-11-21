<?php

namespace Application\Opermitcare\Permit\Form;

use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class PermitForm extends Form implements InputFilterProviderInterface
{
    /**
     * PermitForm constructor.
     */
    public function __construct()
    {
        parent::__construct('permit');
    }

    public function init() : void
    {
        $this->setName('permitForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'permitId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'residentId',
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'agentId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'businessEntityType',
                       'options' => [
                           'label' => 'Business Entity Type',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'lastName',
                       'options' => [
                           'label' => 'Last Name',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'firstName',
                       'options' => [
                           'label' => 'First Name',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'middleName',
                       'options' => [
                           'label' => 'Middle Name',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'lessorName',
                       'options' => [
                           'label' => 'lessorName',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'corporateName',
                       'options' => [
                           'label' => 'Corporate Name',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'tradeName',
                       'options' => [
                           'label' => 'Trade Name',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'businessAddress',
                       'options' => [
                           'label' => 'Business Address',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'natureOfBusiness',
                       'options' => [
                           'label' => 'Nature Of Business',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'area',
                       'options' => [
                           'label' => 'Area (sqm)',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'capital',
                       'options' => [
                           'label' => 'Capital',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'monthlyRent',
                       'options' => [
                           'label' => 'Monthly Rent',
                       ],
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'permitStatusId',
                       'options' => [
                           'label' => 'Permit Status Id',
                       ],
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
                       'type' => Hidden::class,
                       'name' => 'dateCreated',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'remarks',
                       'options' => [
                           'label' => 'Remarks',
                       ],
                   ]);
        $this->add([
                       'type' => File::class,
                       'name' => 'attachment',
                       'options' => [
                           'label' => 'Attachment (s)',
                       ],
                       'attributes' => [
                           'type'     => 'file',
                           'multiple' => 'true'
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

    public function getInputFilterSpecification() : array
    {
        return [
            [
                'name' => 'residentId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}
