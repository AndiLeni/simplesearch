<?php

namespace AndiLeni\search;

use rex;
use rex_article;
use rex_article_content;
use rex_extension_point_art_content_updated;
use rex_logger;
use rex_path;
use TeamTNT\TNTSearch\TNTSearch;

class Search
{

    public static function get_article_name(int $article_id): string
    {
        $article = rex_article::get($article_id);
        return $article->getName();
    }

    public static function get_article_url(int $article_id): string
    {
        $article = rex_article::get($article_id);
        return $article->getUrl();
    }

    public static function update_article_ep(rex_extension_point_art_content_updated $ep): void
    {
        $tnt = Search::get_tntsearch();
        $index = $tnt->getIndex();

        $article =  $ep->getArticle();
        $article_id = $article->getId();

        rex_logger::logError(2, strval($article_id), '', 1);

        $content = Search::get_article_content($article_id);

        if ($content != '') {
            $index->update($article_id, ['id' => $article_id, 'content' => $content]);
        } else {
            $index->update($article_id, ['id' => $article_id, 'content' => '-']);
        }
    }

    public static function update_article(int $article_id): void
    {
        $tnt = Search::get_tntsearch();
        $index = $tnt->getIndex();

        $content = Search::get_article_content($article_id);

        if ($content != '') {
            $index->update($article_id, ['id' => $article_id, 'content' => $content]);
        } else {
            $index->update($article_id, ['id' => $article_id, 'content' => '-']);
        }


        // $article = rex_article::get($article_id);
        // $article_online = $article->isOnline();

        // if ($article_online) {
        //     // if article is online update index
        //     $content = Search::get_article_content($article_id);
        //     $index->update($article_id, ['id' => $article_id, 'content' => $content]);
        // } else {
        //     // delete from index
        //     $index->delete($article_id);
        // }
    }

    public static function get_article_content(int $article_id): string
    {
        $article_content = new rex_article_content($article_id);

        $content = $article_content->getArticle();

        $content = strip_tags($content);
        $content = str_replace(["\r", "\n"], '', $content);

        return $content;
    }

    public static function search(string $query): array
    {
        $tnt = Search::get_tntsearch();

        $res = $tnt->search($query, 12);

        return $res;
    }

    public static function search_fuzzy(string $query): array
    {
        $tnt = Search::get_tntsearch();
        $tnt->fuzziness = true;

        $res = $tnt->search($query, 12);

        return $res;
    }

    public static function get_tntsearch(): TNTSearch
    {
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

        $tnt->selectIndex("articles.index");

        return $tnt;
    }

    public static function delete_index_for_article(int $article_id): void
    {
        $tnt = Search::get_tntsearch();
        $index = $tnt->getIndex();
        $index->delete($article_id);
    }

    public static function status_update($ep): void
    {
        $article_id =  $ep->getParams()['id'];
        $newstatus = $ep->getParams()['status'];

        if ($newstatus === 1) {
            Search::update_article($article_id);
        } else {
            Search::delete_index_for_article($article_id);
        }
    }
}
