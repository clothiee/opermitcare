<?php

namespace Application\User\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;

class UserForm extends Form implements InputFilterProviderInterface
{
    /**
     * UserForm constructor.
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    public function init() : void
    {
        $this->setName('userForm')
             ->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('enctype', 'multipart/form-data');
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'userId',
                   ]);
        $this->add([
                       'type' => Text::class,
                       'name' => 'userName',
                       'options' => [
                           'label' => 'Username',
                       ],
                   ]);
        $this->add([
                       'type' => Password::class,
                       'name' => 'password',
                       'options' => [
                           'label' => 'Password',
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
                       'name' => 'lastName',
                       'options' => [
                           'label' => 'Last name',
                       ],
                   ]);
        $this->add([
                       'type' => Email::class,
                       'name' => 'email',
                       'options' => [
                           'label' => 'Email',
                       ],
                   ]);
        $this->add([
                       'type' => Hidden::class,
                       'name' => 'userTypeId',
                   ]);
        $this->add([
                       'name' => 'submit',
                       'type' => Submit::class,
                       'attributes' => [
                           'value' => 'Sign Up',
                       ],
                   ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name' => 'userName',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'firstName',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'lastName',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'email',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                ],
            ],
            [
                'name' => 'userTypeId',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}
