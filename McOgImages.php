<?php

namespace McOgImages;

use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;


/**
 *
 */


if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class McOgImages extends Plugin
{
    protected $conf;

  public function install(InstallContext $context){
        $attrService = $this->container->get('shopware_attribute.crud_service');
        $attrService->update('s_articles_attributes', 'ogimage', 'string', [
            'label' => 'OG Image',
            'supportText' => '',
            'helpText' => '',
            'displayInBackend' => true,
        ]);
        $attrService->update('s_categories_attributes', 'ogimage', 'single_selection', [
           'label' => 'OG Image',
           'entity' => 'Shopware\Models\Media\Media',
           'supportText' => '',
           'helpText' => '',
           'displayInBackend' => true,
        ]);
        $attrService->update('s_emotion_attributes', 'ogimage', 'single_selection', [
          'label' => 'OG Image',
          'entity' => 'Shopware\Models\Media\Media',
          'supportText' => '',
          'helpText' => '',
          'displayInBackend' => true,
      ]);

      $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
      $metaDataCache->deleteAll();
      Shopware()->Models()->generateAttributeModels(['s_articles_attributes', 's_emotion_attributes', 's_categories_attributes']);
  }

  public static function getSubscribedEvents()
  {
      return [
          'Enlight_Controller_Action_PostDispatch_Frontend_Listing' => 'onPostDispatchListing',
          'Enlight_Controller_Action_PostDispatch_Frontend_Campaign' => 'onPostDispatchCampaign',
          'Enlight_Controller_Action_PostDispatch_Frontend_Detail' => 'onPostDispatchDetail'
      ];
  }

    public function uninstall(UninstallContext $context){
      $service = $this->container->get('shopware_attribute.crud_service');
      $service->delete('s_articles_attributes', 'ogimage');
      $service->delete('s_categories_attributes', 'ogimage');
      $service->delete('s_emotion_attributes', 'ogimage');
  }



  //listeners
    public function onPostDispatchDetail(\Enlight_Event_EventArgs $ea){
      $controller = $ea->getSubject();
      $view = $controller->View();

      $view->assign(['ogimageDimensions' => $this->getConfigDimensions(), 'fbAppId' => $this->getFbAppId()]);
    }

    public function onPostDispatchListing(\Enlight_Event_EventArgs $ea){
      $controller = $ea->getSubject();
      $view = $controller->View();

      $context = $this->container->get('shopware_storefront.context_service')->getContext();

      $ogimages = null;

      if($view->hasEmotion){
          foreach($view->emotions as $emotion){
              $ogimageIds[] = $this->getEmotionOgImages($emotion['id']);
          }
          $ogimageIds = array_filter($ogimageIds);
          $ogimages = $this->getImagePath($ogimageIds, $context);
      } else {
          $ogimages = $this->getImagePath([$view->sCategoryContent['attribute']['ogimage']], $context);
      }

        //die('<pre>'.print_r(count($ogimages), true).'</pre>');

      if(count($ogimages) == 0){
          $ogimages[] = $this->_getConfig('ogDefaultImage');
      }

        $view->assign(['ogimages' => $ogimages, 'ogimageDimensions' => $this->getConfigDimensions(), 'fbAppId' => $this->getFbAppId()]);
    }
    public function onPostDispatchCampaign(\Enlight_Event_EventArgs $ea){
        $controller = $ea->getSubject();
        $context = $this->container->get('shopware_storefront.context_service')->getContext();

        $view = $controller->View();
        foreach($view->landingPage['emotions'] as $emotion){
            $ogimageIds[] = $this->getEmotionOgImages($emotion['id']);
            $ogimageIds = array_filter($ogimageIds);
            $ogimages = $this->getImagePath($ogimageIds, $context);
        }
        $ogimages = array_filter($ogimages);



        $view->assign(['ogimages' => $ogimages, 'ogimageDimensions' => $this->getConfigDimensions(), 'fbAppId' => $this->getFbAppId()]);
    }
    private function getImagePath(Array $ids, $context){
        $mediaService = $this->container->get('shopware_storefront.media_service');
        $images = [];
        foreach($ids as $id){
            $image = $mediaService->get($id, $context);
            //die('<pre>'.print_r($image, true).'</pre>');
            if($image){
                $images[] = $image->getPath();
            } else{
                //die('<pre>'.print_r($id, true).'</pre>');
                $images = $id;
            }
        }


        return $images;

    }
    private function getEmotionOgImages($emotionId){
      $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();
          $queryBuilder->select('ogimage')
          ->from('s_emotion_attributes')
          ->where('emotionId = :eId')
          ->andWhere($queryBuilder->expr()->isNotNull('ogimage'))
          ->setParameter('eId', $emotionId);


          $queryResults = $queryBuilder->execute()->fetch(\PDO::FETCH_COLUMN);

          if(!$queryResults){
              $queryResults[] = $this->getDefaultBackground();
          }

          return $queryResults;
    }

    private function getDefaultBackground(){
        $conf = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName(),$this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault());
        $mc = $this->container->get('shopware_storefront.media_service');


        return $conf['ogDefaultImage'];
    }
    private function getConfigDimensions(){
        $conf = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName(),$this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault());

        return explode('x', $conf['ogImageSize']);
    }
    private function getFbAppId(){
        $conf = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName(),$this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault());

        return $conf['fbAppId'];
    }
    private function _getConfig(String $key){

        $conf = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName(),$this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault());
        return $conf[$key];
    }
}
