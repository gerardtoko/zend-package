<?php

class Admin_Form_Delete extends Zend_Form {

    public function init() {
        $this->setMethod('post');

        $translate = Zend_Registry::get('translate');
        $confimation_ = ucfirst($translate->_('confimation'));
        $return_ = ucfirst($translate->_('annulation'));


        //Mail
        $hidden = new Zend_Form_Element_Hidden('hidden');
        $hidden->setDecorators(array( 'ViewHelper'));

        //Delete
        $submit_yes = new Zend_Form_Element_Submit($confimation_);
        $submit_yes->setAttrib('class', 'btn btn-danger')
                   ->setDecorators(array( 'ViewHelper'));

        //
        $submit_no = new Zend_Form_Element_Submit($return_);
        $submit_no->setAttrib('class', 'btn')
                  ->setDecorators(array( 'ViewHelper'));

        // Add Elements
        $this->addElements(array($hidden, $submit_yes, $submit_no));
        $this->setDecorators(array('FormElements', 'Form' ));
   }
}