<?php

$addon = rex_addon::get('simplesearch');

$db_file = rex_path::addonData('simplesearch', "loupe.db");

rex_dir::create(rex_path::addonData('simplesearch'), $recursive = true);

if (rex_file::get($db_file) == null) {
    rex_file::put(rex_path::addonData('simplesearch', "loupe.db"), "");
}


rex_sql_table::get(rex::getTable('simplesearch_searchterms'))
    ->ensureColumn(new rex_sql_column('term', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('resultcount', 'int(11)', false, '0'))
    ->setPrimaryKey('term')
    ->ensure();
