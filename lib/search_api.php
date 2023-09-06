<?php

use AndiLeni\search\Search;

class rex_api_simplesearch extends rex_api_function
{

    protected $published = true;

    public function execute()
    {

        $query = rex_request('query', 'string', '');

        $search = new Search();
        $result = $search->search($query);

        exit(json_encode($result));
    }
}
