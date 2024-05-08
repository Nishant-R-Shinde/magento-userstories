<?php
namespace Mage2tv\Userstory25\Plugin;
use MagicToolbox\Magic360\Block\Product\View\Gallery;
class GetFirstImagePath extends Gallery{

    public function aroundGetGalleryImagesJson(Gallery $subject, callable $proceed) {
        $imagesItems = [];
        $product = $subject->getProduct();
        $magic360Slide = null;

        if (!$subject->isStandaloneMode()) {
            $images = $subject->getGalleryImagesCollection($product);
            
            $firstImage = $images->getItems()[array_keys($images->getItems())[0]]->getData()['medium_image_url'];
            if ($images->count()) {
                $magic360Icon = $firstImage;
                
                $id = $product->getId();
                $magic360Slide = [
                    'magic360' => 'Magic360-product-'.$id,
                    'thumb' => $magic360Icon,
                    'img' => $magic360Icon,
                    'html' => '<div class="fotorama__select">'.$subject->renderedGalleryHtml[$id].'</div>',
                    'caption' => '',
                    'position' => $subject->spinPosition,
                    'isMain' => true,
                    'type' => 'magic360',
                    'videoUrl' => null,
                    'fit' => 'none',
                ];
            }
        }

        $productImages = $subject->getGalleryImages() ?: [];
        $productMainImage = $product->getImage();
        $productName = $product->getName();
        $isMain = !($magic360Slide);
        $position = 0;
        foreach ($productImages as $image) {
            if ($position == $subject->spinPosition) {
                if ($magic360Slide) {
                    $imagesItems[$position] = $magic360Slide;
                    $magic360Slide = null;
                    $position++;
                }
            }
            $imagesItems[$position] = [
                'thumb' => $image->getData('small_image_url'),
                'img' => $image->getData('medium_image_url'),
                'full' => $image->getData('large_image_url'),
                'caption' => ($image->getLabel() ?: $productName),
                'position' => $position,
                'isMain' => ($isMain && ($image->getFile() == $productMainImage)),
                'type' => str_replace('external-', '', $image->getMediaType()),
                'videoUrl' => $image->getVideoUrl(),
            ];
            $position++;
        }

        if ($magic360Slide) {
            $imagesItems[$position] = $magic360Slide;
        }

        if (empty($imagesItems)) {
            $imagesItems[] = [
                'thumb' => $subject->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $subject->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $subject->_imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => 0,
                'isMain' => true,
                'type' => 'image',
                'videoUrl' => null,
            ];
        }

        return json_encode($imagesItems);
    }
}