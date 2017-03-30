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
use Solarium\Cloud\Core\Zookeeper\ZkStateReader;
use Solarium\Cloud\Exception\ZookeeperException;
use Solarium\Core\Client\AbstractEndpoint;
use Solarium\Core\Configurable;

// TODO Add a way to set select or update type, this will choose shard leaders or any of the nodes
// TODO Load balancing in the Endpoint?
/**
 * Class for describing a SolrCloud collection endpoint.
 * @package Solarium\Cloud\Core\Client
 */
class CollectionEndpoint extends Configurable // TODO implements EndpointInterface
{
    /** @var  */
    //protected $type = AbstractEndpoint::SOLRCLOUD;
    /** @var  string Name of the collection */
    protected $collection;

    /** @var  ZkStateReader */
    protected $zkStateReader;

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
     * @param string             $collection
     * @param ZkStateReader      $zkStateReader
     * @param array|\Zend_Config $options
     */
    public function __construct(string $collection, ZkStateReader &$zkStateReader, array $options = null)
    {
        $this->collection = $collection;
        $this->zkStateReader = $zkStateReader;
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
    public function getCollection(): string
    {
        return $this->collection;
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
     * Get the base uri for all requests.
     *
     * Based on host, path, port and core/collection options.
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        $uri = $this->getServerUri().$this->zkStateReader->getCollectionName($this->collection).'/';

        return $uri;
    }

    /**
     * Get the server uri
     *
     * @return string
     */
    public function getServerUri(): string
    {
        return $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/';
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
     * @return CollectionEndpoint Provides fluent interface
     */
    public function setTimeout($timeout)
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__.'::__toString'."\n".'base uri: '.$this->getBaseUri()."\n".'host: '.$this->getHost()."\n".'port: '.$this->getPort()."\n".'path: '.$this->getPath()."\n".'collection: '.$this->getCore()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);

        return $output;
    }

    /**
     * @return CollectionState
     */
    protected function getCollectionState(): CollectionState
    {
        return $this->zkStateReader->getCollectionState($this->collection);
    }

    /**
     *
     */
    protected function randomNodeBaseUri()
    {
        $nodesBaseUris = $this->zkStateReader->getCollectionState($this->collection)->getNodesBaseUris();
        shuffle($nodesBaseUris);
        $parseUri = parse_url(reset($nodesBaseUris));
        $this->scheme = $parseUri['scheme'];
        $this->host = $parseUri['host'];
        $this->port = $parseUri['port'];
        $this->path = $parseUri['path'];
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
        $this->randomNodeBaseUri();
    }
}
