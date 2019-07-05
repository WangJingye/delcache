<?php

namespace common\extend\excel;

class SpreadExcel
{
    /**
     * @param $data
     * @param $path
     */
    public static function exportExcelToFile($data, $path)
    {
        array_unshift($data['data'], $data['info']);
        $writer = new XLSXWriter();
        $writer->setAuthor('DELCACHE');
        foreach ($data['data'] as $row){
            $writer->writeSheetRow('Sheet1', $row);
        }
        $writer->writeToFile($path);
    }
}