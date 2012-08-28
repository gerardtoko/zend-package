<?php

class Admin_Form_User extends Zend_Form {

    public function init() {

        //Setting
        $this->setAttrib('class', 'form');
        $this->setMethod('post');

        //Traduction
        $translate = Zend_Registry::get('translate');

        $t_username = ucfirst($translate->_('username'));
        $t_firstname = ucfirst($translate->_('firstname'));
        $t_lastname = ucfirst($translate->_('lastname'));
        $t_pass = ucfirst($translate->_('pass'));
        $t_role = ucfirst($translate->_('role'));
        $t_mail = ucfirst($translate->_('email'));
        $t_pass_old = ucfirst($translate->_('current pass'));
        $t_status = ucfirst($translate->_("status"));
        $t_active = ucfirst($translate->_("active"));
        $t_inactive = ucfirst($translate->_("blocked"));

        $chevron = "<i class=\"icon-chevron-up\"></i> ";

        $t_username_description = $chevron . ucfirst($translate->_("les espaces sont autorisés ; la ponctuation n'est pas autorisée à l'exception des points, traits d'union, apostrophes et tirets bas."));
        $t_pass_description_new = $chevron . ucfirst($translate->_("saisissez un mot de passe pour le nouveau compte dans les deux champs."));
        $t_mail_description = $chevron . ucfirst($translate->_("une adresse électronique valide. Le système enverra tous les courriels à cette adresse. L'adresse électronique ne sera pas rendue publique et ne sera utilisée que pour la réception d'un nouveau mot de passe ou pour la réception de certaines notifications désirées."));
        $t_status_description = $chevron . ucfirst($translate->_("les utilisateurs qui ne sont pas activés ne seront pas autorisés à entrer dans l'administration"));
        $t_pass_description_rename = $chevron . ucfirst($translate->_("pour modifier le mot de passe actuel, saisissez le nouveau mot de passe dans les deux champs de texte."));
        $t_pass_description_old = $chevron . ucfirst($translate->_("saisissez votre mot de passe actuel pour changer votre Adresse de courriel ou votre Mot de passe."));
        $t_role_description = $chevron . ucfirst($translate->_("les roles varient en fonction des permissions, les utilisateurs qui possédent le super role seront automatiquement en status actif"));

        $req = Zend_Registry::get('reqContext');

        
        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setLabel("$t_firstname")
                ->setAttrib('maxLength', 75)
                ->addValidator('StringLength', false, array(2, 50))
                ->addValidator("NotEmpty")
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->addFilter('Alnum')
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));

        $this->addElement($firstname);



        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setLabel("$t_lastname")
                ->setAttrib('maxLength', 75)
                ->addValidator('StringLength', false, array(2, 50))
                ->addValidator("NotEmpty")
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->addFilter('Alnum')
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));
        $this->addElement($lastname);



        $username = new Zend_Form_Element_Text('username');
        $username->setLabel("$t_username")
                ->setAttrib('maxLength', 75)
                ->addValidator('StringLength', false, array(2, 50))
                ->addValidator("NotEmpty")
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setRequired(true)
                ->setDescription($t_username_description)
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('Description', array('placement' => 'append',
                            'escape' => false,
                            'tag' => 'div')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));
        $this->addElement($username);



        if ($req['act'] == 'edit') {

            $re_pass = new Zend_Form_Element_Password('pass_old');
            $re_pass->setLabel("$t_pass_old")
                    ->setAttrib('maxLength', 45)
                    ->addValidator('StringLength', false, array(4, 20))
                    ->addValidator("NotEmpty")
                    ->addFilter("StripTags")
                    ->addFilter("StringTrim")
                    ->setDescription($t_pass_description_old)
                    ->setDecorators(array(
                        'ViewHelper',
                        'Errors',
                        'Label',
                        array('Description', array('placement' => 'append',
                                'escape' => false,
                                'tag' => 'div')),
                        array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                    ));
            $this->addElement($re_pass);
        }

        $pass = new Zend_Form_Element_Password('pass');
        $pass->setLabel("$t_pass")
                ->setAttrib('maxLength', 45)
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));

        if ($req['act'] == 'add') {
            $pass->setRequired(true)
                    ->addValidator('StringLength', false, array(4, 20))
                    ->addValidator("NotEmpty");
        }

        $this->addElement($pass);

        $pass_2 = new Zend_Form_Element_Password('pass_2');
        $pass_2->setAttrib('maxLength', 45)
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setDescription($t_pass_description_new)
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    array('Description', array('placement' => 'append',
                            'escape' => false,
                            'tag' => 'div')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));

        if ($req['act'] == 'add') {
            $pass_2->setRequired(true)
                    ->addValidator('StringLength', false, array(4, 20))
                    ->addValidator("NotEmpty")
                    ->setDescription($t_pass_description_new);
        } else {
            $pass_2->setDescription($t_pass_description_rename);
        }

        $this->addElement($pass_2);

        //Mail
        $mail = new Zend_Form_Element_Text('email');
        $mail->setLabel("$t_mail")
                ->setAttrib('maxLength', 100)
                ->addValidator('StringLength', false, array(2, 100))
                ->addValidator("NotEmpty")
                ->addValidator('EmailAddress', TRUE)
                ->addFilter("StripTags")
                ->addFilter("StringTrim")
                ->setRequired(true)
                ->setDescription($t_mail_description)
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('Description', array('placement' => 'append',
                            'escape' => false,
                            'tag' => 'div')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));


        $status = new Zend_Form_Element_Select('status');
        $status->setLabel("$t_status")
                ->setMultiOptions(array('1' => $t_active, '0' => $t_inactive))
                ->setDescription($t_status_description)
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('Description', array('placement' => 'append',
                            'escape' => false,
                            'tag' => 'div')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));

        $role = new Zend_Form_Element_Multiselect('role');
        $role->setRequired(true)
                ->setLabel("$t_role:")
                ->setAttrib('size', 10)
                ->setSeparator('</br>')
                ->setDescription($t_role_description)
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    'Label',
                    array('Description', array('placement' => 'append',
                            'escape' => false,
                            'tag' => 'div')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'element'))
                ));

        $this->addElements(array($mail, $status , $role));
        $this->setDecorators(array('FormElements', 'Form'));
    }

}