<?php
/**
 * OpenSKOS
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenSKOS
 * @package    OpenSKOS
 * @copyright  Copyright (c) 2012 Pictura Database Publishing. (http://www.pictura-dp.nl)
 * @author     Alexandar Mitsev
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */

class Editor_Forms_OAuthLogin extends Zend_Form
{
    public function init()
    {
        $this->setName('oauth2login')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'login', 'action' => 'oauth2-login')))
        ->setMethod('get');

        $this->buildProvider()
        ->buildRememberMe()
        ->buildButtons();
    }
    
    protected function buildProvider()
    {
        $this->addElement('text', 'provider', array('label' => 'Provider', 'required' => true));
        return $this;
    }
    
    protected function buildRememberMe()
    {
        $this->addElement('checkbox', 'rememberme', array('label' => _('Remember me')));
        return $this;
    }
    
    protected function buildButtons()
    {
        $this->addElement('submit', 'OAuth2LoginButton', array(
                'label' => 'Log in with OAuth2',
                'decorators' => array(),
        ));
        return $this;
    }
    
    /**
     * @return Editor_Forms_OAuthLogin
     */
    public static function getInstance()
    {
        static $instance;
    
        if (null === $instance) {
            $instance = new Editor_Forms_OAuthLogin();
        }
    
        return $instance;
    }
}
