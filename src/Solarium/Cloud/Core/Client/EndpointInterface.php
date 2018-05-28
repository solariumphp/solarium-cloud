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

namespace Solarium\Cloud\Core\Client;

use Solarium\Core\Configurable;

/**
 * Interface for describing a SolrCloud endpoint.
 * @package Solarium\Cloud\Core\Client
 */
interface EndpointInterface
{
    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return AbstractEndpoint Provides fluent interface
     */
    public function setAuthentication($username, $password): AbstractEndpoint;

    /**
     * Get HTTP basic auth settings.
     *
     * @return array
     */
    public function getAuthentication(): array;

    /**
     * Get a random host.
     *
     * @return string
     */
    public function getSolrUrl(): string;

    /**
     * Get all Solr URLs.
     *
     * @return string[]
     */
    public function getSolrUrls(): array;

    /**
     * Get the base uri for all requests.
     *
     * Based on a random host from all hosts and the path.
     *
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * Get the server uri
     *
     * @return string
     */
    public function getServerUri(): string;

    /**
     * Get Solr timeout option.
     *
     * @return string
     */
    public function getTimeout(): string;

    /**
     * Set Solr timeout option.
     *
     * @param int $timeout
     *
     * @return Configurable Provides fluent interface
     */
    public function setTimeout($timeout): Configurable;
}