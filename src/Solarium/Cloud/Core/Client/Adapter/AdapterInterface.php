<?php
/**
 * BSD 2-Clause License
 *
 * Copyright (c) 2017 Jeroen Steggink, Bas de Nooijer
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

namespace Solarium\Cloud\Core\Client\Adapter;

use Solarium\Cloud\Core\Client\AbstractEndpoint;
use Solarium\Core\ConfigurableInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Cloud\Core\Client\CollectionEndpoint;

/**
 * Interface for client adapters.
 *
 * The goal of an adapter is to accept a query, execute it and return the right
 * result object. This is actually quite a complex task as it involves the
 * handling of all Solr communication.
 *
 * The adapter structure allows for varying implementations of this task.
 *
 * Most adapters will use some sort of HTTP client. In that case the
 * query request builders and query response parsers can be used to simplify
 * HTTP communication.
 *
 * However an adapter may also implement all logic by itself if needed.
 */
interface AdapterInterface extends ConfigurableInterface
{
    /**
     * Execute a request.
     *
     * @param Request            $request
     * @param AbstractEndpoint $endpoint
     *
     * @return Response
     *
     * @throws \Solarium\Exception\HttpException
     */
    public function execute($request, $endpoint): Response;
}
