<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name : Class (singular) for example User}
                                      {--table= : 表名称}
                                      {--m : 生成Model}
                                      {--v : 生成View}
                                      {--c : 生成Controller}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUD Generator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getStub($type)
    {
        return file_get_contents(base_path("admin/stubs/$type.stub"));
    }

    protected function model($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("Models/{$name}.php"), $modelTemplate);
    }

    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{search_function_snippet}}'
            ],
            [
                $name,
                strtolower(Str::plural($name)),
                strtolower($name),
                $this->_search_function_snippet($name)
            ],
            $this->getStub('Controller')
        );
        file_put_contents(app_path("Http/Controllers/Admin/{$name}Controller.php"), $controllerTemplate);
    }

    protected function _search_function_snippet($name)
    {
        $modelNamePluralLowerCase = strtolower(Str::snake(Str::plural($name)));
        //$columnNames = Schema::getColumnListing($modelNamePluralLowerCase);
        $dbName = env("DB_DATABASE");
        $columnNames = DB::select(DB::raw("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE table_name = '{$modelNamePluralLowerCase}' AND table_schema = '{$dbName}' ORDER BY ORDINAL_POSITION ASC"));
        $snippet = "";
        foreach ($columnNames as $column) {
            $column = $column->COLUMN_NAME;
            $columnObject = DB::select(DB::raw("show full fields from `{$modelNamePluralLowerCase}` where Field = '{$column}';"));
            $field = $columnObject[0];
            if (Str::startsWith($field->Type, "timestamp")) {
                $snippet .= "\t\t\${$column} = \$request->input('{$column}');\n";
                $snippet .= "\t\tif(isset(\${$column}) && count(\${$column})==2) {\n";
                $snippet .= "\t\t\t\$query->whereBetween(\"{$column}\", [\${$column}[0], \${$column}[1]]);\n";
                $snippet .= "\t\t}\n\n";
            } else if (Str::startsWith($field->Type, "varcha")) {
                $snippet .= "\t\t\${$column} = \$request->input('{$column}');\n";
                $snippet .= "\t\tif(isset(\${$column})) {\n";
                $snippet .= "\t\t\t\$query->where(\"{$column}\",'like', \"%{\${$column}}%\");\n";
                $snippet .= "\t\t}\n\n";
            } else {
                $snippet .= "\t\t\${$column} = \$request->input('{$column}');\n";
                $snippet .= "\t\tif(isset(\${$column})) {\n";
                $snippet .= "\t\t\t\$query->where(\"{$column}\", \${$column});\n";
                $snippet .= "\t\t}\n\n";
            }
        }
        return $snippet;
    }

    protected function request($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Request')
        );

        if (!file_exists($path = app_path('/Http/Controllers/Admin/Requests')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Controllers/Admin/Requests/{$name}Request.php"), $requestTemplate);
    }

    protected function resouce($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Resource')
        );

        if (!file_exists($path = app_path('/Http/Controllers/Admin/Resources')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Controllers/Admin/Resources/{$name}Resource.php"), $requestTemplate);
    }

    protected function vue($name)
    {
        $requestTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{table_column_snippet}}',
                '{{form_item_snippet}}'
            ],
            [
                $name,
                strtolower(Str::plural($name)),
                $this->_table_column_snippet($name),
                $this->_form_item_snippet($name)
            ],
            $this->getStub('Vue')
        );

        if (!file_exists($path = base_path('admin/views')))
            mkdir($path, 0777, true);

        file_put_contents(base_path("admin/views/{$name}.vue"), $requestTemplate);
    }

    function _table_column_snippet($name)
    {
        $modelNamePluralLowerCase = strtolower(Str::snake(Str::plural($name)));
        $dbName = env("DB_DATABASE");
        $columnNames = DB::select(DB::raw("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE table_name = '{$modelNamePluralLowerCase}' AND table_schema = '{$dbName}' ORDER BY ORDINAL_POSITION ASC"));
        //$columnNames = Schema::getColumnListing($modelNamePluralLowerCase);
        $snippet = [];
        foreach ($columnNames as $column) {
            $column = $column->COLUMN_NAME;
            $columnObject = DB::select(DB::raw("show full fields from `{$modelNamePluralLowerCase}` where Field = '{$column}';"));
            $fieldName = $columnObject[0]->Comment;
            $fieldType = $columnObject[0]->Type;

            if ($column == 'deleted_at' || $column == 'updated_at') {
                continue;
            }
            if ($fieldType == "timestamp") {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "
                <el-table-column align=\"center\" width=\"130\">
                    <template slot=\"header\" slot-scope=\"scope\">
                        <div>#fieldName#</div>
                        <div>
                            <el-date-picker
                              v-model=\"params.#column#\"
                              type=\"daterange\"
                              size=\"mini\"
                              style='width:100%'
                              range-separator=\"\"
                              start-placeholder=\"开始\"
                              end-placeholder=\"结束\"
                              value-format=\"yyyy-MM-dd\"
                              :picker-options=\"\$store.state.datePickerOptions\"
                            >
                            </el-date-picker>
                        </div>
                    </template>
                    <template slot-scope=\"scope\">
                        <div>{{scope.row.#column#}}</div>
                    </template>
                </el-table-column>"
                );
            } else if ($fieldType == "text" || $fieldType == "mediumtext") {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "
                <el-table-column align=\"center\" width=\"180\">
                    <template slot=\"header\" slot-scope=\"scope\">
                        <div>#fieldName#</div>
                        <div>
                            <el-input v-model=\"params.#column#\" @keyup.enter.native=\"columnSearch\" size=\"mini\"/>
                        </div>
                    </template>
                    <template slot-scope=\"scope\">
                        <div></div>
                    </template>
                </el-table-column>"
                );
            } else {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "
                <el-table-column align=\"center\" width=\"180\">
                    <template slot=\"header\" slot-scope=\"scope\">
                        <div>#fieldName#</div>
                        <div>
                            <el-input v-model=\"params.#column#\" @keyup.enter.native=\"columnSearch\" size=\"mini\"/>
                        </div>
                    </template>
                    <template slot-scope=\"scope\">
                        <div>{{scope.row.#column#}}</div>
                    </template>
                </el-table-column>"
                );
            }

        }
        return implode($snippet);
    }

    function _form_item_snippet($name)
    {
        $modelNamePluralLowerCase = strtolower(Str::snake(Str::plural($name)));
        $dbName = env("DB_DATABASE");
        $columnNames = DB::select(DB::raw("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE table_name = '{$modelNamePluralLowerCase}' AND table_schema = '{$dbName}' ORDER BY ORDINAL_POSITION ASC"));
        //$columnNames = Schema::getColumnListing($modelNamePluralLowerCase);
        $snippet = [];
        foreach ($columnNames as $column) {
            $column = $column->COLUMN_NAME;
            if ($column == 'id' || $column == 'deleted_at' || $column == 'created_at' || $column == 'updated_at') {
                continue;
            }
            $columnObject = DB::select(DB::raw("show full fields from `{$modelNamePluralLowerCase}` where Field = '{$column}';"));
            $fieldName = $columnObject[0]->Comment;
            $fieldType = $columnObject[0]->Type;
            if ($fieldType == "timestamp") {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "<el-form-item label=\"#fieldName#\">
                                <el-date-picker
                                    v-model=\"formData.#column#\"
                                    type=\"datetime\"
                                    value-format=\"yyyy-MM-dd HH:mm:ss\"
                                    placeholder=\"选择日期\">
                                </el-date-picker>
                            </el-form-item>"
                );
            } else if ($fieldType == "text" || $fieldType == "mediumtext") {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "<quill-editor v-model=\"formData.#column#\"/>"
                );
            } else {
                $snippet[] = str_replace(
                    ['#column#', '#fieldName#',],
                    [$column, empty($fieldName) ? $column : $fieldName],
                    "<el-form-item label=\"#fieldName#\">
                                <el-input v-model=\"formData.#column#\" autocomplete=\"off\" palceholder=\"#fieldName#\"></el-input>
                            </el-form-item>"
                );
            }
        }
        return implode($snippet);
    }

    public function view($name, $tableName)
    {
        $this->vue($name);
        #追加路由
        $resourceName = Str::plural(strtolower($name));
        $controllerName = "{$name}Controller";
        #要追加的行
        $routeLine = "Route::resource('{$resourceName}', '{$controllerName}');";
        #判断是否追加过了
        $content = File::get(base_path('routes/admin.php'));
        if (!Str::contains($content, $routeLine)) {
            File::append(base_path('routes/admin.php'), "\n{$routeLine}");
        } else {
            echo("admin.php 中已存在路由{$routeLine}\n");
        }
        #其他需要手动追加的内容
        echo "-------以下内容需要手动追加到router/index.js中-------------\n";
        echo "{path: '/{$tableName}',name: '{$name}',component: () => import('../views/{$name}')},\n";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $tableName = $this->option('table');
        if (empty($tableName)) {
            $tableName = strtolower(Str::snake(Str::plural($name)));
            if (!Schema::hasTable($tableName)) {
                exit("不存在的模型{$name} -> 表名{$tableName}");
            }
        }

        #检查参数
        $c = $this->option('c');
        $m = $this->option('m');
        $v = $this->option('v');

        if ($c == 1) {
            $this->controller($name);
        }
        if ($m == 1) {
            if ($name != "User") {
                $this->model($name);
            }
        }
        if ($v == 1) {
            $this->view($name, $tableName);
        }
        #生成全部
        if ($c != 1 && $m != 1 && $v != 1) {
            $this->controller($name);
            if ($name != "User") {
                $this->model($name);
            }
            $this->request($name);
            $this->resouce($name);
            $this->view($name, $tableName);
        }
    }
}
