<?php

namespace generate;


class Generate
{
    private $table;
    private $controllerUrl;
    private $module;
    private $primaryKey;
    private $app;
    private $columns = [];
    private $uniqueColumns = [];
    private $comments = [];
    private $templatePath = '';
    private static $instance;

    /**
     * Generate constructor.
     * @param $app
     * @param $module
     * @param $table
     * @throws \Exception
     */
    public function __construct($app, $module, $table)
    {
        $this->templatePath = dirname(__FILE__) . '/template/admin/';
        if ($app == 'api') {
            $this->templatePath = dirname(__FILE__) . '/template/api/';
        }
        $this->app = $app;
        $this->table = ucfirst($table);
        $this->module = $module;
        $this->controllerUrl = strtolower(trim(preg_replace('/([A-Z])/', '-$1', $this->table), '-'));
        $fields = \Db::table($table)->getFields();
        $keys = \Db::table($table)->getKeys();
        $comments = \Db::table($table)->getComments();
        $this->comments = array_column($comments, 'column_comment', 'column_name');
        $uniqueColumns = [];
        foreach ($keys as $v) {
            //主键单独处理
            if ($v['Key_name'] == 'PRIMARY') {
                $this->primaryKey = $v['Column_name'];
                continue;
            }
            //不允许重复
            if ($v['Non_unique'] == 0) {
                $uniqueColumns[$v['Key_name']][] = $v['Column_name'];
            }
        }
        $this->uniqueColumns = $uniqueColumns;
        $columns = [];
        foreach ($fields as $field => $v) {
            $arr = [];
            $arr['field'] = $field;
            $arr['default'] = $v['Default'] == 'NULL' ? '' : $v['Default'];
            $arr['form_type'] = 'input';
            if (strpos(strtolower($v['Type']), 'tinyint') !== false) {
                $arr['form_type'] = 'radio';
            }
            if (strpos($this->comments[$field], 'select') !== false) {
                $arr['form_type'] = 'select';
            }
            $this->comments[$field] = trim(str_replace('select', '', $this->comments[$field]));
            if (strpos($v['Type'], 'int') !== false) {
                $arr['type'] = 'int';
            } elseif (strpos($v['Type'], 'char') !== false || strpos($v['Type'], 'ext') !== false) {
                $arr['type'] = 'string';
            } elseif (strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'double') !== false ||
                strpos($v['Type'], 'decimal') !== false
            ) {
                $arr['type'] = 'float';
            }
            $arr['null'] = 0;
            if ($v['Null'] == 'YES') {
                $arr['null'] = 1;
            }
            $columns[] = $arr;
        }
        $this->columns = $columns;
    }

    /**
     * @param $app
     * @param $module
     * @param $table
     * @return Generate
     * @throws \Exception
     */
    public static function instance($app, $module, $table)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($app, $module, $table);
        }
        return self::$instance;
    }

    public function common()
    {
        $dir = BASE_PATH . $this->app . '/common';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $dirList = [
            'service', 'controller', 'config'
        ];
        foreach ($dirList as $v) {
            if (!file_exists($dir . '/' . $v)) {
                mkdir($dir . '/' . $v, 0755, true);
            }
        }
        $filename = $dir . '/service/BaseService.php';
        if (!file_exists($filename)) {
            $file = $this->templatePath . 'base_service';
            $str = file_get_contents($file);
            $str = str_replace('{{app}}', $this->app, $str);
            file_put_contents($filename, $str);
        }

        $filename = $dir . '/config/config.php';
        if (!file_exists($filename)) {
            $file = dirname(__FILE__) . '/template/common_config';
            $str = file_get_contents($file);
            file_put_contents($filename, $str);
        }

        return $this;
    }

    public function module()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $dirList = [
            'service', 'controller', 'config', 'views'
        ];
        foreach ($dirList as $v) {
            if (!file_exists($dir . '/' . $v)) {
                mkdir($dir . '/' . $v, 0755, true);
            }
        }
        $filename = $dir . '/config/config.php';
        if (!file_exists($filename)) {
            $file = dirname(__FILE__) . '/template/config';
            $str = file_get_contents($file);
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function service()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module . '/service';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $dir . '/' . ucfirst($this->table) . 'Service.php';
        $file = $this->templatePath . 'service';
        $str = file_get_contents($file);
        $selectorParams = '';
        foreach ($this->columns as $v) {
            $selectorParams .= '        if (isset($params[\'' . $v['field'] . '\']) && $params[\'' . $v['field'] . '\'] != \'\') {' . PHP_EOL;
            if ($v['type'] == 'string') {
                $where = '[\'' . $v['field'] . '\' => [\'like\', \'%\' . $params[\'' . $v['field'] . '\'] . \'%\']]';
            } else {
                $where = '[\'' . $v['field'] . '\' => $params[\'' . $v['field'] . '\']]';
            }
            $selectorParams .= '            $selector->where(' . $where . ');' . PHP_EOL;
            $selectorParams .= '        }' . PHP_EOL;
        }
        $checkUnique = '';
        if (count($this->uniqueColumns)) {
            $checkUnique .= PHP_EOL . '        $selector = Db::table(\'' . $this->table . '\');' . PHP_EOL;
            $checkUnique .= '        if (isset($data[\'' . $this->primaryKey . '\']) && $data[\'' . $this->primaryKey . '\']) {' . PHP_EOL;
            $checkUnique .= '            $selector->where([\'' . $this->primaryKey . '\' => [\'!=\', $data[\'' . $this->primaryKey . '\']]]);' . PHP_EOL;
            $checkUnique .= '        }' . PHP_EOL;
            $message = [];
            foreach ($this->uniqueColumns as $key => $vList) {
                $checkUnique .= '        $check = [];' . PHP_EOL;
                foreach ($vList as $v) {
                    $checkUnique .= '        $check[\'' . $v . '\'] = $data[\'' . $v . '\'];' . PHP_EOL;
                    $message[] = $this->comments[$v];
                }
                $checkUnique .= '        $selector->where($check);' . PHP_EOL;
            }
            $checkUnique .= '        $row = $selector->find();' . PHP_EOL;
            $checkUnique .= '        if ($row) {' . PHP_EOL;

            $checkUnique .= '            throw new \Exception(\'' . implode(',', $message) . '不能重复~\');' . PHP_EOL;
            $checkUnique .= '        }' . PHP_EOL;
        }

        $str = str_replace('{{checkUnique}}', $checkUnique, $str);
        $str = str_replace('{{selectorParams}}', $selectorParams, $str);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->table, $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);
        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function controller()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module . '/controller';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = $dir . '/' . ucfirst($this->table) . 'Controller.php';
        $file = $this->templatePath . 'controller';
        $str = file_get_contents($file);
        $searchParams = '';
        foreach ($this->columns as $v) {
            $searchParams .= '        $params[\'' . $v['field'] . '\'] = \App::$request->getParams(\'' . $v['field'] . '\');' . PHP_EOL;
        }
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{searchParams}}', $searchParams, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);
        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function view()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module . '/views/' . $this->controllerUrl;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        //edit
        $inputParams = '';
        foreach ($this->columns as $v) {
            if ($v['field'] == $this->primaryKey) {
                continue;
            }
            $inputParams .= '    <div class="form-group row">' . PHP_EOL;
            $inputParams .= '        <label class="col-sm-4 text-nowrap col-form-label form-label">' . $this->comments[$v['field']] . '</label>' . PHP_EOL;
            $inputParams .= '        <div class="col-sm-8 form-radio-group">' . PHP_EOL;
            if ($v['form_type'] == 'select') {
                $inputParams .= '            <select name="' . $v['field'] . '" class="form-control">' . PHP_EOL;
                $inputParams .= '                <option value="">请选择</option>' . PHP_EOL;
                $inputParams .= '            </select>' . PHP_EOL;
            } else if ($v['form_type'] == 'radio') {
                $inputParams .= '            <?php foreach ([\'0\' => \'否\', \'1\' => \'是\'] as $k => $v): ?>' . PHP_EOL;
                $inputParams .= '                <div class="form-check form-check-inline text-nowrap">' . PHP_EOL;
                $inputParams .= '                    <label class="form-check-label">' . PHP_EOL;
                $inputParams .= '                        <input class="form-check-input" type="radio" name="' . $v['field'] . '"' . PHP_EOL;
                $inputParams .= '                               value="<?= $k ?>" <?= (isset($this->model[\'' . $v['field'] . '\']) && $this->model[\'' . $v['field'] . '\'] == $k || $k=="' . $v['default'] . '") ? \'checked\' : \'\' ?>>' . PHP_EOL;
                $inputParams .= '                        <?= $v ?>' . PHP_EOL;
                $inputParams .= '                    </label>' . PHP_EOL;
                $inputParams .= '                </div>' . PHP_EOL;
                $inputParams .= '            <?php endforeach; ?>' . PHP_EOL;
            } else {
                $inputParams .= '            <input type="text" name="' . $v['field'] . '" class="form-control" value="<?= isset($this->model[\'' . $v['field'] . '\']) ? $this->model[\'' . $v['field'] . '\'] : "' . $v['default'] . '" ?>" placeholder="请输入' . $this->comments[$v['field']] . '">' . PHP_EOL;
            }
            $inputParams .= '        </div>' . PHP_EOL;
            $inputParams .= '    </div>' . PHP_EOL;
        }
        $filename = $dir . '/edit-' . $this->controllerUrl . '.php';
        $file = $this->templatePath . 'view/edit';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{inputParams}}', $inputParams, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        //index
        $searchs = [];
        $header = '';
        $body = '';
        foreach ($this->columns as $v) {
            $searchs[] = '\'' . $v['field'] . '\' => \'' . $this->comments[$v['field']] . '\'';
            $header .= '            <th>' . $this->comments[$v['field']] . '</th>' . PHP_EOL;
            $body .= '                <td><?= $v[\'' . $v['field'] . '\'] ?></td>' . PHP_EOL;
        }
        $searchList = '[' . implode(', ', $searchs) . ']';


        $filename = $dir . '/index.php';
        $file = $this->templatePath . 'view/index';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{searchList}}', $searchList, $str);
        $str = str_replace('{{table-header}}', $header, $str);
        $str = str_replace('{{table-body}}', $body, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function js()
    {
        $dir = PUBLIC_PATH . 'static/js/' . $this->app . '/' . $this->module;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        //js
        $rules = '';
        $rulesMessage = '';
        foreach ($this->columns as $v) {
            if ($v['field'] == $this->primaryKey) {
                continue;
            }
            if (!$v['null']) {
                if ($rules) {
                    $rules .= ',' . PHP_EOL;
                    $rulesMessage .= ',' . PHP_EOL;
                }
                $rules .= '            ' . $v['field'] . ': {' . PHP_EOL;
                $rulesMessage .= '            ' . $v['field'] . ': {' . PHP_EOL;
                $rules .= '                required: true' . PHP_EOL;;
                $rulesMessage .= '                required: \'请输入' . $this->comments[$v['field']] . '\'' . PHP_EOL;;
                $rules .= '            }';
                $rulesMessage .= '            }';

            }
        }
        $filename = $dir . '/' . $this->controllerUrl . '.js';
        $file = $this->templatePath . 'js';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{rules}}', $rules, $str);
        $str = str_replace('{{rulesMessage}}', $rulesMessage, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function run()
    {
        $this->common()->module()->service()->controller()->view()->js();
    }
}