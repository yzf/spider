<?php
require_once 'config.php';
//相关的辅助函数
/**
 * 输出格式
 * @param $flag int[1时输出到文件，0时输出到终端]
 */ 
function output($pro_name, $com_name, $price, $date,
    $rank, $star, $total, $five, $chipset, $core_clock, 
    $mem_int, $style, $bit, $gra_mem, $dvi, $hdmi, $flag) {
	$content = "######id######$pro_name\n" . 
        "##company##$com_name###price###$price###date###$date" . 
        "##rank##$rank##star##$star##total##$total##five##$five\n" . 
        "##chipset##$chipset##coreClock##$core_clock##MemoryInterface##$mem_int" . 
        "##style##$style##BIT##$bit##Graphic Memory##$gra_mem##DVI##$dvi##HDMI##$hdmi\n";
    if ($flag) {
		file_put_contents(OUTPUT, $content, FILE_APPEND);//线程安全
    }
    else {
    	echo $content;
    }
}
/**
 * 校验结果
 */
function check(&$value, $default = null) {
	if ($value == $default) {
		$value = NOTHING;
	}
}
/**
 * 获取字符串中的有用部分
 */
function parse_html($arr = array(), $str, $charlist = array()) {
	foreach ($arr as $a => $b) {
		$tmp = explode($a, $str);
		$str = $tmp[$b];
	}
	if ($charlist != null) {
		foreach ($charlist as $c) {
			$str = trim($str, $c);
		}
	} 
	return trim($str);
}
/**
 * 获取网页的源码
 * @param $page int[页数]
 */
function get_html_code($page) {
	while (true) {
		echo "Thread $page : Begin to get source code of page $page\n";
		$page_url = URL_PREFIX_PAGE . $page;
		$page_file = "$page/page";
		if (! file_exists($page_file)) {
			echo "Thread $page : Get page $page's information from URL:  $page_url\n";
			$page_content = file_get_contents($page_url);
		}
		else {
			echo "Thread $page : Get page $page's information from local file\n";
			$page_content = file_get_contents($page_file);
		}
		//获取该页在网页上显示的产品id,避免重复,页面有数据隐藏
		$page_tmp_result = array();
		preg_match_all(PREG_RESULT_COUNT, $page_content, $page_tmp_result);
		$page_tmp_result = explode(" ", $page_tmp_result[0][0]);
		$begin = $page_tmp_result[1];
		$end = $page_tmp_result[3];
		echo "Thread $page : Products $begin - $end ...\n";
		//检验结果
		if (($begin != (16 * ($page - 1) + 1)) || ($end != (16 * $page))) {
			echo "Thread $page : Products' number are dismatched...retry again...\n";
			continue;
		}
		file_put_contents($page_file, $page_content);
		//获取所有链接
		$page_tmp_result = array();
		preg_match_all(PREG_PRODUCTS, $page_content, $page_tmp_result);
		foreach ($page_tmp_result[0] as $r) {
			$pro_tmp = explode('"', $r);
			$pro_url = $pro_tmp[1];
			$pro_tmp = explode('?', $pro_url);
			$pro_tmp = explode( '-', $pro_tmp[1] );
			$pro_num = $pro_tmp[1];
			if ($pro_num >= $begin && $pro_num <= $end) {//获取产品信息
				$pro_file = "$page/$pro_num";
				if (! file_exists($pro_file)) {//避免重复获取
					echo "Thread $page : Get product $pro_num's information from url: $pro_url \n";
					$pro_content = file_get_contents($pro_url);
					file_put_contents($pro_file, $pro_content);
				}
				else {
					echo "Thread $page : Get product $pro_num's information from local file\n";
					$pro_content = file_get_contents($pro_file);
				}
				if (preg_match(PREG_PRO_DETAIL_URL, $pro_content, $pro_tmp_result)) {//产品获取详细信息
					$pro_parse = array('"'=>1);
					$pro_detail_url = parse_html($pro_parse, $pro_tmp_result[0]);
					$detail_file = $pro_file . "_detail";
					if (! file_exists($detail_file)) {
						echo "Thread $page : Get product $pro_num's detail from url: $pro_detail_url \n";
						$detail_content = file_get_contents($pro_detail_url);
						file_put_contents($detail_file, $detail_content);
					}
					else {
						echo "Thread $page : Get product $pro_num's detail from local file\n";
						$detail_content = file_get_contents($detail_file);
					}
				}
			}
		}
		break;
	}
	echo "####################################Page $page is over#################################\n\n";
}
/**
 * 获取产品的详细信息
 */
function get_pro_detail($url) {
	// 	$url = "details";
	$ret = array();
	$detail_content = file_get_contents($url);
	$detail_tmp_result = array();
	//图形缓存
	$detail_parse = array('>'=>1, '<'=>0, ' '=>1);
	preg_match(PREG_PRO_GRAMEM, $detail_content, $detail_tmp_result);
	$detail_gm = parse_html($detail_parse, $detail_tmp_result[0]) . "GB";
	check($detail_gm);
	//ehco "Graphic Memory:$detail_gm\n";
	$ret['gra_memory'] = $detail_gm;
	//时钟频率
	if (preg_match(PREG_PRO_CORECLOCK, $detail_content, $detail_tmp_result)) {
		$detail_parse = array('>'=>1, '<'=>0, 'core'=>0);
		$detail_clock = parse_html($detail_parse, $detail_tmp_result[0]);
		//ehco "Core Clock:$detail_clock\n";
	}
	else {
		preg_match(PREG_PRO_BASE_CLOCK, $detail_content, $detail_tmp_result);
		$detail_parse = array('>'=>1, '<'=>0, 'Clock:'=>1);
		$detail_clock = parse_html($detail_parse, $detail_tmp_result[0]);
		preg_match(PREG_PRO_BOOST_CLOCK, $detail_content, $detail_tmp_result);
		$detail_clock .= parse_html($detail_parse, $detail_tmp_result[0]);
		//ehco "Core Clock:$detail_clock\n";
	}
	check($detail_clock);
	$ret['clock'] = $detail_clock;
	//BIT
	$detail_parse = array('bit'=>0, "Bit"=>0, "BIT"=>0);
	preg_match(PREG_PRO_BIT, $detail_content, $detail_tmp_result);
	// 	print_r($detail_tmp_result);
	$detail_bit = parse_html($detail_parse, $detail_tmp_result[0], array('-'));
	check($detail_bit);
	//ehco "bit:$detail_bit\n";
	$ret['bit'] = $detail_bit;
	//Memory
	$detail_parse = array('>'=>1, 'MB'=>0);
	preg_match(PREG_PRO_MEMORY, $detail_content, $detail_tmp_result);
	// 	print_r($detail_tmp_result);
	$detail_memory = parse_html($detail_parse, $detail_tmp_result[0]);
	$detail_memory .= "MB";
	check($detail_memory, "MB");
	$detail_style = stripos($detail_tmp_result[0], "gddr5") === false ? "DDR3" : "GDDR5";
	//ehco "MemoryInterface:$detail_memory\n";
	//ehco "Style:$detail_style\n";
	$ret['mem_inter'] = $detail_memory;
	$ret['style'] = $detail_style;

	return $ret;
}
/**
 * 提取关键信息
 * @param $id int[产品id]
 */
function get_product_info($id, $flag) {
	$folder = (int)(($id / 16) + 1);
	$file = "$folder/$id";
	$detail_file = $file . "_detail";
	
	$pro_content = file_get_contents($file);
	//公司名称
	$pro_tmp_result = array();
	$pro_parse = array('>'=>1, '<'=>0);
	preg_match(PREG_PRO_COM, $pro_content, $pro_tmp_result);
	$pro_com = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_com);
	//echo "pro_company:$pro_com\n";
	//产品名称
	$pro_parse = array('>'=>1, '<'=>0);
	preg_match(PREG_PRO_NAME, $pro_content, $pro_tmp_result);
	$pro_name = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_name);
	//echo "pro_name:$pro_name\n";
	stripos($pro_name, "GeForce") !== false ? $pro_chipset = "Invida" : $pro_chipset = "AMD";
	stripos($pro_name, "dvi") !== false ? $pro_dvi = 1 : $pro_dvi = 0;
	stripos($pro_name, "hdmi") !== false ? $pro_hdmi = 1 : $pro_hdmi = 0;
	//echo "chipset:$pro_chipset\n";
	//echo "dvi:$pro_dvi\n";
	//echo "hdmi:$pro_hdmi\n";
	//价格
	$pro_parse = array('>'=>1, '<'=>0);
	preg_match(PREG_PRO_PRICE, $pro_content, $pro_tmp_result);
	$pro_price = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_price);
	//echo "price:$pro_price\n";
	//上市时间
	$pro_parse = array('</b>'=>1, '<'=>0);
	preg_match(PREG_PRO_DATE, $pro_content, $pro_tmp_result);
	$pro_date = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_date);
	//echo "date:$pro_date\n";
	//排名
	$pro_parse = array('>'=>1, '<'=>0, '#'=>1);
	preg_match(PREG_PRO_RANK, $pro_content, $pro_tmp_result);
	$pro_rank = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_rank);
	//echo "rank:$pro_rank\n";
	$pro_parse = array('>'=>1, '<'=>0, ' '=>0);
	preg_match(PREG_PRO_STAR, $pro_content, $pro_tmp_result);
	$pro_star = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_star);
	//echo "star:$pro_star\n";
	//总评分人数
	$pro_parse = array(' '=>0);
	preg_match(PREG_PRO_TOTAL, $pro_content, $pro_tmp_result);
	$pro_total = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_total);
	//echo "total:$pro_total\n";
	//评五星人数
	$pro_parse = array('>'=>1, '<'=>0);
	preg_match(PREG_PRO_FIVE, $pro_content, $pro_tmp_result);
	$pro_five = parse_html($pro_parse, $pro_tmp_result[0]);
	check($pro_parse);
	//echo "five:$pro_five\n";
	//忽略没有detail的产品
	if (! file_exists($detail_file)) {
		echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!No detail file of $id!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
		echo "####################################Product $id is over#################################\n\n";
		return;
	}
	$details = get_pro_detail($detail_file);
	output($pro_name, $pro_com, $pro_price, $pro_date,
		$pro_rank, $pro_star, $pro_total, $pro_five,
		$pro_chipset, $details['clock'], $details['mem_inter'],
		$details['style'], $details['bit'], $details['gra_memory'],
		$pro_dvi, $pro_hdmi, 1);

	echo "####################################Product $id is over#################################\n\n";
}




















