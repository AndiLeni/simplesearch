<?php

namespace AndiLeni\search;

use rex;
use rex_article;
use rex_article_content;
use rex_extension_point_art_content_updated;
use rex_logger;
use rex_path;
use Loupe\Loupe\Config\TypoTolerance;
use Loupe\Loupe\Configuration;
use Loupe\Loupe\LoupeFactory;
use Loupe\Loupe\SearchParameters;
use rex_sql;

class Search
{

    public $loupe;

    public function __construct()
    {
        $this->loupe = $this->getLoupe();
    }

    public function get_article_name(int $article_id): string
    {
        $article = rex_article::get($article_id);
        return $article->getName();
    }

    public function get_article_url(int $article_id): string
    {
        $article = rex_article::get($article_id);
        return $article->getUrl();
    }

    public function update_article(int $article_id): void
    {
        $content = $this->get_article_content($article_id);

        $article = rex_article::get($article_id);
        $name = $article->getName();

        if ($content != '') {
            $this->loupe->addDocument([
                'id' => $article_id,
                'name' => $name,
                'content' => $content,
            ]);
        } else {
            $this->loupe->addDocument([
                'id' => $article_id,
                'name' => $name,
                'content' => "-",
            ]);
        }
    }

    public function get_article_content(int $article_id): string
    {
        $article_content = new rex_article_content($article_id);

        $content = $article_content->getArticle();

        $content = strip_tags($content);
        $content = str_replace(["\r", "\n"], '', $content);

        return $content;
    }

    public function search(string $query): array
    {
        $searchParameters = SearchParameters::create()
            ->withShowRankingScore(true)
            ->withQuery($query);

        $results = $this->loupe->search($searchParameters);

        // update searchterms table for statistics, only if request is made in frontend
        if (rex::isFrontend()) {
            $sql = rex_sql::factory();
            $sql_insert = 'INSERT INTO ' . rex::getTable('simplesearch_searchterms') . ' (term,resultcount) VALUES ("' . addslashes($query) . '",1)  
                ON DUPLICATE KEY UPDATE resultcount = resultcount + 1;';
            $sql->setQuery($sql_insert);
        }

        return $results->toArray();
    }

    private function getLoupe()
    {
        $configuration = Configuration::create();

        $loupeFactory = new LoupeFactory();

        $loupe = $loupeFactory->create(rex_path::addonData("simplesearch", "loupe.db"), $configuration);

        return $loupe;
    }

    public function delete_index_for_article(int $article_id): void
    {
        $this->loupe->deleteDocument($article_id);
    }

    public function status_update($ep): void
    {
        $article_id =  $ep->getParams()['id'];
        $newstatus = $ep->getParams()['status'];

        if ($newstatus === 1) {
            $this->update_article($article_id);
        } else {
            $this->delete_index_for_article($article_id);
        }
    }
}
