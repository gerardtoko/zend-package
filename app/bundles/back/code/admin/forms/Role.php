<?php

class Admin_Form_Role extends Zend_Form {

    public function init() {

        $this->setMethod('post');
        $this->setAttrib('class', 'form');

        $translate = Zend_Registry::get('translate');
        $t_name = ucfirst($translate->_('name'));

        $name = new Zend_Form_Element_Text('name');
        $name->setAttrib('size', 40)
                ->addValidator('StringLength', false, array(3, 100))
                ->addValidator("NotEmpty")
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setRequired(true)
                ->setLabel("$t_name")
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));


        // Add Elements
        $this->addElements(array($name));
        $this->setDecorators(array('FormElements', 'Form'));
    }

}