<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/13
 * Time: 2:34 PM
 */
namespace api\home\controller;
use core\Controller;
use core\Db;

class IndexController extends Controller{

    public function index(){
        $sql='show columns from tbl_test';
        $a=Db::table('Menu')->findAll($sql);

      echo 1;die;
    }
}