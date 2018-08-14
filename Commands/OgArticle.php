<?php

namespace McOgImages\Commands;

use McOgImages\Resources\Helper\OgImage;
use Shopware\Commands\ShopwareCommand;
use Shopware\Components\Api\Exception\NotFoundException;
use Shopware\Components\Api\Exception\ParameterMissingException;
use Shopware\Components\Api\Exception\ValidationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\Api\Manager;
use Shopware\Components\Api\Resource\Resource;
use Symfony\Component\Console\Helper\ProgressBar;

class OgArticle extends ShopwareCommand{

    protected $articleResource;
    protected $mediaResource;
    protected $mediaService;
    protected $backgroundImage;
    protected $pluginConfig;

    protected function configure()
    {
        $this->setName('mc:ogimages:article:full');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->articleResource = Manager::getResource('article');
        $this->mediaResource = Manager::getResource('media');
        $this->mediaService = Shopware()->Container()->get('shopware_media.media_service');

        $shop = $this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('McOgImages', $shop);

        $this->backgroundImage = Shopware()->DocPath().$this->mediaService->normalize($this->pluginConfig['ogImageBackgroundFile']);

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();


        $queryBuilder->select('s_articles.id, media_id, ordernumber')
            ->from('s_articles')
            ->leftJoin('s_articles', 's_articles_img', 'sai', 's_articles.id = sai.articleid')
            ->leftJoin('s_articles', 's_articles_details', 'sad', 's_articles.id = sad.articleid')
            ->where('main  = 1');


        $articles = $queryBuilder->execute()->fetchAll();

        $ogImage = new OgImage();
        $ogImage->setImgSize($this->pluginConfig['ogImageSize']);
        $ogImage->setImgBackground($this->backgroundImage);
        $ogImage->setUseBlur($this->pluginConfig['ogBackgroundBlur']);
        $total = count($articles);
        $pb = new ProgressBar($output, $total);

        $output->writeln("<info>Article's OG:Images</info>");
        foreach ($articles as $article){

            $articleImage = $this->mediaResource->getOne($article['media_id'])['path'];
            $ogImage->setImgForeground($this->mediaService->normalize($articleImage));
            $ogImage->setFileName($article['ordernumber']);
            $ogImage->createOgImage();


            try {
                $this->articleResource->update($article['id'], [
                    'mainDetail' => [
                        'attribute' => [
                            'ogimage' => $ogImage->getFileName(),
                        ],
                    ],
                ]);

                $pb->advance();
            } catch (NotFoundException $e) {
                $output->writeln($e->getMessage());
            } catch (ParameterMissingException $e) {
                $output->writeln($e->getMessage());
            } catch (ValidationException $e) {
                $output->writeln($e->getMessage());
            } catch (Exception $e){
                $output->writeln($e->getMessage());
                break;
            }



        }
        $pb->finish();
        $output->writeln('');


    }

}