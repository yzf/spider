<?php
//配置文件，配置一些常量
define("OUTPUT", "output");

define("PAGE_MAX", 1);
define("URL_PREFIX_PAGE", "http://www.amazon.com/s/ref=sr_pg_1?" .
		"rh=n%3A172282%2Cn%3A%21493964%2Cn%3A541966%2Cn%3A19387".
		"0011%2Cn%3A284822%2Cp_n_availability%3A1248800011&" .
		"ie=UTF8&qid=1351691800&page=");
define("PREG_RESULT_COUNT", "/Showing.*?Results/");
define("PREG_PRODUCTS", "/<a.*?alt=\"Product Details\"\/>.*?<\/a>/");

define("PREG_PRO_DETAIL_URL", "/<a.*?>See more technical details/");

define("PREG_PRO_NAME", "/<span id=\"btAsinTitle\" >.*?<\/span>/");
define("PREG_PRO_COM", "/by&#160;<a.*?>.*?<\/a>/");
define("PREG_PRO_PRICE", "/<b class=\"priceLarge\">.*?<\/b>/");
define("PREG_PRO_DATE", "/<li><b> Date first available at Amazon.com:<\/b>.*?<\/li>/");
define("PREG_PRO_RANK", "/<span class=\"zg_hrsr_rank\">.*?<\/span>/");
define("PREG_PRO_STAR", "/gry txtnormal acrRating\">.*?out of 5 stars<\/div>/");
define("PREG_PRO_TOTAL", "/\d+ customer reviews<\/a>\)<\/span><\/span>/");
define("PREG_PRO_FIVE", "/<div class=\"histoCount fl gl10 ltgry txtnormal\".*?>.*?<\/div>/");
define("PREG_PRO_CORECLOCK", "/<li>.*?core clock<\/li>/");
define("PREG_PRO_BASE_CLOCK", "/<li>[Bb][Aa][Ss][Ee] [Cc][Ll][Oo][Cc][Kk].*?<\/li>/");
define("PREG_PRO_BOOST_CLOCK", "/<li>[Bb][Oo][Oo][Ss][Tt] [Cc][Ll][Oo][Cc][Kk]:.*?<\/li>/");
define("PREG_PRO_MEMORY", "/<li>.*?DDR.*?<\/li>/");
define("PREG_PRO_BIT", "/[0-9|\-]* ?[Bb][Ii][Tt].*?<\/li>/");
define("PREG_PRO_GRAMEM", "/[Gg][Rr][Aa][Pp][Hh][Ii][Cc][Ss] [Rr][Aa][Mm].*?<\/b>.*?<\/li>/");

//默认值 爬取不到时使用
define("NOTHING", "None");