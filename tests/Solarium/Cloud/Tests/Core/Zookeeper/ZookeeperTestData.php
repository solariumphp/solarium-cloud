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

namespace Solarium\Cloud\Tests\Core\Zookeeper;

/**
 * Class ZookeeperTestData
 */
class ZookeeperTestData
{

    const ALIASES = '{"collection":{"collection":"collection1"}}';
    const COLLECTION_STATE =  '{"collection1":{
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
                                      "core_node2":{
                                        "core":"collection1_shard2_replica1",
                                        "base_url":"http://localhost:8984/solr",
                                        "node_name":"localhost:8984_solr",
                                        "state":"active",
                                        "leader":"true"}}}},
                                "router":{"name":"compositeId"},
                                "maxShardsPerNode":"1",
                                "autoAddReplicas":"false"}}';

    const COLLECTION_STATE_EXPECTED = 'O:45:"Solarium\\Cloud\\Core\\Zookeeper\\CollectionState":5:{s:7:"' . "\0" . '*' . "\0" . 'name";s:11:"collection1";s:9:"' . "\0" . '*' . "\0" . 'shards";a:2:{s:6:"shard1";O:40:"Solarium\\Cloud\\Core\\Zookeeper\\ShardState":8:{s:7:"' . "\0" . '*' . "\0" . 'name";s:6:"shard1";s:8:"' . "\0" . '*' . "\0" . 'range";s:10:"0-7fffffff";s:11:"' . "\0" . '*' . "\0" . 'replicas";a:1:{s:10:"core_node1";O:42:"Solarium\\Cloud\\Core\\Zookeeper\\ReplicaState":8:{s:7:"' . "\0" . '*' . "\0" . 'name";s:10:"core_node1";s:7:"' . "\0" . '*' . "\0" . 'core";s:27:"collection1_shard1_replica1";s:10:"' . "\0" . '*' . "\0" . 'baseUri";s:26:"http://localhost:8983/solr";s:11:"' . "\0" . '*' . "\0" . 'nodeName";s:19:"localhost:8983_solr";s:9:"' . "\0" . '*' . "\0" . 'leader";s:4:"true";s:8:"' . "\0" . '*' . "\0" . 'state";s:6:"active";s:11:"' . "\0" . '*' . "\0" . 'stateRaw";a:5:{s:4:"core";s:27:"collection1_shard1_replica1";s:8:"base_url";s:26:"http://localhost:8983/solr";s:9:"node_name";s:19:"localhost:8983_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}s:12:"' . "\0" . '*' . "\0" . 'liveNodes";a:2:{i:0;s:19:"localhost:8983_solr";i:1;s:19:"localhost:8984_solr";}}}s:14:"' . "\0" . '*' . "\0" . 'shardLeader";s:10:"core_node1";s:17:"' . "\0" . '*' . "\0" . 'activeReplicas";N;s:8:"' . "\0" . '*' . "\0" . 'state";s:6:"active";s:11:"' . "\0" . '*' . "\0" . 'stateRaw";a:3:{s:5:"range";s:10:"0-7fffffff";s:5:"state";s:6:"active";s:8:"replicas";a:1:{s:10:"core_node1";a:5:{s:4:"core";s:27:"collection1_shard1_replica1";s:8:"base_url";s:26:"http://localhost:8983/solr";s:9:"node_name";s:19:"localhost:8983_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}}}s:12:"' . "\0" . '*' . "\0" . 'liveNodes";a:2:{i:0;s:19:"localhost:8983_solr";i:1;s:19:"localhost:8984_solr";}}s:6:"shard2";O:40:"Solarium\\Cloud\\Core\\Zookeeper\\ShardState":8:{s:7:"' . "\0" . '*' . "\0" . 'name";s:6:"shard2";s:8:"' . "\0" . '*' . "\0" . 'range";s:17:"80000000-ffffffff";s:11:"' . "\0" . '*' . "\0" . 'replicas";a:1:{s:10:"core_node2";O:42:"Solarium\\Cloud\\Core\\Zookeeper\\ReplicaState":8:{s:7:"' . "\0" . '*' . "\0" . 'name";s:10:"core_node2";s:7:"' . "\0" . '*' . "\0" . 'core";s:27:"collection1_shard2_replica1";s:10:"' . "\0" . '*' . "\0" . 'baseUri";s:26:"http://localhost:8984/solr";s:11:"' . "\0" . '*' . "\0" . 'nodeName";s:19:"localhost:8984_solr";s:9:"' . "\0" . '*' . "\0" . 'leader";s:4:"true";s:8:"' . "\0" . '*' . "\0" . 'state";s:6:"active";s:11:"' . "\0" . '*' . "\0" . 'stateRaw";a:5:{s:4:"core";s:27:"collection1_shard2_replica1";s:8:"base_url";s:26:"http://localhost:8984/solr";s:9:"node_name";s:19:"localhost:8984_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}s:12:"' . "\0" . '*' . "\0" . 'liveNodes";a:2:{i:0;s:19:"localhost:8983_solr";i:1;s:19:"localhost:8984_solr";}}}s:14:"' . "\0" . '*' . "\0" . 'shardLeader";s:10:"core_node2";s:17:"' . "\0" . '*' . "\0" . 'activeReplicas";N;s:8:"' . "\0" . '*' . "\0" . 'state";s:6:"active";s:11:"' . "\0" . '*' . "\0" . 'stateRaw";a:3:{s:5:"range";s:17:"80000000-ffffffff";s:5:"state";s:6:"active";s:8:"replicas";a:1:{s:10:"core_node2";a:5:{s:4:"core";s:27:"collection1_shard2_replica1";s:8:"base_url";s:26:"http://localhost:8984/solr";s:9:"node_name";s:19:"localhost:8984_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}}}s:12:"' . "\0" . '*' . "\0" . 'liveNodes";a:2:{i:0;s:19:"localhost:8983_solr";i:1;s:19:"localhost:8984_solr";}}}s:10:"' . "\0" . '*' . "\0" . 'aliases";N;s:11:"' . "\0" . '*' . "\0" . 'stateRaw";a:1:{s:11:"collection1";a:5:{s:17:"replicationFactor";s:1:"1";s:6:"shards";a:2:{s:6:"shard1";a:3:{s:5:"range";s:10:"0-7fffffff";s:5:"state";s:6:"active";s:8:"replicas";a:1:{s:10:"core_node1";a:5:{s:4:"core";s:27:"collection1_shard1_replica1";s:8:"base_url";s:26:"http://localhost:8983/solr";s:9:"node_name";s:19:"localhost:8983_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}}}s:6:"shard2";a:3:{s:5:"range";s:17:"80000000-ffffffff";s:5:"state";s:6:"active";s:8:"replicas";a:1:{s:10:"core_node2";a:5:{s:4:"core";s:27:"collection1_shard2_replica1";s:8:"base_url";s:26:"http://localhost:8984/solr";s:9:"node_name";s:19:"localhost:8984_solr";s:5:"state";s:6:"active";s:6:"leader";s:4:"true";}}}}s:6:"router";a:1:{s:4:"name";s:11:"compositeId";}s:16:"maxShardsPerNode";s:1:"1";s:15:"autoAddReplicas";s:5:"false";}}s:12:"' . "\0" . '*' . "\0" . 'liveNodes";a:2:{i:0;s:19:"localhost:8983_solr";i:1;s:19:"localhost:8984_solr";}}';

    const CLUSTER_STATE = array (
                            'collection1' =>
                                array (
                                    'replicationFactor' => '1',
                                    'shards' =>
                                        array (
                                            'shard1' =>
                                                array (
                                                    'range' => '0-7fffffff',
                                                    'state' => 'active',
                                                    'replicas' =>
                                                        array (
                                                            'core_node1' =>
                                                                array (
                                                                    'core' => 'collection1_shard1_replica1',
                                                                    'base_url' => 'http://localhost:8983/solr',
                                                                    'node_name' => 'localhost:8983_solr',
                                                                    'state' => 'active',
                                                                    'leader' => 'true',
                                                                ),
                                                        ),
                                                ),
                                            'shard2' =>
                                                array (
                                                    'range' => '80000000-ffffffff',
                                                    'state' => 'active',
                                                    'replicas' =>
                                                        array (
                                                            'core_node2' =>
                                                                array (
                                                                    'core' => 'collection1_shard2_replica1',
                                                                    'base_url' => 'http://localhost:8984/solr',
                                                                    'node_name' => 'localhost:8984_solr',
                                                                    'state' => 'active',
                                                                    'leader' => 'true',
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    'router' =>
                                        array (
                                            'name' => 'compositeId',
                                        ),
                                    'maxShardsPerNode' => '1',
                                    'autoAddReplicas' => 'false',
                                ),
                        );

    const SHARD1_LEADER = '{"core":"collection1_shard1_replica1",
                            "core_node_name":"core_node1",
                            "base_url":"http://localhost:8983/solr",
                            "node_name":"localhost:8983_solr"}';

    const SHARD2_LEADER = '{"core":"scollection1_shard2_replica1",
                            "core_node_name":"core_node2",
                            "base_url":"http://localhost:8984/solr",
                            "node_name":"localhost:8984_solr"}';

}


