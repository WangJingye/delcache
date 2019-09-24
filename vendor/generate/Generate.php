<?php

namespace generate;


class Generate extends \ObjectAccess
{
    public static $instance;
    public $table;
    public $controllerUrl;
    public $module;
    public $theme;
    public $primaryKey;
    public $option;
    public $app;
    public $columnTypes;
    public $uniqueColumns = [];
    public $templatePath = '';

    /**
     * Generate constructor.
     * @param $app
     * @param $module
     * @param $table
     * @throws \Exception
     */
    public function __construct($option)
    {
        $this->theme = $option['template'];
        $this->templatePath = dirname(__FILE__) . '/template/' . $this->theme . '/';
        $this->app = $this->theme == 'api' ? 'api' : 'admin';
        $this->table = ucfirst($option['table']);
        $this->module = $option['module'];
        $this->controllerUrl = strtolower(trim(preg_replace('/([A-Z])/', '-$1', $this->table), '-'));
        $keys = \Db::table($option['table'])->getKeys();
        $this->option = $option;
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
        $columnTypes = [];
        $fields = \Db::table($option['table'])->getFields();
        foreach ($fields as $field => $v) {
            $type = [];
            if (strpos($v['Type'], 'int') !== false) {
                $type = 'int';
            } elseif (strpos($v['Type'], 'char') !== false || strpos($v['Type'], 'ext') !== false) {
                $type = 'string';
            } elseif (strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'double') !== false ||
                strpos($v['Type'], 'decimal') !== false
            ) {
                $type = 'float';
            }
            $columnTypes[$field] = $type;
        }
        $this->columnTypes = $columnTypes;
        $this->uniqueColumns = $uniqueColumns;
    }

    /**
     * @param $app
     * @param $module
     * @param $table
     * @return Generate
     * @throws \Exception
     */
    public static function instance($option)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($option);
        }
        return self::$instance;
    }

    public function run()
    {
        if ($this->theme == 'web') {
            $this->common()->module()->service()->controller()->view()->js();
        } else {
            $this->common()->module()->service()->controller();
        }
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
        foreach ($this->option['fcomment'] as $field => $label) {
            if ($field == $this->primaryKey) {
                continue;
            }
            if (!isset($this->option['frequire'][$field]) || $this->option['frequire'][$field] == 0) {
                continue;
            }
            if ($rules) {
                $rules .= PHP_EOL.',';
                $rulesMessage .= PHP_EOL.',';
            }
            $rules .= PHP_EOL.'            ' . $field . ': {';
            $rulesMessage .= PHP_EOL.'            ' . $field . ': {';
            $rules .= PHP_EOL.'                required: true';;
            $rulesMessage .= PHP_EOL.'                required: \'请输入' . $label . '\'';;
            $rules .= PHP_EOL.'            }';
            $rulesMessage .= PHP_EOL.'            }';

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

    public function view()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module . '/views/' . $this->controllerUrl;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        //edit
        $inputParams = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if ($field == $this->primaryKey) {
                continue;
            }
            if (!isset($this->option['feditshow'][$field]) || $this->option['feditshow'][$field] == 0) {
                continue;
            }
            $inputParams .= PHP_EOL . '    <div class="form-group row">';
            $inputParams .= PHP_EOL . '        <label class="col-sm-4 text-nowrap col-form-label form-label">' . $label . '</label>';
            if (in_array($this->option['ftype'][$field], ['select', 'radio', 'checkbox'])) {
                $inputParams .= PHP_EOL . '        <div class="col-sm-8 form-radio-group">';
                $res = $this->getChooseList($field);
            } else {
                $inputParams .= PHP_EOL . '        <div class="col-sm-8">';
            }
            if ($this->option['ftype'][$field] == 'select') {
                $inputParams .= PHP_EOL . '            <select name="' . $field . '" class="form-control">';
                $inputParams .= PHP_EOL . '                <option value="">请选择</option>';
                $inputParams .= PHP_EOL . '                <?php foreach ($this->' . $res['variable'] . ' as $key => $v): ?>';
                $inputParams .= PHP_EOL . '                    <option value="<?= $key ?>" <?= isset($this->model[\'' . $field . '\']) && $this->model[\'' . $field . '\'] == $key ? \'selected\' : \'\' ?>><?= $v ?></option>';
                $inputParams .= PHP_EOL . '                <?php endforeach; ?>';
                $inputParams .= PHP_EOL . '            </select>';
            } else if ($this->option['ftype'][$field] == 'radio') {
                $inputParams .= PHP_EOL . '            <?php foreach ($this->' . $res['variable'] . ' as $k => $v): ?>';
                $inputParams .= PHP_EOL . '                <div class="form-check form-check-inline text-nowrap">';
                $inputParams .= PHP_EOL . '                    <label class="form-check-label">';
                $inputParams .= PHP_EOL . '                        <input class="form-check-input" type="radio" name="' . $field . '"';
                $inputParams .= PHP_EOL . '                               value="<?= $k ?>" <?= (isset($this->model[\'' . $field . '\']) && $this->model[\'' . $field . '\'] == $k) ? \'checked\' : \'\' ?>>';
                $inputParams .= PHP_EOL . '                        <?= $v ?>';
                $inputParams .= PHP_EOL . '                    </label>';
                $inputParams .= PHP_EOL . '                </div>';
                $inputParams .= PHP_EOL . '            <?php endforeach; ?>';
            } else if ($this->option['ftype'][$field] == 'checkbox') {
                $inputParams .= PHP_EOL . '            <?php foreach ($this->' . $res['variable'] . ' as $k => $v): ?>';
                $inputParams .= PHP_EOL . '                <div class="form-check form-check-inline text-nowrap">';
                $inputParams .= PHP_EOL . '                    <label class="form-check-label">';
                $inputParams .= PHP_EOL . '                        <input class="form-check-input" type="checkbox" name="' . $field . '"';
                $inputParams .= PHP_EOL . '                               value="<?= $k ?>" <?= (isset($this->model[\'' . $field . '\']) && $this->model[\'' . $field . '\'] == $k) ? \'checked\' : \'\' ?>>';
                $inputParams .= PHP_EOL . '                        <?= $v ?>';
                $inputParams .= PHP_EOL . '                    </label>';
                $inputParams .= PHP_EOL . '                </div>';
                $inputParams .= PHP_EOL . '            <?php endforeach; ?>';
            } else if ($this->option['ftype'][$field] == 'textarea') {
                $inputParams .= PHP_EOL . '            <textarea name="' . $field . '" class="form-control" placeholder="请输入' . $label . '"><?= isset($this->model[\'' . $field . '\']) ? $this->model[\'' . $field . '\'] : "" ?></textarea>';
            } else if ($this->option['ftype'][$field] == 'image') {
                $inputParams .= PHP_EOL . '            <div class="fileinput-box">';
                $inputParams .= PHP_EOL . '                <?php if (isset($this->model[\'' . $field . '\']) && $this->model[\'' . $field . '\']): ?>';
                $inputParams .= PHP_EOL . '                    <img src="<?= $this->model[\'' . $field . '\'] ?>">';
                $inputParams .= PHP_EOL . '                <?php endif; ?>';
                $inputParams .= PHP_EOL . '                <div class="fileinput-button">';
                $inputParams .= PHP_EOL . '                    <div class="plus-symbol" <?= isset($this->model[\'' . $field . '\']) && $this->model[\'' . $field . '\'] ? \'style="display:none"\' : \'\' ?>>';
                $inputParams .= PHP_EOL . '                        +';
                $inputParams .= PHP_EOL . '                    </div>';
                $inputParams .= PHP_EOL . '                    <input class="fileinput-input" type="file" name="' . $field . '" value="">';
                $inputParams .= PHP_EOL . '                </div>';
                $inputParams .= PHP_EOL . '            </div>';
                $inputParams .= PHP_EOL . '            <div style="text-align: center;color:red;font-size: 0.5rem;width: 80px">点击修改</div>';
            } else {
                $placeholder = '请输入' . $label;
                if (in_array($this->option['ftype'][$field], ['date', 'date-normal', 'datetime', 'datetime-normal'])) {
                    $placeholder = $label . '，格式为2019-01-01';
                }
                $inputParams .= PHP_EOL . '            <input type="text" name="' . $field . '" class="form-control" value="<?= isset($this->model[\'' . $field . '\']) ? $this->model[\'' . $field . '\'] : "" ?>" placeholder="' . $placeholder . '">';
            }
            $inputParams .= PHP_EOL . '        </div>';
            $inputParams .= PHP_EOL . '    </div>';
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
        $searchPer = '';
        $header = '';
        $body = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            $res = $this->getChooseList($field);
            if (isset($this->option['fpageshow'][$field]) && $this->option['fpageshow'][$field] == 1) {
                $header .= PHP_EOL.'            <th>' . $label . '</th>';
                if (in_array($this->option['ftype'][$field], ['select', 'radio', 'checkbox'])) {
                    $body .= PHP_EOL.'                <td><?= $this->' . $res['variable'] . '[$v[\'' . $field . '\']] ?></td>';
                } else if ($this->option['ftype'][$field] == 'date') {
                    $body .= PHP_EOL.'                <td><?= date(\'Y-m-d\', $v[\'' . $field . '\']) ?></td>';

                } else if ($this->option['ftype'][$field] == 'datetime') {
                    $body .= PHP_EOL.'                <td><?= date(\'Y-m-d H:i:s\', $v[\'' . $field . '\']) ?></td>';
                } else if ($this->option['ftype'][$field] == 'image') {
                    $body .= PHP_EOL.'                <td>';
                    $body .= PHP_EOL.'                    <?php if ($v[\'' . $field . '\']): ?>';
                    $body .= PHP_EOL.'                        <img src="<?= $v[\'' . $field . '\'] ?>" style="width: 60px;height: 60px;">';
                    $body .= PHP_EOL.'                    <?php endif; ?>';
                    $body .= PHP_EOL.'                </td>';
                } else {
                    $body .= PHP_EOL.'                <td><?= $v[\'' . $field . '\'] ?></td>';
                }
            }
            if (isset($this->option['fpagesearch1'][$field]) && $this->option['fpagesearch1'][$field] == 1) {
                $searchPer .= PHP_EOL . '    <div class="form-content">';
                $searchPer .= PHP_EOL . '        <span class="col-form-label search-label">' . $label . '</span>';
                if (!in_array($this->option['ftype'][$field], ['select', 'radio', 'checkbox'])) {
                    $searchPer .= PHP_EOL . '        <input class="form-control search-input" name="' . $field . '" value="<?= $this->params[\'' . $field . '\'] ?>">';
                } else {
                    $searchPer .= PHP_EOL . '        <select class="form-control search-input" name="' . $field . '">';
                    $searchPer .= PHP_EOL . '            <option value="">请选择</option>';
                    $searchPer .= PHP_EOL . '            <?php foreach ($this->' . $res['variable'] . ' as $k => $v): ?>';
                    $searchPer .= PHP_EOL . '                <option value="<?= $k ?>" <?= $this->params[\'' . $field . '\'] == (string)$k ? \'selected\' : \'\' ?>><?= $v ?></option>';
                    $searchPer .= PHP_EOL . '            <?php endforeach; ?>';
                    $searchPer .= PHP_EOL . '        </select>';
                }
                $searchPer .= PHP_EOL . '    </div>';
            }
            if (isset($this->option['fpagesearch2'][$field]) && $this->option['fpagesearch2'][$field] == 1) {
                $searchs[] = '\'' . $field . '\' => \'' . $label . '\'';
            }
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
        $str = str_replace('{{searchPer}}', $searchPer, $str);
        $str = str_replace('{{searchList}}', $searchList, $str);
        $str = str_replace('{{table-header}}', $header, $str);
        $str = str_replace('{{table-body}}', $body, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function getChooseList($field)
    {
        if (isset($this->option['fchoice'][$field])) {
            if ($this->option['fchoice'][$field] == 1) {
                $list = $this->option['fchoicelist'][$field] ? explode(',', $this->option['fchoicelist'][$field]) : [];
                $res = [];
                $var = $list[0];
                unset($list[0]);
                foreach ($list as $v) {
                    $arr = explode(':', $v);
                    $res[$arr[0]] = $arr[1];
                }
                return ['variable' => $var, 'list' => $res, 'type' => 1];
            } else {
                $arr = $this->option['fchoicelist'][$field] ? explode(':', $this->option['fchoicelist'][$field]) : [];
//                $wheres=explode(',',$arr[4]);
//                foreach ($wheres as $v){
//                    explode('',$v)
//                }
                $res = [
                    'type' => 2,
                    'variable' => $arr[3],
                    'table' => $arr[0],
                    'key' => $arr[1],
                    'value' => $arr[2],
                    'where' => $arr[2],
                ];
                return $res;
            }
        }
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
        $otherAssign = '';
        $parseFile = '';
        $otherDefineService = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if (in_array($this->option['ftype'][$field], ['select', 'radio', 'checkbox'])) {
                $res = $this->getChooseList($field);
                if ($res['type'] == 1) {
                    $otherDefineService .= PHP_EOL . '    public $' . $res['variable'] . ' = [';
                    foreach ($res['list'] as $key => $v) {
                        $otherDefineService .= PHP_EOL . '        \'' . $key . '\' => \'' . $v . '\',';
                    }
                    $otherDefineService .= PHP_EOL . '    ];';
                    $otherAssign .= PHP_EOL . '        $this->assign(\'' . $res['variable'] . '\', $this->' . $res['variable'] . ');';
                } else {
                    $otherAssign .= PHP_EOL . '        $' . $res['variable'] . ' = \Db::table(\'' . $res['table'] . '\')->field([\'' . $res['key'] . '\', \'' . $res['value'] . '\'])->findAll();';
                    $otherAssign .= PHP_EOL . '        $' . $res['variable'] . ' = array_column($' . $res['variable'] . ', \'' . $res['value'] . '\',\'' . $res['key'] . '\');';
                    $otherAssign .= PHP_EOL . '        $this->assign(\'' . $res['variable'] . '\', $' . $res['variable'] . ');';
                }
            } else if ($this->option['ftype'][$field] == 'image') {
                $parseFile .= PHP_EOL . '                if (!empty($_FILES[\'' . $field . '\'])) {';
                $parseFile .= PHP_EOL . '                    $file = $_FILES[\'' . $field . '\'];';
                $parseFile .= PHP_EOL . '                    $params[\'' . $field . '\'] = $this->parseFile($file);';
                $parseFile .= PHP_EOL . '                }';
            }
        }
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{otherDefineService}}', $otherDefineService, $str);
        $str = str_replace('{{otherAssign}}', $otherAssign, $str);
        $str = str_replace('{{parseFile}}', $parseFile, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);
        $str = str_replace('{{tablename}}', $this->option['name'], $str);
        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        $filename = BASE_PATH . $this->app . '/common/controller/HomeController.php';
        $file = $this->templatePath . 'home_controller';
        if (file_exists($file)) {
            $str = file_get_contents($file);
            $str = str_replace('{{app}}', $this->app, $str);
            if (!file_exists($filename)) {
                file_put_contents($filename, $str);
            }
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
        foreach ($this->option['fcomment'] as $field => $label) {
            $selectorParams .= PHP_EOL.'        if (isset($params[\'' . $field . '\']) && $params[\'' . $field . '\'] != \'\') {';
            if ($this->columnTypes[$field] == 'string') {
                $where = '[\'' . $field . '\' => [\'like\', \'%\' . $params[\'' . $field . '\'] . \'%\']]';
            } else {
                $where = '[\'' . $field . '\' => $params[\'' . $field . '\']]';
            }
            $selectorParams .= PHP_EOL.'            $selector->where(' . $where . ');';
            $selectorParams .= PHP_EOL.'        }';
        }
        $checkUnique = '';
        if (count($this->uniqueColumns)) {
            $checkUnique .= PHP_EOL . '        $selector = \Db::table(\'' . $this->table . '\');';
            $checkUnique .= PHP_EOL.'        if (isset($data[\'' . $this->primaryKey . '\']) && $data[\'' . $this->primaryKey . '\']) {';
            $checkUnique .= PHP_EOL.'            $selector->where([\'' . $this->primaryKey . '\' => [\'!=\', $data[\'' . $this->primaryKey . '\']]]);';
            $checkUnique .= PHP_EOL.'        }';
            $message = [];
            foreach ($this->uniqueColumns as $key => $vList) {
                $checkUnique .= PHP_EOL.'        $check = [];';
                foreach ($vList as $v) {
                    $checkUnique .= PHP_EOL.'        $check[\'' . $v . '\'] = $data[\'' . $v . '\'];';
                    $message[] = $this->option['fcomment'][$v];
                }
                $checkUnique .= PHP_EOL.'        $selector->where($check);';
            }
            $checkUnique .= PHP_EOL.'        $row = $selector->find();';
            $checkUnique .= PHP_EOL.'        if ($row) {';

            $checkUnique .= PHP_EOL.'            throw new \Exception(\'' . implode(',', $message) . '不能重复~\');';
            $checkUnique .= PHP_EOL.'        }';
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

    public function module()
    {
        $dir = BASE_PATH . $this->app . '/' . $this->module;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        if ($this->app == 'admin') {
            $dirList = [
                'service', 'controller', 'config', 'views'
            ];
        } else {
            $dirList = [
                'service', 'controller', 'config'
            ];
        }
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
        $filename = $dir . '/controller/BaseController.php';
        if (!file_exists($filename)) {
            $file = $this->templatePath . 'base_controller';
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
}