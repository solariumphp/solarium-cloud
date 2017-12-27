# solarium-cloud
Solarium extension to connect to SolrCloud via Apache Zookeeper.

This is an alpha version of solarium-cloud.

## Requirement
The extension requires the PHP Zookeeper extension, which can be found here:
https://pecl.php.net/package/zookeeper

In order to compile the extension you need to have the Apache Zookeeper C-library installed.
Instructions on how to build the extension can be found here:
https://github.com/php-zookeeper/php-zookeeper

## Example use
You can create a CloudClient using the following code:

```php
    <?php
    
    $options = array('zkhosts' => 'localhost:2181');
    $client = new \Solarium\Cloud\Client($options);
    
    $client->setCollection('collection1');
    $query = $client->createSelect();
    $result = $client->select($query);
```
