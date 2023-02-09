<?php

use TeamTNT\TNTSearch\TNTSearch;



$addon = rex_addon::get('simplesearch');


rex_dir::create(rex_path::addonData('simplesearch'), $recursive = true);

$db_config = rex::getDbConfig(1);

$tnt = new TNTSearch;
$tnt->loadConfig([
    'driver'    => 'mysql',
    'host'      => $db_config->host,
    'database'  => $db_config->name,
    'username'  => $db_config->login,
    'password'  => $db_config->password,
    'storage'   => rex_path::addonData('simplesearch'),
    'stemmer'   => \TeamTNT\TNTSearch\Stemmer\GermanStemmer::class //optional
]);
$indexer = $tnt->createIndex('articles.index');
$indexer->setLanguage('german');
