<?php

/**
 * 分页类
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CompactTp\Lib;

class Page {

    private $each_disNums = 5; //每页显示的条目数
    private $nums; //总条目数
    private $current_page = 1; //当前被选中的页
    private $sub_pages = 6; //每次显示的页数
    private $pageNums; //总页数
    private $page_array = array(); //用来构造分页的数组
    private $subPage_link; //每个分页的链接
    private $page_where; //分页附加条件
    private $pageLength = 6;

    /**
     * __construct是SubPages的构造函数，用来在创建类的时候自动运行.
     * @$each_disNums   每页显示的条目数
     * @nums  总条目数
     * @current_num    当前被选中的页
     * @sub_pages      每次显示的页数
     * @subPage_link   每个分页的链接
     * @subPage_type   显示分页的类型
     */

    public function __construct($param)
    {
        if(isset($_REQUEST['page'])){
            $this->current_page = $_REQUEST['page'] > 0 ? $_REQUEST['page']: 1;
        }
        if (isset($param["limit"])) {
            $this->each_disNums = intval($param["limit"]) > 1 ? intval($param["limit"]) : 1;
        }
        if (isset($param["sub_pages"])) {
            $this->sub_pages = intval($param["sub_pages"]);
        }
        /*
        $this->subPage_link = $_SERVER['PHP_SELF'] . "?page=";
        $data = array();
        foreach ($_REQUEST as $key => $val) {
            $data[] = $key . '=' . $val;
        }
        $this->page_where = implode('&', $data);
        */
    }

    function __destruct()
    {

        unset($this->each_disNums);
        unset($this->nums);
        unset($this->current_page);
        unset($this->sub_pages);
        unset($this->pageNums);
        unset($this->page_array);
        unset($this->subPage_link);
    }

    /**
     * 用来给建立分页的数组初始化的函数
     */
    function initArray()
    {
        for ($i = 0; $i < $this->sub_pages; $i++) {
            $this->page_array[$i] = $i;
        }
        return $this->page_array;
    }

    /**
     * 将分页信息封装到Query
     * @param type query
     * @return type
     */
    public function getLimit($query)
    {
        $this->each_disNums = isset($query[':limit']) ? $query[':limit'] : $this->each_disNums;
        $query[":limit"] = $this->each_disNums;
        $query[":page"] = ($this->current_page - 1) * $this->each_disNums;
        return $query;
    }

    /**
     * 初始化总记录数
     * @param type $nums
     */
    public function initTotalNum($nums)
    {
        $this->nums = intval($nums);
        $this->pageNums = ceil($this->nums / $this->each_disNums);
    }

    public function getPageConf()
    {
        return array(
            'page' => $this->current_page,
            'total_num' => $this->nums,
            'total_page' => $this->pageNums,
        );
    }
    public function setPages($url) {
        $page = $this->current_page;
        if ($page <= 0) {
            $page = 1;
        }
        $page_string = '';
        $pageTotal = $this->pageNums;//总页数

        if ($pageTotal==null) {
            /*throw new Exception('pageTotal must be a number');*/
            return false;
        }

        //简洁版页码
//		if ($showModel == 'simple') {
//			if($page == 1) {
//				$page_string .= '<a href="javascript:;">第一页</a>&nbsp;<a href="javascript:;">上一页</a>&nbsp;';
//			} else {
//				$page_string .= '<a href='.$url.'page=1>第一页</a>&nbsp;<a href='.$url.'page='.($page-1).'>上一页</a>&nbsp;';
//			}
//			if($page == $pageTotal) {
//				$page_string .= '<a href="javascript:;">下一页</a>&nbsp;<a href="javascript:;">最末页</a>';
//			} else {
//				$page_string .= '<a href='.$url.'page='.($page+1).'>下一页</a>&nbsp;<a href='.$url.'page='.$pageTotal.'>最末页</a>';
//			}
//		} else {
        //完整版页面
        //判断分页中循环的起始位置$min,$max
        $min = $max = '';
        $pageOffset = self::toEven($this->pageLength) / 2;
        if ($pageTotal < $this->pageLength) {
            $min = 1;
            $max = $pageTotal;
        } else {
            if ($page < $this->pageLength) {
                $min = 1;
                $max = $this->pageLength;
            } else {
                if (($page + $pageOffset) > $pageTotal) {
                    $min = $pageTotal - $this->pageLength + 1;
                    $max = $pageTotal;
                } else {
                    $min = $page - $pageOffset;
                    $max = $min + $this->pageLength;
                }
            }
        }

        if ($page == 1) {
            $page_string .= '<a class="first">首页</a>';
            if ($min != 1) {
                $page_string .= '<a href='.self::setUrl($url, 1).'>1</a>';
            }
        } else {
            $page_string .= '<a class="first" href='.self::setUrl($url, 1).'>首页</a>';
            $page_string .='<a class="prev" href='.self::setUrl($url, $page-1).'>上一页</a>';
        }
        for ($i=$min;$i<=$max;$i++) {
            if ($i==$page) {
                $page_string .= '<a class="bb">'.$i.'</a>';
            } else {
                $page_string .= '<a href='.self::setUrl($url, $i).'>'.$i.'</a>';
            }
        }

        if ($page!=$pageTotal) {
            $page_string .= '<a class="next" href='.self::setUrl($url, $page+1).'>下一页</a>';
            $page_string .= '<a class="end" href='.self::setUrl($url, $pageTotal).'>末页</a>';
        } else {
            $page_string .= '<a class="end">末页</a>';
        }
//		}

        if ($pageTotal == 1) {
            return '';
        }
        return $page_string;
    }
    public static function setUrl($url, $page) {
        return $url .'&page='. $page;
    }
    public static function toEven($num) {
        if (($num % 2) != 0) {
            return $num + 1;
        }
        return $num;
    }
    /**
     * 构造普通模式的分页
     * 共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页]
     */
    function subPageCss1()
    {
        $subPageCss1Str = "";
        $subPageCss1Str .= "共" . $this->nums . "条记录，";
        $subPageCss1Str .= "每页显示" . $this->each_disNums . "条，";
        $subPageCss1Str .= "当前第" . $this->current_page . "/" . $this->pageNums . "页 ";
        if ($this->current_page > 1) {
            $firstPageUrl = $this->subPage_link . "1" . $this->page_where;
            $prewPageUrl = $this->subPage_link . ($this->current_page - 1) . $this->page_where;
            $subPageCss1Str .= "[<a href='$firstPageUrl'>首页</a>] ";
            $subPageCss1Str .= "[<a href='$prewPageUrl'>上一页</a>] ";
        } else {
            $subPageCss1Str .= "[首页] ";
            $subPageCss1Str .= "[上一页] ";
        }
        if ($this->current_page < $this->pageNums) {
            $lastPageUrl = $this->subPage_link . $this->pageNums . $this->page_where;
            $nextPageUrl = $this->subPage_link . ($this->current_page + 1) . $this->page_where;
            $subPageCss1Str .= " [<a href='$nextPageUrl'>下一页</a>] ";
            $subPageCss1Str .= "[<a href='$lastPageUrl'>尾页</a>] ";
        } else {
            $subPageCss1Str .= "[下一页] ";
            $subPageCss1Str .= "[尾页] ";
        }
        return $subPageCss1Str;
    }

    /**
     * 构造经典模式的分页
     * 当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页]
     */
    function subPageCss2()
    {
        $subPageCss2Str = "";
        $subPageCss2Str .= "当前第" . $this->current_page . "/" . $this->pageNums . "页 ";

        if ($this->current_page > 1) {
            $firstPageUrl = $this->subPage_link . "1" . $this->page_where;
            $prewPageUrl = $this->subPage_link . ($this->current_page - 1) . $this->page_where;
            $subPageCss2Str .= "[<a href='$firstPageUrl'>首页</a>] ";
            $subPageCss2Str .= "[<a href='$prewPageUrl'>上一页</a>] ";
        } else {
            $subPageCss2Str .= "[首页] ";
            $subPageCss2Str .= "[上一页] ";
        }
        $a = $this->construct_num_Page();

        for ($i = 0; $i < count($a); $i++) {
            $s = $a[$i];
            if ($s == $this->current_page) {
                $subPageCss2Str .= "[<span style='color:red;font-weight:bold;'>" . $s . "</span>]";
            } else {
                $url = $this->subPage_link . $s . $this->page_where;
                $subPageCss2Str .= "[<a href='$url'>" . $s . "</a>]";
            }
        }
        if ($this->current_page < $this->pageNums) {
            $lastPageUrl = $this->subPage_link . $this->pageNums . $this->page_where;
            $nextPageUrl = $this->subPage_link . ($this->current_page + 1) . $this->page_where;
            $subPageCss2Str .= " [<a href='$nextPageUrl'>下一页</a>] ";
            $subPageCss2Str .= "[<a href='$lastPageUrl'>尾页</a>] ";
        } else {
            $subPageCss2Str .= "[下一页] ";
            $subPageCss2Str .= "[尾页] ";
        }
        return $subPageCss2Str;
    }

    /**
     * 该函数使用来构造显示的条目
     * @return type
     */
    function construct_num_Page()
    {
        if ($this->pageNums < $this->sub_pages) {
            $current_array = array();
            for ($i = 0; $i < $this->pageNums; $i++) {
                $current_array[$i] = $i + 1;
            }
        } else {
            $current_array = $this->initArray();
            if ($this->current_page <= 3) {
                for ($i = 0; $i < count($current_array); $i++) {
                    $current_array[$i] = $i + 1;
                }
            } elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1) {
                for ($i = 0; $i < count($current_array); $i++) {
                    $current_array[$i] = ($this->pageNums) - ($this->sub_pages) + 1 + $i;
                }
            } else {
                for ($i = 0; $i < count($current_array); $i++) {
                    $current_array[$i] = $this->current_page - 2 + $i;
                }
            }
        }
        return $current_array;
    }

}

?>
