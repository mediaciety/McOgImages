<?php

namespace McOgImages\Resources\Helper;

use Shopware\Models\Media\Media;
use Intervention\Image\ImageManager;



class OgImage{

    protected $imgSize;
    protected $imgBackground;
    protected $imgForeground;
    protected $mediaService;
    protected $fileName;
    protected $useBlur;

    public function __construct()
    {
        $this->mediaService = Shopware()->Container()->get('shopware_media.media_service');
    }

    public function createOgImage(){
        $dim = explode('x', $this->getImgSize());

        $imgManager = new ImageManager([
            'driver' => 'gd',
        ]);


        $fImage = $imgManager->make($this->mediaService->read($this->getImgForeground()));

        if($fImage->width() > $fImage->height()){
            //landscape
            $fImage->resize($dim[0] - 200, null, function($constraint){
               $constraint->aspectRatio();
            });
        } else {
            $fImage->resize(null, $dim[1] - 200, function($constraint){
                $constraint->aspectRatio();
            });
        }


        $image = $imgManager->make($this->mediaService->read($this->getImgBackground()))
            ->resize($dim[0], $dim[1]);

        if($this->getUseBlur() > 0){
           $image = $image->blur($this->getUseBlur());
        }
        $image = $image->insert($fImage, 'center')
        ->stream('png', 100);

        $this->mediaService->write($this->getFileName(), $image);

        return null;
    }

    /**
     * @param mixed $imgBackground
     */
    public function setImgBackground($imgBackground)
    {
        $this->imgBackground = $imgBackground;
    }

    /**
     * @param mixed $imgForeground
     */
    public function setImgForeground($imgForeground)
    {
        $this->imgForeground = $imgForeground;
    }

    /**
     * @param mixed $imgSize
     */
    public function setImgSize($imgSize)
    {
        $this->imgSize = $imgSize;
    }

    /**
     * @return mixed
     */
    public function getImgSize()
    {
        return $this->imgSize;
    }

    /**
     * @return mixed
     */
    public function getImgBackground()
    {
        return $this->imgBackground;
    }

    /**
     * @return mixed
     */
    public function getImgForeground()
    {
        return $this->imgForeground;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return 'media/image/'.$this->fileName.'.png';
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getUseBlur()
    {
        if($this->useBlur >= 100){
            return 100;
        }
        return $this->useBlur;
    }

    /**
     * @param mixed $useBlur
     */
    public function setUseBlur($useBlur)
    {
        $this->useBlur = $useBlur;
    }

}