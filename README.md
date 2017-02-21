# solarium-cloud
solarium extension to connect to SolrCloud via Zookeeper

This is an alpha version of solarium-cloud. Do not use in production yet.

You can create a CloudClient using the following code:

```php
    <?php
    
    $options = array('zkhosts' => 'localhost:2181');
    $client = new \Solarium\Cloud\Client($options);
    
    $client->setCollection('collection1');
    $query = $client->createSelect();
    $result = $client->select($query);
```