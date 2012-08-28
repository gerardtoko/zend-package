<?php

class Admin_Connect_Form_Login extends Zend_Form {

    public function init() {

        $this->setMethod('post');
        $this->setAction("");

        $translate = Zend_Registry::get('translate');
        $t_name = ucfirst($translate->_('name'));
        $t_password = ucfirst($translate->_('password'));

        //Mail
        $name = new Zend_Form_Element_Text('name');
        $name->setAttrib('size', 30)
                ->setAttrib('maxLength', 45)
                ->setAttrib('placeholder', $t_name)
                ->setAttrib('class', 'input-xlarge')
                ->addValidator('StringLength', false, array(2, 20))
                ->addValidator("NotEmpty")
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setRequired(true);

        //Password
        $password = new Zend_Form_Element_Password('password');
        $password->setAttrib('size', 30)
                ->setAttrib('class', 'input-xlarge')
                ->setAttrib('placeholder', $t_password)
                ->setRequired(true);


        //Submit
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib("class", 'btn');

        // Add Elements
        $this->addElements(array($name, $password, $submit));
    }

}