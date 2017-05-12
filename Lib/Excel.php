<?php
namespace CompactTp\Lib;

class Excel
{
    protected static $excel;

    public static function export($data,$name='excel')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
        header('Cache-Control: max-age=0');
        /*$titleConf = [
            "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"
        ];*/
        $titleConf=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $objPHPExcel=self::getExcelObj();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        /*$objPHPExcel->getProperties()->setCreator("转弯的阳光")
            ->setLastModifiedBy("转弯的阳光")
            ->setTitle("数据EXCEL导出")
            ->setSubject("数据EXCEL导出")
            ->setDescription("备份数据")
            ->setKeywords("excel")
            ->setCategory("result file");*/
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        $objSheet=$objPHPExcel->getActiveSheet();//获得当前活动sheet的操作对象
        $objSheet->setTitle("demo");//给当前活动sheet设置名称
        $colNum=count($data[0]);  #列数
        $rowNum=count($data);     #行数
        echo '<table>'.'<tr>';
        for($i=0;$i<$rowNum;$i++){   #0,1,2,3
            for($j=0;$j<$colNum;$j++){      #0,1,2,3,4,5,6,7,8,9,10
                echo '<td>'.$data[$i][$j].'</td>';
                //echo $data[$i][$j].'    ';
                if($j%12==0 && $j!=0){
                    echo '</tr><tr>';
                }
                $objSheet->setCellValue($titleConf[$j].($i+1), $data[$i][$j]);
            }
        }
        echo '</tr></table>';

        //$objSheet=$objPHPExcel->getActiveSheet();//获得当前活动sheet的操作对象
        //$objSheet->setTitle("demo");//给当前活动sheet设置名称
        //$objSheet->fromArray($data);
        //$objPHPExcel->getActiveSheet()->setTitle($name);
        //$objPHPExcel->setActiveSheetIndex(0);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($name.'xlsx');
        exit;

    }
    /**
     * 获得Excel类
     */
    public static function getExcelObj()
    {
        if(!self::$excel){
            require_once dirname(__DIR__).'/Lib/phpexcel/PHPExcel.php';
            $objPHPExcel = new \PHPExcel();
            self::$excel=$objPHPExcel;
        }
        return self::$excel;
    }
}