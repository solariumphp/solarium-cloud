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

use Solarium\Cloud\Core\Zookeeper\ClusterState;
use Solarium\Cloud\Core\Zookeeper\StateReaderInterface;
use Solarium\Cloud\Exception\SolrCloudException;

// TODO Add a way to set select or update type, this will choose shard leaders or any of the nodes
// TODO Load balancing in the Endpoint?
/**
 * Class for describing a SolrCloud collection endpoint.
 * @package Solarium\Cloud\Core\Client
 */
class CollectionEndpoint extends AbstractEndpoint
{
    /** @var  string Name of the collection */
    protected $collection;

    /** @var  StateReaderInterface */
    protected $stateReader;

    /**
     * Default options.
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $options = array(
        'timeout' => 5,
    );

    /**
     * CollectionEndpoint constructor.
     * @param string             $collection
     * @param StateReaderInterface      $stateReader
     * @param array|\Zend_Config $options
     * @throws \Solarium\Exception\InvalidArgumentException
     */
    public function __construct(string $collection, StateReaderInterface $stateReader, array $options = null)
    {
        $this->collection = $collection;
        $this->stateReader = $stateReader;
        parent::__construct($options);
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
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__.'::__toString'."\n".'base uri: '.$this->getBaseUri()."\n".'Solr URLs: '.$this->getSolrUrls()."\n".'collection: '.$this->getCollection()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);
    }

    /**
     * @throws SolrCloudException
     * @return ClusterState
     */
    protected function getCollectionState(): ClusterState
    {
        if (!empty($this->stateReader->getClusterState()[$this->collection])) {
            return $this->stateReader->getClusterState()[$this->collection];
        }
        else {
           throw new SolrCloudException("Collection does not exist.");
        }
    }

    /**
     * Retrieve a random node, poor man's load balancer.
     * @throws SolrCloudException
     */
    protected function randomNodeBaseUri()
    {
        $nodesBaseUris = $this->getCollectionState()->getNodesBaseUris();
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
     * @throws SolrCloudException
     */
    protected function init()
    {
        $this->randomNodeBaseUri();
    }
}
