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

namespace Solarium\Cloud\Component\Admin\Collections\RequestBuilder;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;

class ClusterStatus implements ComponentRequestBuilderInterface
{

    /**
     * Add request settings for the debug component.
     *
     * @param \Solarium\Cloud\Component\Admin\Collections\ClusterStatus $component
     * @param Request $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        $request->addParam('action', 'CLUSTERSTATUS');
        if($component->getCollection() != null) {
            $request->addParam('collection', $component->getCollection());
        }
        if($component->getShard() != null) {
            $request->addParam('shard', $component->getShard());
        }
        if($component->getRoute() != null) {
            $request->addParam('_route_', $component->getRoute());
        }

        return $request;
    }
}