<?php
// cli-config.php
$entityManager = require_once "config/orm.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);

