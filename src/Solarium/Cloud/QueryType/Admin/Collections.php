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

namespace Solarium\Cloud\QueryType\Admin;


use Solarium\Cloud\Client;
use Solarium\Cloud\Component\Admin\Collections\ComponentAwareCollectionsInterface;
use Solarium\Cloud\Component\Admin\Collections\Traits\ClusterStatusTrait;
use Solarium\Cloud\QueryType\Admin\Collections\RequestBuilder;
use Solarium\Cloud\QueryType\Admin\Collections\ResponseParser;
use Solarium\Component\ComponentAwareQueryTrait;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;

class Collections extends AbstractQuery implements ComponentAwareCollectionsInterface
{
    use ComponentAwareQueryTrait;
    use ClusterStatusTrait;

    protected $options = [
        'handler' => 'collections',
        'resultclass' => 'Solarium\Cloud\QueryType\Admin\Collections\Result',
        'action' => ''
    ];

    public function __construct($options = null)
    {
        $this->componentTypes = [
            ComponentAwareCollectionsInterface::CLUSTERSTATUS => 'Solarium\Cloud\Component\Admin\Collections\ClusterStatus'
        ];

        parent::__construct($options);
    }

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_ADMIN_COLLECTIONS;
    }

    /**
     * Get the requestbuilder class for this query.
     *
     * @return RequestBuilderInterface
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParserInterface
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }
}