<?php

/**
 * This File is part of the vendor\thapp\src\Thapp\XsltBridge\Engines package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XsltBridge\Engines;

use \XSLTProcessor;
use Thapp\XsltBridge\XMLBuilder;
use Thapp\XsltBridge\XSLTBridge;
use Illuminate\View\Engines\EngineInterface;

/**
 * Class: XslEngine
 *
 * @implements EngineInterface
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XslEngine implements EngineInterface
{
    /**
     * builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * globalData
     *
     * @var mixed
     */
    protected $globalData;

    /**
     * __construct
     *
     * @param XMLBuilder $builder
     * @param array $globalData
     * @access public
     * @return void
     */
    public function __construct(XMLBuilder $builder, XSLTBridge $processor, array $globalData)
    {
        $this->builder     = $builder;
        $this->globalData  = $globalData;
        $this->processor   = $processor;
    }

    /**
     * getData
     *
     * @param array $data
     * @access public
     * @return array
     */
    public function getData(array $data = array())
    {
        return array_merge(array('params' => $this->globalData), $data);
    }

    /**
     * getGlobalData
     *
     * @param array $data
     * @access public
     * @return array
     */
    public function getGlobalData()
    {
        return $this->globalData;
    }

    /**
     * get
     *
     * @param mixed $path
     * @param array $data
     * @access public
     * @return string
     */
    public function get($path, array $data = array())
    {
        // File we want to load
        $file = realpath($path);
        $filename = pathinfo($file, PATHINFO_BASENAME);
        // We need to move the directory requested as the first search path
        // this stops conflicts. For example, with packages
        $path = pathinfo($file, PATHINFO_DIRNAME);
        $paths[] = $path;

        $this->processor->loadXSL($file);

        $this->processor->setParameters($this->getGlobalData());

        // Render template
        $this->builder->load($this->getData($data));
        return $this->processor->render($this->builder->createXML());
    }
}