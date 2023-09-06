<?php

$addon = rex_addon::get('simplesearch');

$db_file = rex_path::addonData('simplesearch', "loupe.db");

rex_dir::create(rex_path::addonData('simplesearch'), $recursive = true);

if (rex_file::get($db_file) == null) {
    rex_file::put(rex_path::addonData('simplesearch', "loupe.db"), "");
}
