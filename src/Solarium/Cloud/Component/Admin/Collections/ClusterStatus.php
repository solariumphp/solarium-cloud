<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2018 Jeroen Steggink
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

namespace Solarium\Cloud\Component\Admin\Collections;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\ResponseParser\ComponentParserInterface;

use Solarium\Cloud\Component\Admin\Collections\RequestBuilder\ClusterStatus as RequestBuilder;
use Solarium\Cloud\Component\Admin\Collections\ResponseParser\ClusterStatus as ResponseParser;

/**
 * @see https://lucene.apache.org/solr/guide/collections-api.html#clusterstatus
 * @package Solarium\Cloud\Component\Admin\Collections
 */
class ClusterStatus extends AbstractComponent
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'collections',
        'omitheader' => true,
    ];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareCollectionsInterface::CLUSTERSTATUS;
    }

    /**
     * Get the request builder class for this query.
     *
     * @return ComponentRequestBuilderInterface
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return ComponentParserInterface
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Get collection
     *
     * @return string|null
     */
    public function getCollection() {
        return $this->getOption("collection");
    }

    /**
     * Get shard
     *
     * @return string|null
     */
    public function getShard() {
        return $this->getOption("shard");
    }

    /**
     * Get route
     *
     * @return string|null
     */
    public function getRoute() {
        return $this->getOption("_route_");
    }

    /**
     * The collection name for which information is requested.
     * If omitted, information on all collections in the cluster will be returned.
     *
     * @param string $collection
     * @return self Provides fluent interface
     */
    public function setCollection($collection)
    {
        return $this->setOption('collection', $collection);
    }

    /**
     * The shard(s) for which information is requested. Multiple shard names can be specified as a comma-separated list.
     *
     * @param string $shard
     * @return self Provides fluent interface
     */
    public function setShard($shard) {
        return $this->setOption("shard", $shard);
    }

    /**
     * This can be used if you need the details of the shard where a particular document belongs to and you don’t
     * know which shard it falls under.
     *
     * @param $route
     * @return self Provides fluent interface
     */
    public function setRoute($route) {
        return $this->setOption("_route_", $route);
    }
}