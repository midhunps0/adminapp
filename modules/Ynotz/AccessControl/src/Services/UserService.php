<?php
namespace Ynotz\AccessControl\Services;


use Illuminate\Support\Str;
use Ynotz\AccessControl\Models\Role;
use Ynotz\AccessControl\Models\User;
use Ynotz\EasyAdmin\Services\FormHelper;
use Ynotz\EasyAdmin\Services\DashboardService;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class UserService implements ModelViewConnector
{
    use IsModelViewConnector;

    protected $storeValidationRules = [
        'name' => 'required|min:3',
        'role' => 'required'
    ];

    public function __construct()
    {
        $this->modelClass = User::class;
    }

    private function getQuery()
    {
        return $this->modelClass::query()->with(['roles' => function ($query) {
            $query->select('name', 'id');
        }]);
    }

    protected function getIndexHeaders(): array
    {
        return [
            [
                'title' => 'Name',
                'sort' => ['key' => 'name'],
                'search' => ['key' => 'name', 'condition' => 'ct'],
                'search_label' => 'Search Users',
                'style' => 'width: 400px;'
            ],
            [
                'title' => 'Roles',
                'filter' => [
                    'key' => 'roles',
                    'options' => Role::all()->pluck('name', 'id')
                ],
                'style' => 'width: 300px;'
            ],
            [
                'title' => 'Action'
            ]
        ];
    }

    protected function getIndexColumns(): array
    {
        return [
            [
                'fields' => ['name'],
                'component' => 'text',
                'link' => [
                    'route' => 'users.show',
                    'key' => 'id'
                ]
            ],
            [
                'fields' => ['id', 'name'],
                'relation' => 'roles',
                'component' => 'text'
            ],
            [
                'edit_route' => 'users.edit',
                'component' => 'actions'
            ]
        ];
    }

    protected function relations(): array
    {
        return [
            'roles' => [
                'search_column' => 'id',
                'filter_column' => 'id'
                // 'search_fn' => function ($query, $op, $val) {
                //     $query->whereHas('roles', function ($q) use ($op, $val) {
                //         $q->where('name', $op, $val);
                //     });
                // }
            ],
        ];
    }

    protected function getAdvanceSearchFields(): array
    {
        return [
            'roles' => [
                'key' => 'roles',
                'text' => 'Roles',
                'type' => 'list_numeric',
                'inputType' => 'select',
                'options' => Role::all()->pluck('name', 'id'),
                'optionsType' => 'key_value' //value_only
            ]
        ];
    }

    public function getDownloadCols(): array
    {
        return [
            'id',
            'name'
        ];
    }

    public function getCreatePageData(): array
    {
        return [
            'title' => 'Create User',
            'form' => [
                'id' => 'form_user_create',
                'action_route' => 'users.store',
                'label_position' => 'float', //top/side/float
                'items' => [
                    FormHelper::makeInput(
                        inputType: 'text',
                        key: 'name',
                        label: 'Name',
                        properties: ['required' => true],
                        fireInputEvent: true
                    ),
                    FormHelper::makeInput(
                        inputType: 'text',
                        key: 'slug',
                        label: 'Slug',
                        properties: ['required' => true],
                        updateOnEvents: [
                            'name' => [
                                urlencode(Static::class),
                                'getSlug'
                            ]
                        ]
                    ),
                    FormHelper::makeInput('email', 'email', 'Email', ['required' => true]),
                    FormHelper::makeSelect(
                        key: 'role',
                        label: 'Role',
                        options: Role::all()->pluck('name', 'id')->toArray(),
                        options_type: 'key_value',
                        properties: [
                            'required' => true,
                            'multiple' => false
                        ]
                    ),
                    // FormHelper::makeSuggestlist(
                    //     key: 'role',
                    //     label: 'Role',
                    //     options_src: [RoleService::class, 'suggestList'],
                    //     properties: [
                    //         'required' => true,
                    //         'multiple' => true
                    //     ]
                    // ),
                ]
            ]
        ];
    }

    protected function getRelationQuery(int $id = null) {
        return null;
    }

    protected function accessCheck($item): bool
    {
        return true;
    }

    public function getSlug($request): string
    {
        return Str::slug($request->input('value', ''));
    }
}
?>
