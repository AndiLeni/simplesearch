<?php

use TeamTNT\TNTSearch\TNTSearch;
use AndiLeni\search\Search;

$reindex = rex_get('reindex', 'boolean', false);
$offset = rex_post('offset', 'int', 1);

// TODO
// offset einbauen
// offline artikel berücksichtigen

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
$index = $tnt->getIndex();

if ($reindex) {

    if ($offset === 0) {
        $sql = rex_sql::factory();
        $article_ids = $sql->getArray('SELECT DISTINCT article_id FROM rex_article_slice');
        $article_ids = array_column($article_ids, 'article_id');
    } else {
        $article_ids = [];
        for ($i = 1 + (20 * ($offset - 1)); $i <= $offset * 20; $i++) {
            $article_ids[] = $i;
        }
    }

    $start = microtime(true);

    foreach ($article_ids as $aid) {
        Search::update_article($aid);
    }

    $stop = microtime(true);

    echo rex_view::info('Es wurden ' . count($article_ids) . ' Artikel indexiert in ' . round(($stop - $start) + 1) . ' Sekunden.');
    echo rex_view::info('Es wurden die Artikel IDs ' . implode(',', $article_ids) . ' verarbeitet.');
}


$sql = rex_sql::factory();
$res = $sql->getArray('SELECT COUNT(DISTINCT id) as total FROM rex_article');
$num_total_articles = $res[0]['total'];


?>

<div class="panel panel-default">
    <header class="panel-heading">
        <div class="panel-title">Index</div>
    </header>

    <div class="panel-body">
        <p>Aktuell sind <b><?= $tnt->totalDocumentsInCollection() ?></b> Artikel indiziert.</p>
        <p style="margin-bottom: 0;">Insgesamt sind <b><?= $num_total_articles ?></b> Artikel auf dieser Webseite vorhanden.</p>
    </div>
</div>

<div class="panel panel-default">
    <header class="panel-heading">
        <div class="panel-title">Einstellungen</div>
    </header>

    <div class="panel-body">
        <form action="<?= rex_url::currentBackendPage(['reindex' => 'true']) ?>" method="post">
            <div class="form-group">
                <label>Offset:</label>
                <input name="offset" value="0" class="form-control" type="number" min="0" max="100">
                <p>Falls der Indexierungsprozess zu lange dauert kann hier inkrementell ein Offset angegeben werden. 1: indexiere Artikel 1-20, 2: Artikel 21-40, usw. | 0: versuche alle Artikel zu indexieren.</p>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Alle Artikel neu indizieren</button>
            </div>
        </form>
    </div>
</div>