<?php

use AndiLeni\search\Search;

$reindex = rex_get('reindex', 'boolean', false);
$offset = rex_post('offset', 'int', 1);

$search = new Search();

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
        $search->update_article($aid);
    }

    $stop = microtime(true);

    echo rex_view::info('Es wurden ' . count($article_ids) . ' Artikel indexiert in ' . round(($stop - $start) + 1) . ' Sekunden.');
    echo rex_view::info('Es wurden die Artikel IDs ' . implode(',', $article_ids) . ' verarbeitet.');
}


$sql = rex_sql::factory();
$res = $sql->getArray('SELECT COUNT(DISTINCT id) as total FROM rex_article');
$num_total_articles = $res[0]['total'];

$res = $sql->getArray('SELECT COUNT(DISTINCT article_id) as total FROM rex_article_slice');
$num_total_articles_slice = $res[0]['total'];

$num_indexed = $search->loupe->countDocuments();



?>

<div class="panel panel-default">
    <header class="panel-heading">
        <div class="panel-title">Index</div>
    </header>

    <div class="panel-body">
        <p>Aktuell sind <b><?= $num_indexed ?></b> Artikel indiziert.</p>
        <p style="margin-bottom: 0;">Insgesamt sind <b><?= $num_total_articles ?></b> Artikel auf dieser Webseite vorhanden, <b><?= $num_total_articles_slice ?></b> davon haben Inhalte.</p>
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

<div class="panel panel-default">
    <header class="panel-heading">
        <div class="panel-title">Funktionen</div>
    </header>

    <div class="panel-body">
        <p>SQLite Version: <?= SQLite3::version()['versionString'] ?></p>
    </div>
</div>