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

namespace Solarium\Cloud\QueryType\Admin\Collections;

use Solarium\Cloud\Component\Admin\Collections\ClusterStatus;
use Solarium\Cloud\Component\Admin\Collections\ComponentAwareCollectionsInterface;
use Solarium\Cloud\Core\Zookeeper\ClusterState;
use Solarium\Core\Query\Result\QueryType;

class Result extends QueryType
{
    /**
     * Component results.
     */
    protected $components;

    /**
     * Get all component results.
     *
     * @return array
     */
    public function getComponents()
    {
        $this->parseResponse();

        return $this->components;
    }

    /**
     * Get a component result by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getComponent($key)
    {
        $this->parseResponse();

        if (isset($this->components[$key])) {
            return $this->components[$key];
        }

        return null;
    }

    /**
     * Set components.
     * @param $components
     */
    public function setComponents($components) {
        $this->components = $components;
    }

    /**
     * Get cluster status.
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\Cloud\Component\Admin\Collections\Result\ClusterStatus|null
     */
    public function getClusterStatus()
    {
        return $this->getComponent(ComponentAwareCollectionsInterface::CLUSTERSTATUS);
    }
}