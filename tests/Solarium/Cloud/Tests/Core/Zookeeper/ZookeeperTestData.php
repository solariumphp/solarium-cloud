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

    const COLLECTION_STATE_EXPECTED = 'Tzo0NToiU29sYXJpdW1cQ2xvdWRcQ29yZVxab29rZWVwZXJcQ29sbGVjdGlvblN0YXRlIjo1OntzOjc6IgAqAG5hbWUiO3M6MTE6ImNvbGxlY3Rpb24xIjtzOjk6IgAqAHNoYXJkcyI7YToyOntzOjY6InNoYXJkMSI7Tzo0MDoiU29sYXJpdW1cQ2xvdWRcQ29yZVxab29rZWVwZXJcU2hhcmRTdGF0ZSI6ODp7czo3OiIAKgBuYW1lIjtzOjY6InNoYXJkMSI7czo4OiIAKgByYW5nZSI7czoxMDoiMC03ZmZmZmZmZiI7czoxMToiACoAcmVwbGljYXMiO2E6MTp7czoxMDoiY29yZV9ub2RlMSI7Tzo0MjoiU29sYXJpdW1cQ2xvdWRcQ29yZVxab29rZWVwZXJcUmVwbGljYVN0YXRlIjo4OntzOjc6IgAqAG5hbWUiO3M6MTA6ImNvcmVfbm9kZTEiO3M6NzoiACoAY29yZSI7czoyNzoiY29sbGVjdGlvbjFfc2hhcmQxX3JlcGxpY2ExIjtzOjEwOiIAKgBiYXNlVXJpIjtzOjI2OiJodHRwOi8vbG9jYWxob3N0Ojg5ODMvc29sciI7czoxMToiACoAbm9kZU5hbWUiO3M6MTk6ImxvY2FsaG9zdDo4OTgzX3NvbHIiO3M6OToiACoAbGVhZGVyIjtzOjQ6InRydWUiO3M6ODoiACoAc3RhdGUiO3M6NjoiYWN0aXZlIjtzOjExOiIAKgBzdGF0ZVJhdyI7YTo1OntzOjQ6ImNvcmUiO3M6Mjc6ImNvbGxlY3Rpb24xX3NoYXJkMV9yZXBsaWNhMSI7czo4OiJiYXNlX3VybCI7czoyNjoiaHR0cDovL2xvY2FsaG9zdDo4OTgzL3NvbHIiO3M6OToibm9kZV9uYW1lIjtzOjE5OiJsb2NhbGhvc3Q6ODk4M19zb2xyIjtzOjU6InN0YXRlIjtzOjY6ImFjdGl2ZSI7czo2OiJsZWFkZXIiO3M6NDoidHJ1ZSI7fXM6MTI6IgAqAGxpdmVOb2RlcyI7YToyOntpOjA7czoxOToibG9jYWxob3N0Ojg5ODNfc29sciI7aToxO3M6MTk6ImxvY2FsaG9zdDo4OTg0X3NvbHIiO319fXM6MTQ6IgAqAHNoYXJkTGVhZGVyIjtzOjEwOiJjb3JlX25v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InJlcGxpY2F0aW9uRmFjdG9yIjtzOjE6IjEiO3M6Njoic2hhcmRzIjthOjI6e3M6Njoic2hhcmQxIjthOjM6e3M6NToicmFuZ2UiO3M6MTA6IjAtN2ZmZmZmZmYiO3M6NToic3RhdGUiO3M6NjoiYWN0aXZlIjtzOjg6InJlcGxpY2FzIjthOjE6e3M6MTA6ImNvcmVfbm9kZTEiO2E6NTp7czo0OiJjb3JlIjtzOjI3OiJjb2xsZWN0aW9uMV9zaGFyZDFfcmVwbGljYTEiO3M6ODoiYmFzZV91cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODk4My9zb2xyIjtzOjk6Im5vZGVfbmFtZSI7czoxOToibG9jYWxob3N0Ojg5ODNfc29sciI7czo1OiJzdGF0ZSI7czo2OiJhY3RpdmUiO3M6NjoibGVhZGVyIjtzOjQ6InRydWUiO319fXM6Njoic2hhcmQyIjthOjM6e3M6NToicmFuZ2UiO3M6MTc6IjgwMDAwMDAwLWZmZmZmZmZmIjtzOjU6InN0YXRlIjtzOjY6ImFjdGl2ZSI7czo4OiJyZXBsaWNhcyI7YToxOntzOjEwOiJjb3JlX25vZGUyIjthOjU6e3M6NDoiY29yZSI7czoyNzoiY29sbGVjdGlvbjFfc2hhcmQyX3JlcGxpY2ExIjtzOjg6ImJhc2VfdXJsIjtzOjI2OiJodHRwOi8vbG9jYWxob3N0Ojg5ODQvc29sciI7czo5OiJub2RlX25hbWUiO3M6MTk6ImxvY2FsaG9zdDo4OTg0X3NvbHIiO3M6NToic3RhdGUiO3M6NjoiYWN0aXZlIjtzOjY6ImxlYWRlciI7czo0OiJ0cnVlIjt9fX19czo2OiJyb3V0ZXIiO2E6MTp7czo0OiJuYW1lIjtzOjExOiJjb21wb3NpdGVJZCI7fXM6MTY6Im1heFNoYXJkc1Blck5vZGUiO3M6MToiMSI7czoxNToiYXV0b0FkZFJlcGxpY2FzIjtzOjU6ImZhbHNlIjt9fXM6MTI6IgAqAGxpdmVOb2RlcyI7YToyOntpOjA7czoxOToibG9jYWxob3N0Ojg5ODNfc29sciI7aToxO3M6MTk6ImxvY2FsaG9zdDo4OTg0X3NvbHIiO319';

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


