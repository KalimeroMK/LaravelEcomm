<?php

namespace Modules\Core\Interfaces;

/**
 * Interface SearchInterface
 */
interface SearchInterface
{
    /**
     * Performs a search based on the provided criteria.
     *
     * @param  array<string, mixed>  $data  Criteria used for the search. Expected keys might include:
     *                                      - 'query': string, The search text.
     *                                      - 'limit': int, Optional. The number of results to return.
     *                                      - 'page': int, Optional. Page number for paginated results.
     *                                      - 'filters': array<string, mixed>, Optional. Additional filters to apply.
     * @return mixed Results of the search operation, typically an array or a collection.
     */
    public function search(array $data): mixed;
}
