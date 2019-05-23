<?php

use ClanCats\Hydrahon\Query\Sql\Table;

require 'vendor/autoload.php';

$GLOBALS['pdo'] = new PDO('mysql:host=gs.exorth.com;dbname=exorth_beta;charset=utf8', 'exorth_beta', 'abc123');

$pdoConnection = $GLOBALS['pdo'];
$con  = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($pdoConnection) {
    $statement = $pdoConnection->prepare($queryString);
    $statement->execute($queryParameters);

    if($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface) {
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
});

/** @var Table $table */
/*$table = $con->table('items');

$items = $table->select()
    ->execute();

echo count($items);*/

$prepare = $pdoConnection->query("SELECT * FROM `items` LIMIT 1");
$result = $prepare->fetchAll();

echo count($result);