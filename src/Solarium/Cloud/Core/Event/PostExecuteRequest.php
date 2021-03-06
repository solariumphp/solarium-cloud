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

namespace Solarium\Cloud\Core\Event;

use Solarium\Cloud\Core\Client\EndpointInterface;
use Symfony\Component\EventDispatcher\Event;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;

/**
 * PostExecuteRequest event, see Events for details.
 */
class PostExecuteRequest extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EndpointInterface
     */
    protected $endpoint;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Event constructor.
     *
     * @param Request            $request
     * @param EndpointInterface $endpoint
     * @param Response           $response
     */
    public function __construct(Request $request, EndpointInterface $endpoint, Response $response)
    {
        $this->request = $request;
        $this->endpoint = $endpoint;
        $this->response = $response;
    }

    /**
     * Get the endpoint object for this event.
     *
     * @return EndpointInterface
     */
    public function getEndpoint(): \Solarium\Cloud\Core\Client\EndpointInterface
    {
        return $this->endpoint;
    }

    /**
     * Get the response object for this event.
     *
     * @return Response|null
     */
    public function getResponse() //: ?\Solarium\Core\Client\Response
    {
        return $this->response;
    }

    /**
     * Get the request object for this event.
     *
     * @return Request
     */
    public function getRequest(): \Solarium\Core\Client\Request
    {
        return $this->request;
    }
}
