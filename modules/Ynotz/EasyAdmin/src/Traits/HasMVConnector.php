<?php
/***
 *  This trait is to be used in the controller for quick setup.
 */
namespace Ynotz\EasyAdmin\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\ImportExports\DefaultArrayExports;
use Maatwebsite\Excel\Facades\Excel;


trait HasMVConnector {
    private $itemName = 'item';
    private $indexView = null;
    private $createView = null;
    private $editView = null;

    public function index()
    {
        $result = $this->connectorService->index(
            intval($this->request->input('items_count', 10)),
            $this->request->input('page'),
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', ''),
        );

        if (is_string($this->indexView)) {
            $view = $this->indexView ?? 'admin.'.Str::plural($this->itemName).'.index';
        } elseif(is_array($this->indexView)) {
            $target = $this->request->input('x_target');
            $view = isset($target) && isset($this->indexView[$target]) ? $this->indexView[$target] : $this->indexView['default'];
        }

        return $this->buildResponse($view, $result);
    }

    public function selectIds()
    {
        $ids = $this->connectorService->getIdsForParams(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', [])
        );

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }

    public function download()
    {
        $results = $this->connectorService->indexDownload(
            $this->request->input('search', []),
            $this->request->input('sort', []),
            $this->request->input('filter', []),
            $this->request->input('adv_search', []),
            $this->request->input('selected_ids', '')
        );

        $respone = Excel::download(new DefaultArrayExports($results, $this->connectorService->getDownloadCols()), $this->connectorService->downloadFileName.'.'.$this->request->input('format', 'xlsx'));

        ob_end_clean();

        return $respone;
    }

    public function create()
    {
        $view = $this->createView ?? 'admin.'.Str::plural($this->itemName).'.create';
        $data = $this->connectorService->getCreatePageData();
        return $this->buildResponse($view, $data);
    }

    public function store(Request $request)
    {
        info($request->all());
        try {
            $rules = $this->connectorService->getStoreValidationRules();
            $validator = Validator::make($request->all(), $rules);
            $view = $this->createView ?? 'admin.'.Str::plural($this->itemName).'.create';
            $data = $this->connectorService->getCreatePageData();

            if ($validator->fails()) {
                // $data['_old'] = $request->all();
                // $data['errors'] = $validator->errors();
                // info('errors:');
                // info($data['errors']);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
                // return $this->buildResponse($view, $data);
            }
            // return 'success';
            $this->connectorService->store($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'New '.$this->itemName.' added.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $item = $this->connectorService->modelClass::find($id);
        $view = $this->editView ?? 'admin.'.Str::plural($this->itemName).'.edit';
        return $this->buildResponse($view,
            [$this->itemName => $item]
        );
    }

    public function suggestlist()
    {
        $search = $this->request->input('search', null);

        return response()->json([
            'success' => true,
            'results' => $this->connectorService->suggestlist($search)
        ]);
    }
}
?>
