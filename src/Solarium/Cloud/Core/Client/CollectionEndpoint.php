<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Solarium\Cloud\Core\Client;

use Solarium\Cloud\Core\Zookeeper\CollectionState;
use Solarium\Core\Configurable;


// TODO Add a way to set select or update type, this will choose shard leaders or any of the nodes
// TODO Load balancing in the Endpoint?
/**
 * Class for describing a SolrCloud collection endpoint.
 * @package Solarium\Cloud\Core\Client
 */
class CollectionEndpoint extends Configurable
{
    /** @var  string Name of the collection */
    protected $name;
    /** @var  CollectionState Collection state retrieved from ZkStateReader */
    protected $state;

    /** @var  string[]  */
    protected $leaderBaseUrls;
    /** @var  string[]  */
    protected $nodesBaseUrls;

    /** @var  string */
    protected $scheme;
    /** @var  string */
    protected $host;
    /** @var  int */
    protected $port;
    /** @var  string */
    protected $path;

    /**
     * CollectionEndpoint constructor.
     * @param array              $state   The collection state returned by ZkStateReader
     * @param array|\Zend_Config $options
     */
    public function __construct(CollectionState $state, array $options = null)
    {
        $this->state = $state;
        parent::__construct($options);
    }

    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return self Provides fluent interface
     */
    public function setAuthentication($username, $password)
    {
        $this->setOption('username', $username);
        $this->setOption('password', $password);

        return $this;
    }

    /**
     * Get HTTP basic auth settings.
     *
     * @return array
     */
    public function getAuthentication()
    {
        return array(
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        );
    }

    /**
     * Name of the collection.
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->name;
    }

    /**
     * Get host option.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Name of the collection.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get path option.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get port option.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get scheme option.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Get the base url for all requests.
     *
     * Based on scheme, host, port and path
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/';

        return $url;
    }

    /**
     * Get the collection url for all requests.
     *
     * Based on scheme, host, port and path
     *
     * @return string
     */
    public function getCollectionUrl()
    {
        $url = $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/'.$this->getName().'/';

        return $url;
    }

    /**
     * Get timeout option.
     *
     * @return string
     */
    public function getTimeout()
    {
        return $this->getOption('timeout');
    }

    /**
     * Set timeout option.
     *
     * @param int $timeout
     *
     * @return self Provides fluent interface
     */
    public function setTimeout($timeout)
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: url resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__.'::__toString'."\n".'base url: '.$this->getBaseUrl()."\n".'host: '.$this->getHost()."\n".'port: '.$this->getPort()."\n".'path: '.$this->getPath()."\n".'collection: '.$this->getCore()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);

        return $output;
    }

    /**
     * @return CollectionState
     */
    protected function getCollectionState(): CollectionState
    {
        return $this->state;
    }

    /**
     *
     */
    protected function setRandomNodeBaseUrl()
    {
        shuffle($this->nodesBaseUrls);
        $randomUrl = reset($this->nodesBaseUrls);
        $parseUrl = parse_url($randomUrl);
        $this->scheme = $parseUrl['scheme'];
        $this->host = $parseUrl['host'];
        $this->port = $parseUrl['port'];
        $this->path = $parseUrl['path'];
    }

    /**
     * Initialization hook.
     *
     * In this case the path needs to be cleaned of trailing slashes.
     *
     * @see setPath()
     */
    protected function init()
    {
        $this->getCollectionState();
        $this->name = $this->state->getName();
        $this->leaderBaseUrls = $this->state->getShardLeadersBaseUrls();
        $this->nodesBaseUrls = $this->state->getNodesBaseUrls();
        $this->setRandomNodeBaseUrl();
    }
}