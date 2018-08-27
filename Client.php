<?php

namespace iost\soapclient;

use SoapClient;
use SoapFault;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * The SOAP Client class.
 *
 * Note, PHP SOAP extension is required.
 *
 * @author Alexander Mohorev <dev.mohorev@gmail.com>
 * @author Mikhail Iost <mikhail.iost@gmail.com>
 */
class Client extends Component
{
    /**
     * @var string $url the URL of the WSDL file.
     */
    public $url;
    /**
     * @var string $location the URL end-point service.
     */
    public $location;
    /**
     * @var array the array of SOAP client options.
     */
    public $options = [];

    /**
     * @var SoapClient the SOAP client instance.
     */
    private $_client;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->url === null) {
            throw new InvalidConfigException('The "url" property must be set.');
        }
        try {
            $this->_client = new SoapClient($this->url, $this->options);
            if (!empty($this->location)) {
                $this->_client->__setLocation($this->location);
            }
        } catch (SoapFault $e) {
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        try {
            return call_user_func_array([$this->_client, $name], $arguments);
        } catch (SoapFault $e) {
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
