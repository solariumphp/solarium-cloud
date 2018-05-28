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

namespace Solarium\Cloud\Tests\Component\Admin\Collections;

use PHPUnit\Framework\TestCase;
use Solarium\Cloud\Component\Admin\Collections\ClusterStatus;
use Solarium\Cloud\Component\Admin\Collections\ComponentAwareCollectionsInterface;
use  Solarium\Cloud\Component\Admin\Collections\RequestBuilder\ClusterStatus as RequestBuilder;
use  Solarium\Cloud\Component\Admin\Collections\ResponseParser\ClusterStatus as ResponseParser;

class ClusterStatusTest extends TestCase
{
    /**
     * @var ClusterStatus
     */
    protected $clusterstatus;

    public function setUp()
    {
        $this->clusterstatus = new ClusterStatus();
    }

    public function testEmptyConfig()
    {
        $options = [];

        $this->clusterstatus->setOptions($options);

        $this->assertNull($this->clusterstatus->getCollection());
        $this->assertNull($this->clusterstatus->getShard());
        $this->assertNull($this->clusterstatus->getRoute());
    }

    public function testGetType()
    {
        $this->assertEquals(
            ComponentAwareCollectionsInterface::CLUSTERSTATUS,
            $this->clusterstatus->getType()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->clusterstatus->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->clusterstatus->getResponseParser());
    }

}