<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(dirname(__DIR__)."/src/Model"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = require_once dirname(__DIR__) . '/migrations-db.php';
// obtaining the entity manager
return EntityManager::create($conn, $config);
