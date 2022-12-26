<?php
namespace Ynotz\EasyAdmin\Traits;

use ReflectionFunction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Query\Builder;

trait IsModelViewConnector{
    protected $modelClass;
    protected $idKey = 'id'; // id column in the db table to identify the items
    protected $selects = '*'; // query select keys/calcs
    protected $selIdsKey = 'id'; // selected items id key
    protected $searchesMap = []; // associative array mapping search query params to db columns
    protected $sortsMap = []; // associative array mapping sort query params to db columns
    protected $filtersMap = [];
    // protected $uniqueSortKey = null; // unique key to sort items. it can be a calculated field to ensure unique values
    protected $sqlOnlyFullGroupBy = true;
    protected $defaultSearchColumn = 'name';
    protected $defaultSearchMode = 'startswith'; // contains, startswith, endswith
    public $downloadFileName = 'results';

    public function index(
        int $itemsCount,
        ?int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds = '',
        string $resultsName = 'results'
    ): array {

        $this->preIndexExtra();

        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams
        );

        if(!$this->sqlOnlyFullGroupBy) {
            DB::statement("SET SQL_MODE=''");
        }

        $results = $queryData['query']->paginate(
            $itemsCount,
            $this->selects,
            'page',
            $page
        );

        if(!$this->sqlOnlyFullGroupBy) {
            DB::statement("SET SQL_MODE='only_full_group_by'");
        }

        $this->postIndexExtra();
        $data = $results->toArray();

        $paginator = $this->getPaginatorArray($results);

        return [
            'results' => $results,
            // 'results_json' => json_encode($this->formatIndexResults($results->toArray()['data'])),
            'searches' => $queryData['searchParams'],
            'sorts' => $queryData['sortParams'],
            'filters' => $queryData['filterData'],
            'adv_params' => $queryData['advParams'],
            'items_count' => $itemsCount,
            'items_ids' => $this->getItemIds($results),
            'selected_ids' => $selectedIds,
            'selectIdsUrl' => $this->getSelectedIdsUrl(),
            'total_results' => $data['total'],
            // 'current_page' => $data['current_page'],
            // 'paginator' => json_encode($paginator),
            'downloadUrl' => $this->getDownloadUrl(),
            'createRoute' => $this->getCreateRoute(),
            'route' => Request::route()->getName(),
            'selectionEnabled' => true,
            'advSearchFields' => $this->getAdvanceSearchFields(),
            'col_headers' => $this->getIndexHeaders(),
            'columns' => $this->getIndexColumns(),
            'title' => $this->getPageTitle()
        ];
    }

    private function getQuery()
    {
        return $this->modelClass::query();
    }

    private function getItemIds($results) {
        $ids = $results->pluck($this->idKey)->toArray();
        return json_encode($ids);
    }

    public function indexDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters,
            $advParams,
            $selectedIds
        );

        DB::statement("SET SQL_MODE=''");
        $results = $queryData['query']->select($this->selects)->get();
        DB::statement("SET SQL_MODE='only_full_group_by'");

        return $this->formatIndexResults($results->toArray());
    }

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
    ): array {
        $queryData = $this->getQueryAndParams(
            $searches,
            $sorts,
            $filters
        );

        DB::statement("SET SQL_MODE=''");

        $results = $queryData['query']->select($this->selects)->get()->pluck($this->idKey)->unique()->toArray();
        DB::statement("SET SQL_MODE='only_full_group_by'");
        return $results;
    }

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams = [],
        string $selectedIds = ''
    ): array {
        $query = $this->getQuery();

        // if (count($relations = $this->relations()) > 0) {
        //     $query->with(array_keys($relations));
        // }

        $filterData = $this->getFilterParams($query, $filters, $this->filtersMap);
        $searchParams = $this->getSearchParams($query, $searches, $this->searchesMap);
        $sortParams = $this->getSortParams($query, $sorts, $this->sortsMap);
        $advParams = $this->getSearchParams($query, $advParams, $this->searchesMap);

        // $this->extraConditions($query);

        if (isset($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $ids = explode('|', $selectedIds);
            // $this->query->whereIn('c.id', $ids);
            $this->querySelectedIds($query, $this->selIdsKey, $ids);
        }

        return [
            'query' => $query,
            'searchParams' => $searchParams,
            'sortParams' => $sortParams,
            'filterData' => $filterData,
            'advParams' => $advParams
        ];
    }

    public function getItem(string $id): Model
    {
        return $this->modelClass::find($id);
    }

    public function store(array $data)
    {
        return $this->modelClass::create($data);
    }

    private function querySelectedIds(Builder $query, string $idKey, array $ids): void
    {
        $query->whereIn($idKey, $ids);
    }

    abstract protected function accessCheck(Model $item): bool;

    private function getSearchOperator($op, $val)
    {
        $ops = [
            'is' => 'like',
            'ct' => 'like',
            'st' => 'like',
            'en' => 'like',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
            'eq' => '=',
            'neq' => '<>',
        ];
        $v = $val;
        switch($op) {
            case 'ct':
                $v = '%'.$val.'%';
                break;
            case 'st':
                $v = $val.'%';
                break;
            case 'en':
                $v = '%'.$val;
                break;
        }
        // if (in_array($op, ['gt', 'lt', 'gte', 'lte','eq', 'neq'])) {
        //     $v = floatval($v);
        // }
        return [
            'op' => $ops[$op],
            'val' => $v
        ];
    }

    private function getSearchParams($query, array $searches, $searchesMap): array
    {
        $searchParams = [];
        foreach ($searches as $search) {
            $data = explode('::', $search);
            $rel = $searchesMap[$data[0]] ?? $data[0];
            $rel = $data[0];
            $op = $this->getSearchOperator($data[1], $data[2]);
            if($this->isRelation($rel)) {
                $this->applyRelationSearch($query, $rel, $this->relations()[$rel]['search_column'], $op['op'], $op['val']);
            } else {
                $query->where($rel, $op['op'], $op['val']);
            }
            $searchParams[$data[0]] = $data[2];
        }
        return $searchParams;
    }

    private function getFilterParams($query, array $filters, $filtersMap): array
    {
        $filterParams = [];
        foreach ($filters as $filter) {
            $data = explode('::', $filter);
            $rel = $filtersMap[$data[0]] ?? $data[0];
            $rel = $data[0];
            $op = $this->getSearchOperator($data[1], $data[2]);
            if($this->isRelation($rel)) {
                // dd($rel, $op['op'], $op['val']);
                $this->applyRelationSearch($query, $rel, $this->relations()[$rel]['filter_column'], $op['op'], $op['val']);
            } else {
                $query->where($rel, $op['op'], $op['val']);
            }
            $filterParams[$data[0]] = $data[2];
        }
        return $filterParams;
    }

    private function getSortParams($query, array $sorts, array $sortsMap): array
    {
        $sortParams = [];
        foreach ($sorts as $sort) {
            $data = explode('::', $sort);
            $key = $sortsMap[$data[0]] ?? $data[0];
            if (isset($usortkey) && isset($map[$data[0]])) {
                $type = $key['type'];
                $kname = $key['name'];
                switch ($type) {
                    case 'string';
                        $query->orderByRaw('CONCAT('.$kname.',\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'integer';
                        $query->orderByRaw('CONCAT(LPAD(ROUND('.$kname.',0),20,\'00\'),\'::\','.$usortkey.') '.$data[1]);
                        break;
                    case 'float';
                        $query->orderByRaw('CONCAT( LPAD(ROUND('.$kname.',0) * 100,20,\'00\') ,\'::\','.$usortkey.') '.$data[1]);
                        break;
                    default:
                        $query->orderByRaw('CONCAT('.$kname.'\'::\','.$usortkey.') '.$data[1]);
                        break;
                }
            } else {
                $query->orderBy($data[0], $data[1]);
            }

            $sortParams[$data[0]] = $data[1];
        }
        // dd($sortParams);
        return $sortParams;
    }

    private function applyRelationSearch(Builder $query, $relName, $key, $op, $val): void
    {
        // If isset(search_fn): execute it
        if (isset($this->relations()[$relName]['search_fn'])) {
            $this->relations()[$relName]['search_fn']($query, $op['op'], $op['val']);
        } else {
            // Get relation type
            $type = $this->getRelationType($relName);
            switch ($type) {
                case 'onetoone':
                    break;
                case 'BelongsToMany':
                    $query->whereHas($relName, function ($q) use ($key, $op, $val) {
                        $q->where($key, $op, $val);
                    });
                    break;
            }
        }
    }

    private function getRelationType(string $relation): string
    {
        $obj = new $this->modelClass;
        $type = get_class($obj->{$relation}());
        $ar = explode('\\', $type);
        return $ar[count($ar) - 1];
    }
    // private function getFilterParams($query, array $filters, array $filtersMap): array
    // {
    //     $filterData = [];

    //     foreach ($filters as $filter) {
    //         $data = explode('::', $filter);
    //         $key = $filtersMap[$data[0]] ?? $data[0];
    //         $filterData[$data[0]] = $data[1];
    //         if (isset($map[$data[0]])) {
    //             $type = $key['type'];
    //             $kname = $key['name'];
    //             switch ($type) {
    //                 case 'string';
    //                     $query->where($kname, 'like', $data[1]);
    //                     break;
    //                 default:
    //                     $query->where($kname, $data[1]);
    //                     break;
    //             }
    //         } else {
    //             $query->where($data[0], $data[1]);
    //         }
    //     }

    //     return $filterData;
    // }


    private function isRelation($key): bool
    {
        return in_array(explode('.', $key)[0], array_keys($this->relations()));
    }


    private function getPaginatorArray(LengthAwarePaginator $results): array
    {
        $data = $results->toArray();
        return [
            'currentPage' => $data['current_page'],
            'totalItems' => $data['total'],
            'lastPage' => $data['last_page'],
            'itemsPerPage' => $results->perPage(),
            'nextPageUrl' => $results->nextPageUrl(),
            'prevPageUrl' => $results->previousPageUrl(),
            'elements' => $results->links()['elements'],
            'firstItem' => $results->firstItem(),
            'lastItem' => $results->lastItem(),
            'count' => $results->count(),
        ];
    }

    protected function relations(): array
    {
        return [
            // 'relation_name' => [
            //     'type' => '',
            //     'field' => '',
            //     'search_fn' => function ($query, $op, $val) {}, // function to be executed on search
            //     'search_scope' => '', //optional: required only for combined fields search
            //     'sort_scope' => '', //optional: required only for combined fields sort
            //     'models' => '' //optional: required only for morph types of relations
            // ],
        ];
    }

    // protected function extraConditions(Builder $query): void {}
    protected function applyGroupings(Builder $q): void {}

    protected function formatIndexResults(array $results): array
    {
        return $results;
    }

    protected function preIndexExtra(): void {}
    protected function postIndexExtra(): void {}

    protected function getIndexHeaders(): array
    {
        return [];
    }

    protected function getIndexColumns(): array
    {
        return [];
    }

    protected function getAdvanceSearchFields(): array
    {
        return [];
    }

    protected function getPageTitle(): string
    {
        return Str::headline(Str::plural($this->getModelShortName()));
    }

    protected function getSelectedIdsUrl(): string
    {
        return route(Str::lower(Str::plural($this->getModelShortName())).'.selectIds');
    }

    protected function getDownloadUrl(): string
    {
        return route(Str::lower(Str::plural($this->getModelShortName())).'.download');
    }

    protected function getCreateRoute(): string
    {
        return Str::lower(Str::plural($this->getModelShortName())).'.create';
    }

    public function getDownloadCols(): array
    {
        return [];
    }

    public function getCreatePageData(): array
    {
        return [];
    }

    public function getStoreValidationRules(): array
    {
        return $this->storeValidationRules ?? [];
    }

    public function suggestlist($request)
    {
        $search = $request->input('search', null);

        if (isset($search)) {
            switch($this->defaultSearchMode) {
                case 'contains':
                    $search = '%'.$search.'%';
                    break;
                case 'startswith':
                    $search = $search.'%';
                    break;
                case 'endswith':
                    $search = '%'.$search;
                    break;
            }
            return $this->modelClass::where($this->defaultSearchColumn, 'like', $search)->get();
        } else {
            return $this->modelClass::all();
        }
    }

    private function getModelShortName() {
        $a = explode('\\', $this->modelClass);
        return $a[count($a) - 1];
    }
}
?>
