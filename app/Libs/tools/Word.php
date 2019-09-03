<?php
namespace App\Libs\tools;
class word
{
    function start()
    {
        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:w="urn:schemas-microsoft-com:office:word"
            xmlns="http://www.w3.org/TR/REC-html40">';
    }

    function save($path)
    {

        echo "</html>";
        $data = ob_get_contents();
        ob_end_clean();

        $this->outfile($path, $data);

    }

    function outfile($fn, $data)
    {
        $fp = fopen('_temp.doc', "wb");
        header("Content-Type: application/msword");
        if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition:  attachment; filename="' . $fn . '"');
        } elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename*="utf8' . $fn . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $fn . '"');
        }
//			echo $data;
        $datas = str_split($data, 1024);
// 计数器
        $cnt = 0;
// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1024;

// 逐行取出数据，不浪费内存
        $count = count($datas);
        for ($t = 0; $t < $count; $t++) {

            $cnt++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = $datas[$t];
//			foreach ($row as $i => $v) {
//				$row[$i] = iconv('utf-8', 'gbk', $v);
//			}
            echo $row;
            unset($row);
        }


        fclose('_temp.doc');
    }

    function wirtefile($fn, $data)
    {
        $fp = fopen($fn, "wb");
        fwrite($fn);
        fclose($fn);
    }


}
