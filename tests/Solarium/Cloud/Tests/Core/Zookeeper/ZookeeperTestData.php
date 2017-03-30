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

/**
 * Class ZookeeperTestData
 */
class ZookeeperTestData
{

    const TESTDATA1 = '{"collection1":{
    "replicationFactor":"1",
    "shards":{
      "shard1":{
        "range":"0-7fffffff",
        "state":"active",
        "replicas":{
          "core_node1":{
            "core":"collection1_shard1_replica1",
            "base_url":"http://localhost:8983/solr",
            "node_name":"localhost:8983_solr",
            "state":"active",
            "leader":"true"}}},
      "shard2":{
        "range":"80000000-ffffffff",
        "state":"active",
        "replicas":{
          "core_node3":{
            "core":"collection1_shard1_replica2",
            "base_url":"http://localhost:8984/solr",
            "node_name":"localhost:8984_solr",
            "state":"active",
            "leader":"true"}}}},
    "router":{"name":"compositeId"},
    "maxShardsPerNode":"1",
    "autoAddReplicas":"false"}}';

}


