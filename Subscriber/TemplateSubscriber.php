<?php
/**
 * Created by PhpStorm.
 * User: hauke
 * Date: 13.07.18
 * Time: 10:19
 */
namespace McOgImages\Subscriber;

use Enlight\Event\SubscriberInterface;

class TemplateSubscriber implements SubscriberInterface
{
    private $pluginDir;
    private $tplManager;

    public function __construct($pluginDir, \Enlight_Template_Manager $tplManager)
    {

            $this->pluginDir = $pluginDir;
            $this->tplManager = $tplManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend_Detail' => 'onPreDispatchFEDetail',
            'Enlight_Controller_Action_PreDispatch_Frontend_Listing' => 'onPreDispatchFEDetail',
            'Enlight_Controller_Action_PreDispatch_Frontend_Campaign'=> 'onPreDispatchFEDetail'
        ];
    }



    public function onPreDispatchFEDetail(){

        $this->tplManager->addTemplateDir($this->pluginDir.'/Resources/views');
    }
}
