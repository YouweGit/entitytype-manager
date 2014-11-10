<?php
/**
 * Helper to handel image processing procedure, similar to catalog Product
 *
 * @category Youwe
 * @package  Youwe_Etm
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * Current model
     *
     * @var Goodahead_Etm_Model_Entity_Image
     */
    protected $_model;


    /**
     * @var string
     */
    protected $_attribute;
    /**
     * Scheduled for resize image
     *
     * @var bool
     */
    protected $_scheduleResize = false;

    /**
     * Scheduled for rotate image
     *
     * @var bool
     */
    protected $_scheduleRotate = false;

    /**
     * Angle
     *
     * @var int
     */
    protected $_angle;

    /**
     * Watermark file name
     *
     * @var string
     */
    protected $_watermark;

    /**
     * Watermark Position
     *
     * @var string
     */
    protected $_watermarkPosition;

    /**
     * Watermark Size
     *
     * @var string
     */
    protected $_watermarkSize;

    /**
     * Watermark Image opacity
     *
     * @var int
     */
    protected $_watermarkImageOpacity;

    /**
     * Current Product
     *
     * @var Goodahead_Etm_Model_Entity
     */
    protected $_entity;

    /**
     * Image File
     *
     * @var string
     */
    protected $_imageFile;

    /**
     * Image Placeholder
     *
     * @var string
     */
    protected $_placeholder;

    /**
     * Module constract
     *
     * @param $args
     */
    public function __construct()
    {
        $this->_model = Mage::getModel('goodahead_etm/entity_image');
    }

    /**
     * Reset all previous data
     *
     * @return Goodahead_Etm_Helper_Image
     */
    protected function _reset()
    {
        $this->_scheduleResize = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_watermarkImageOpacity = null;
        $this->_entity = null;
        $this->_imageFile = null;
        return $this;
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param Varien_Object $entity
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Goodahead_Etm_Helper_Image
     */
    public function init(Varien_Object $entity, $attributeName, $imageFile=null)
    {
        $this->_reset();
        $this->setEntity($entity);
        $this->setAttribute($attributeName);
        $this->getModel()->setDestinationSubdir($this->getEntitySubdirPath($attributeName));


        $this->setWatermark(
            Mage::getStoreConfig("design/watermark/{$this->getAttribute()}_image")
        );
        $this->setWatermarkImageOpacity(
            Mage::getStoreConfig("design/watermark/{$this->getAttribute()}_imageOpacity")
        );
        $this->setWatermarkPosition(
            Mage::getStoreConfig("design/watermark/{$this->getAttribute()}_position")
        );
        $this->setWatermarkSize(
            Mage::getStoreConfig("design/watermark/{$this->getAttribute()}_size")
        );

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            // add for work original size
            $this->getModel()->setBaseFile($this->getEntity()->getData($this->getAttribute()));
        }
        return $this;
    }

    /**
     * @param Varien_Object $path
     * @param $attribute
     */
    public function getEntitySubdirPath($attribute)
    {
        $code = $this->getEntity()->getEntityTypeInstance()->getEntityTypeCode();
        return $code . DS . $attribute;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param int $width
     * @param int $height
     * @return Goodahead_Etm_Helper_Image
     */
    public function resize($width, $height = null)
    {
        $this->getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param int $quality
     * @return Goodahead_Etm_Helper_Image
     */
    public function setQuality($quality)
    {
        $this->getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @return Goodahead_Etm_Helper_Image
     */
    public function keepAspectRatio($flag)
    {
        $this->getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * $position - TODO, not used for now - picture position inside the frame.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @param array $position
     * @return Goodahead_Etm_Helper_Image
     */
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * $alphaOpacity - TODO, not used for now
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @param int $alphaOpacity
     * @return Goodahead_Etm_Helper_Image
     */
    public function keepTransparency($flag, $alphaOpacity = null)
    {
        $this->getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param bool $flag
     * @return Goodahead_Etm_Helper_Image
     */
    public function constrainOnly($flag)
    {
        $this->getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent color)
     * It is white by default.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param array $colorRGB
     * @return Goodahead_Etm_Helper_Image
     */
    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    /**
     * Rotate image into specified angle
     *
     * @param int $angle
     * @return Goodahead_Etm_Helper_Image
     */
    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $imageOpacity
     * @return Goodahead_Etm_Helper_Image
     */
    public function watermark($fileName, $position, $size=null, $imageOpacity=null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size)
            ->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Set placeholder
     *
     * @param string $fileName
     * @return void
     */
    public function placeholder($fileName)
    {
        $this->_placeholder = $fileName;
    }

    /**
     * Get Placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->getAttribute();
            $this->_placeholder = 'images/catalog/product/placeholder/'.$attr.'.jpg';
        }
        return $this->_placeholder;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $model = $this->getModel();

            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            } else {
                $model->setBaseFile($this->getEntity()->getData($this->getAttribute()));
            }

            if ($model->isCached()) {
                return $model->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $model->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $model->resize();
                }

                if ($this->getWatermark()) {
                    $model->setWatermark($this->getWatermark());
                }

                $url = $model->saveFile()->getUrl();
            }
        } catch (Exception $e) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Set current attribute
     *
     * @param string $attribute
     * @return Goodahead_Etm_Helper_Image
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get current attribute
     *
     * @return string
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Set current Image model
     *
     * @param Goodahead_Etm_Model_Entity_Image $model
     * @return Goodahead_Etm_Helper_Image
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Get current Image model
     *
     * @return Goodahead_Etm_Model_Entity_Image
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Set Rotation Angle
     *
     * @param int $angle
     * @return Goodahead_Etm_Helper_Image
     */
    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    /**
     * Get Rotation Angle
     *
     * @return int
     */
    protected function getAngle()
    {
        return $this->_angle;
    }

    /**
     * Set watermark file name
     *
     * @param string $watermark
     * @return Goodahead_Etm_Helper_Image
     */
    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        $this->getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    protected function getWatermark()
    {
        return $this->_watermark;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return Goodahead_Etm_Helper_Image
     */
    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        $this->getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    protected function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param string $size
     * @return Goodahead_Etm_Helper_Image
     */
    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        $this->getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Get watermark size
     *
     * @return string
     */
    protected function getWatermarkSize()
    {
        return $this->_watermarkSize;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return Goodahead_Etm_Helper_Image
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        $this->getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity()
    {
        if ($this->_watermarkImageOpacity) {
            return $this->_watermarkImageOpacity;
        }

        return $this->getModel()->getWatermarkImageOpacity();
    }

    /**
     * Set current Entity
     *
     * @param Varien_Object $entity
     * @return Goodahead_Etm_Helper_Image
     */
    protected function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Get current Entity
     *
     * @return Goodahead_Etm_Model_Entity
     */
    protected function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Set Image file
     *
     * @param string $file
     * @return Goodahead_Etm_Helper_Image
     */
    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    /**
     * Get Image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Retrieve size from string
     *
     * @param string $string
     * @return array|bool
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return array(
                'width' => ($size[0] > 0) ? $size[0] : null,
                'heigth' => ($size[1] > 0) ? $size[1] : null,
            );
        }
        return false;
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return array(
            $this->getOriginalWidth(),
            $this->getOriginalHeight()
        );
    }

    /**
     * Check - is this file an image
     *
     * @param string $filePath
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validateUploadFile($filePath) {
        if (!getimagesize($filePath)) {
            Mage::throwException($this->__('Disallowed file type.'));
        }

        $_processor = new Varien_Image($filePath);
        return $_processor->getMimeType() !== null;
    }

}