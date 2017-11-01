<?php
/**
 *
 * アジア各国 国別情報
 *
 */
function get_the_content_country($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.full_name
		, c.area_id
		, c.flag
		, c.capital
		, c.city
		, c.volt
		, mu.common_val AS flag_url

		, t.minus_flg
		, t.time_difference
		, t.utc
		, mt.common_val AS time_color

		, rt.rate
		, rt.english_rate

		FROM  $wpdb->m_country c

        LEFT JOIN $wpdb->m_time t
        ON c.country_id = t.country_id
		LEFT JOIN $wpdb->m_common mt
        ON CONCAT(IF(t.minus_flg = '1', '-',''),t.time_difference) = mt.common_name

		LEFT JOIN $wpdb->m_common mu
          ON mu.common_id = 101
         AND mu.common_subid = 1

        LEFT JOIN $wpdb->m_rate rt
		ON c.country_id = rt.country_id

		WHERE c.country_id = %s
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);
	if (count($countrys) == 0) {
		return "";
	}
	$country = $countrys[0];

	$flag_img = "";
	if (strlen($country['flag_url']) > 0) {
		$flag_img = sprintf('<img style="margin:0px;margin-right:10px;padding:0;border:solid 1px #000000" border="0" alt="%s" src="%s" width="40" height="26">'
			,$country['flag']
			, sprintf($country['flag_url'],$country['flag'])
		);
	}

	$time_difference = '';
	$utc = "";
	if (count($countrys) == 1) {
		$start = explode(":", $country['time_difference']);
		if($country['minus_flg']=='1') {
			$time_difference = '-';
		}
		$time_difference .= intval($start[0]) ."時間";
		$hour = 9 - intval($start[0]); 
		$utc = "UTC+" .$hour;
		if($start[1] != "00"){
			$time_difference .= intval($start[1]) ."分";
			$min = 60 - intval($start[1]);
			$hour--;
			$utc = "UTC+" .$hour .":" .$min;
		}
	} else {
		$time_link = sprintf('<a href="#%s">%s時間へ</a>',$country['english_name'] ,$country['country_name'] );
		$time_difference = $time_link;
		$utc = $time_link;
	}
	$city = get_the_content_country_city($country_id);
	$language = get_the_content_country_language($country_id);
	$religion = get_the_content_country_religion($country_id);
	$plug = get_the_content_country_plug($country_id);

	$output .= sprintf('
	<h4 class="tbhd4">基本情報</h4>
	<table cellpadding="3" class="tablecss01" style"display:block;">
		<tr>	<th class="tbth">国旗</th><td style="padding:5px;">%s</td></tr>
		<tr>	<th class="nowrap">国名</th>	<td style="width:100%%">%s</td></tr>
		<tr>	<th class="nowrap">英語国名</th>	<td style="width:100%%">%s</td></tr>
		%s
		<tr>	<th class="nowrap">日本との時差</th>	<td style="width:100%%;%s">%s</td></tr>
		<tr>	<th class="nowrap">協定世界時(UTC)</th>	<td style="width:100%%;%s">%s</td></tr>
		<tr>	<th class="nowrap">公用語</th>	<td style="width:100%%;">%s</td></tr>
		<tr>	<th class="nowrap">主な宗教</th>	<td style="width:100%%;">%s</td></tr>
		<tr>	<th class="nowrap">コンセント</th>	<td style="width:100%%;">%s</td></tr>
		<tr>	<th class="nowrap">電圧</th>	<td style="width:100%%">%s</td></tr>
	</table>'

	,$flag_img
	,$country['full_name'] 
	,$country['english_name'] 
	,$city
	,$country['time_color'] 
	,$time_difference
	,$country['time_color'] 
	,$utc
	,$language
	,$religion
	,$plug 
	,$country['volt']
	);
	
	$output .= get_the_content_time_difference_city($country_id);
	$output .= get_the_content_country_safety($country_id);
	$output .= get_the_content_country_rate($country['rate'] , $country['english_rate'] );
	$output .= get_the_content_country_visa($country_id);
	$output .= get_the_content_country_heritage($country_id);
	$output .= get_the_content_country_book($country_id);
	$output .= get_the_content_adsbygoogle();
	$output .= get_the_content_country_Flights($country_id);
	$output .= get_the_content_country_ticket($country_id);
	$output .= get_the_content_country_hotel($country_id);
	$output .= get_the_content_country_tour($country_id);
	$output .= get_the_content_country_option($country_id);
	$output .= get_the_content_country_introduction($country_id);
	$output .= get_the_content_country_weather($country_id);
	return $output;
}

/**
 *
 * アジア各国 国別情報 都市
 *
 */
function get_the_content_country_city($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
			c.country_id
			, c.city_id
			, c.city_name
		 FROM $wpdb->m_city c
		WHERE c.country_id = %s
		ORDER BY c.city_id
		LIMIT 4
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$citys = bzb_object2array($results1);

	$cnt = count($citys);
	if ($cnt > 0) {
		$output .= sprintf('<tr>	<th class="nowrap">首都</th>	<td style="width:100%%">%s</td></tr>'
		,$citys[0]['city_name']);
	}
	$city = "";
	if ($cnt > 1) {
		$city = $citys[1]['city_name'];
	}
	if ($cnt > 2) {
		$city .= "<br/>" .$citys[2]['city_name'];
	}
	if ($cnt > 3) {
		$city .= "<br/>" .$citys[3]['city_name'];
	}
	if ($city != "") {
		$output .= sprintf('
		<tr>	<th class="nowrap">主な都市</th>	<td style="width:100%%">%s</td></tr>'
		,$city);
	}

	return $output;
}

/**
 *
 * アジア各国 国別情報 公用語
 *
 */
function get_the_content_country_language($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
			l.country_id
			, l.language_common_subid
			, IFNULL(ml.common_name, l.language_name) AS language_name
			, ml.common_val AS language_color
		 FROM $wpdb->m_language l
		 LEFT JOIN $wpdb->m_common ml
           ON ml.common_id = 2
          AND ml.common_subid = l.language_common_subid
		WHERE l.country_id = %s
		ORDER BY l.language_common_subid
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$languages = bzb_object2array($results1);

	$flg = false;
	foreach ($languages as $language) {
		if ($flg) $output .= " 、";
		$output .= sprintf('<span style="%s">%s</span>'
		,$language['language_color'] 
		,$language['language_name'] 
		);
		$flg = true;
	}

	return $output;
}

/**
 *
 * アジア各国 国別情報 主な宗教
 *
 */
function get_the_content_country_religion($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
		  r.country_id
        , mr.common_name AS religion_name
        , mr.common_val AS religion_color 
		 FROM $wpdb->m_religion r
         LEFT JOIN $wpdb->m_common mr
           ON mr.common_id = 3
          AND mr.common_subid = r.religion_common_subid
		WHERE r.country_id = %s
		ORDER BY r.religion_common_subid
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$religions = bzb_object2array($results1);

	$flg = false;
	foreach ($religions as $religion) {
		if ($flg) $output .= " 、";
		$output .= sprintf('<span style="%s">%s</span>'
		,$religion['religion_color'] 
		,$religion['religion_name'] 
		);
		$flg = true;
	}

	return $output;
}

/**
 *
 * アジア各国 国別情報 プラグ
 *
 */
function get_the_content_country_plug($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
		  p.country_id
        , p.plug_subid
        , mp.common_name AS plug_name
        , mp.common_val AS plug_color

		FROM  $wpdb->m_plug p
		LEFT JOIN $wpdb->m_common mp
          ON mp.common_id = 5
         AND mp.common_subid = p.plug_subid

		WHERE p.country_id = %s
		ORDER BY p.plug_subid
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$plugs = bzb_object2array($results1);

	$flg = false;
	foreach ($plugs as $plug) {
		if ($flg) $output .= " 、";
		$output .= sprintf('<span style="%s">%s</span>'
		,$plug['plug_color'] 
		,$plug['plug_name'] 
		);
		$flg = true;
	}

	return $output;
}

/**
 *
 * アジア各国 国別情報 危険情報
 *
 */
function get_the_content_country_safety($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
		  c.country_id

        , g2016.gpi2016 AS gpi
        , m2016.common_val AS gpi_color
        , g2015.gti2015 AS gti
        , m2015.common_val AS gti_color

        , s.max_level_id
        , m.common_name AS safety_name
        , m.common_val AS safety_color
		, s.capital_level_id
        , m2.common_name AS safety_name2
        , m2.common_val AS safety_color2
		, s.safety_url_id AS safety_url 
		, mu.common_val AS url_format

		FROM  $wpdb->m_country c

        LEFT JOIN $wpdb->m_safety s
          ON c.country_id = s.country_id
		LEFT JOIN $wpdb->m_common m
		  ON m.common_id = 8
		 AND m.common_subid = s.max_level_id
		LEFT JOIN $wpdb->m_common m2
		  ON m2.common_id = 8
		 AND m2.common_subid = s.capital_level_id

        LEFT JOIN(
            SELECT country_id, gpi2016, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
             LEFT JOIN $wpdb->m_common
               ON common_id = 6
              AND common_subid < gpi2016
            GROUP BY country_id, gpi2016
        ) g2016
          ON g2016.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2016
    	  ON m2016.common_id = 6
         AND m2016.common_subid = g2016.common_subid

        LEFT JOIN(
            SELECT country_id, gti2015, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 7
              AND common_subid < gti2015
            GROUP BY country_id, gti2015
        ) g2015
          ON g2015.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2015
          ON m2015.common_id = 7
         AND m2015.common_subid = g2015.common_subid

		LEFT JOIN $wpdb->m_common mu
          ON mu.common_id = 101
         AND mu.common_subid = 2

		WHERE c.country_id = %s
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);
	$country = $countrys[0];

	$url = "";
	if (strlen($country['safety_url']) > 0) {
		$url = sprintf('<a href="%s" target="_blank">危険情報</a>'
					, sprintf($country["url_format"],$country['safety_url'])
		);
	}

	$output = sprintf('
	<h4 class="tbhd4">危険情報</h4>
	<table cellpadding="3" class="tablecss01">
		<tr>  <th style="white-space:nowrap">外務省ページ</th>  <td style="width:100%%;">%s</td></tr>
		<tr>  <th style="white-space:nowrap">国内最高危険レベル</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
		<tr>  <th style="white-space:nowrap">首都の危険レベル</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
		<tr>  <th class="orik">世界平和度指数(2016年)</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
		<tr>  <th class="orik">世界テロ指数(2015年)</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
	</table>'
		
	,$url
	,$country['safety_color'] 
	,$country['safety_name'] 
	,$country['safety_color2'] 
	,$country['safety_name2'] 
	,$country['gpi_color'] 
	,(strlen($country['gpi']) > 0) ? $country['gpi']."位" : 'ー'
	,$country['gti_color'] 
	,(strlen($country['gti']) > 0) ? $country['gti']."位" : 'ー'
	);
	return $output;
}

/**
 *
 * アジア各国 国別情報 通貨
 *
 */
function get_the_content_country_rate($rate, $english_rate) {
	global $wpdb;

	$reg = '/<span class=bld>(.*?) JPY<\/span>/';
	$yen="";
	$yhtml = sprintf(
		'https://finance.google.com/finance/converter?a=1&from=%s&to=JPY'
		,$english_rate);

	if ($yhtml != "") {
		$get_yhtml = file_get_contents($yhtml);
		if($get_yhtml === FALSE){
		} else {
			if(preg_match($reg, $get_yhtml, $ymatch)){
				$yen = sprintf('%s円', $ymatch[1]);
			}
		}
	}

	$dhtml = sprintf(
		'https://finance.google.com/finance/converter?a=1&from=USD&to=%s'
		,$english_rate);

	$doll="";
	if ($dhtml != "") {
		$get_dhtml = file_get_contents($dhtml);
		if($get_dhtml === FALSE){
		} else {
			$reg = sprintf('/<span class=bld>(.*?) %s<\/span>/', $english_rate);
			if(preg_match($reg, $get_dhtml, $match)){
				$doll = sprintf('%s%s',$match[1],$rate);
			}
		}
	}

	$output .= sprintf('
	<h4 class="tbhd4">通貨</h4>  
	<table cellpadding="3" class="tablecss01">
		<tr>  <th class="tbth">通貨名</th>  <td>%s</td></tr>
		<tr>  <th>通貨コード</th>  <td>%s</td></tr>
		<tr>  <th>1%s</th>  <td>%s</td></tr>
		<tr>  <th>1ドル</th>  <td>%s</td></tr>
	</table>'
	,$rate
	,$english_rate
	,$rate
	,$yen
	,$doll
	);

	return $output;
}

/**
 *
 * アジア各国 国別情報 ビザ
 *
 */
function get_the_content_country_visa($country_id) {
	global $wpdb;

	$sql1 = sprintf("
		SELECT 
		  v.country_id
		, v.necessary
		, v.arrival
		, v.net
		, v.day
		, v.price
		, v.e_site
		, v.note
		FROM  $wpdb->m_visa v
		WHERE v.country_id = %s
		"
	,$country_id
	);

	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);
	$country = $countrys[0];

	$output .= '
	<h4 class="tbhd4">ビザ</h4>  
	<table cellpadding="3" class="tablecss01">
	';

	if ($country['necessary'] == 1) { 
		$output .= sprintf('  
		<tr>  <th class="tbth">ビザ</th>  <td>%s</td></tr>
		<tr>  <th>ビザ不要滞在日数</th>  <td>%s</td></tr>'
		,"不要"
		,$country['day'] 
		);
	} else { 
		$necessary = '<td style="font-weight:bold;color:red;">必要';
		if ($country['necessary'] != 2) {
			$necessary = '<td><a href="#kome1">ビザについてへ</a>';
		}
		$arrival = "";
		if ($country['arrival']==1) {
			$arrival = '<td style="font-weight:bold;color:darkgreen">◯</td>';
		} else if ($country['arrival']==2) {
			$arrival = '<td style="font-weight:bold;color:red">×</td>';
		} else {
			$arrival = '<td><a href="#kome1">ビザについてへ</a>';
		}
		
		$net = "";
		if ($country['net']==1) {
			if (strlen($country['e_site']) > 0) {
				$net = sprintf('<td><a href="%s" target="_blank">サイトへ</a>'
						,$country['e_site']);
			} else {
				$net = '<td style="font-weight:bold;color:darkgreen">◯';
			}
		} else if ($country['net']==2) {
			$net = '<td style="font-weight:bold;color:red">×';
		} else {
			$net = '<td><a href="#kome1">ビザについてへ</a>';
		}

		$output .= sprintf('  
		<tr>  <th class="tbth">ビザ</th>  %s</td></tr>
		<tr>  <th>現地取得</th>  %s</tr>
		<tr>  <th>ネット取得</th>  %s</td></tr>
		'
		,$necessary
		,$arrival 
		,$net
		);
		if ($country['price']=="*") {
			$output .= '  
		<tr>  <th>観光ビザ滞在日数・価格</th>  <td><a href="#kome1">ビザについてへ</a></td></tr>
			';
		} else {
			$output .= sprintf('  
		<tr>  <th>観光ビザ滞在日数</th>  <td>%s</td></tr>
		<tr>  <th>観光ビザ価格</th>  <td>%s</td></tr>
			'
			,$country['day'] 
			,$country['price']
			);				
		}
	}

	$output .= '
	</table>';

	if(strlen($country['note']) > 0) {
		$output .= sprintf('
	<h6 style="padding-top:2em;margin-top:-2em;">
		<span id="kome1" style="padding:0 5px;border-left:5px solid #ff8c00;">ビザについて</span>
	</h6>
	<p style="font-weight:bold;">%s</p>'
		,$country['note']);
	}

	return $output;
}

/**
 *
 * アジア各国 国別情報 世界遺産
 *
 */
function get_the_content_country_heritage($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  h.heritage_subid
			, h.heritage_name 
		 FROM $wpdb->m_heritage h
		WHERE h.country_id=%s
		ORDER BY h.heritage_subid, h.heritage_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$heritages = bzb_object2array($results);

	$output1 = get_the_content_country_heritage_sub($heritages, "1", "文化遺産");
	$output2 = get_the_content_country_heritage_sub($heritages, "2", "自然遺産");
	$output3 = get_the_content_country_heritage_sub($heritages, "3", "複合遺産");

	$output = sprintf('
	<h4 class="tbhd4">世界遺産</h4>  
	<table cellpadding="3" class="tablecss01">
		<tr>  <th class="tbth">世界遺産数合計</th>  <td>%s</td></tr>
		%s
		%s
		%s
	</table>'
	,count($heritages)
	,$output1
	,$output2
	,$output3
	);

	return $output;
}


/**
 *
 * アジア各国 国別情報 世界遺産 種類別
 *
 */
function get_the_content_country_heritage_sub($heritages, $num, $name) {
	global $wpdb;

	// 検索条件
	$conditions = [
		"heritage_subid" => [$num],
	];
	// 検索実行
	$heritages1 = array_filter($heritages, call_user_func(function($conditions) {
		return function($heritage) use($conditions) {
			return in_array($heritage["heritage_subid"], $conditions["heritage_subid"]);
		};
	}, $conditions));

	//キーが飛び飛びになっているので、キーを振り直す
	$heritages2 = array_values($heritages1);

	$output = "";
	if(count($heritages2) > 0) {
		$output = sprintf('<tr>  <th rowspan="%s">%s</th>  <td>%s</td></tr>'
			,count($heritages2)
			,$name
			,$heritages2[0]["heritage_name"]
		);
	}
	for ($i=1; $i < count($heritages2); $i++) {
		$output .= sprintf('
		<tr><td>%s</td></tr>'
		,$heritages2[$i]["heritage_name"]
		);		
	}

	return $output;
}


/**
 *
 * アジア各国 国別情報 紹介サイト
 *
 */
function get_the_content_country_introduction($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  m.common_name 
			, s.site_url
			, s.site_val
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_site s
		   ON s.country_id = %s
		  AND s.city_id = 0
		  AND m.common_subid = s.site_id
		WHERE m.common_id = 10
		ORDER BY s.site_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$sites = bzb_object2array($results);
	if (count($sites)== 0) return "";

	$output = sprintf('
	<h4 class="tbhd4">旅行ガイド・まとめサイト %s</h4>  
	<table cellpadding="3" class="tablecss01 ori">
	'
	,count($sites)
	);
	foreach ($sites as $site) {
		$url = $site["site_url"];
		if(mb_substr($site["site_url"],0,1)=="<") {
			if(strpos($_SERVER["HTTP_HOST"], "localhost") !== false) {
				$url = $site["site_val"];
			}
		} else {
			$url = sprintf('<a href="%s" target="_blank" rel="nofollow">%s</a>'
			,$site["site_url"]
			,$site["site_val"]
			);
		}

		$output .= sprintf('
		<tr>  <th class="tbth afimg">%s</th>  <td>%s</td></tr>'
		,$site["common_name"]
		,$url
		);
	}

	$output .= '
	</table>';

	return $output;
}


/**
 *
 * アジア各国 国別情報 ガイドブック
 *
 */
function get_the_content_country_book($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  m.common_name 
			, b.book_id
			, b.book_sub_id
			, MAX(IF(b.book_url_id=0,b.book_url,'')) AS url0
			, MAX(IF(b.book_url_id=1,b.book_url,'')) AS url1
			, MAX(IF(b.book_url_id=2,b.book_url,'')) AS url2
			, MAX(IF(b.book_url_id=0,b.book_val,'')) AS val0
			, MAX(IF(b.book_url_id=1,b.book_val,'')) AS val1
			, MAX(IF(b.book_url_id=2,b.book_val,'')) AS val2
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_book b
		   ON country_id = %s
		  AND m.common_subid = b.book_id
		WHERE m.common_id = 9
		GROUP BY m.common_name, b.book_id, b.book_sub_id
		ORDER BY b.book_id, b.book_sub_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$books = bzb_object2array($results);
	if (count($books)== 0) return "";

	$sql2 = sprintf("
		SELECT 
			  m.common_name 
			, b.book_id
			, count(b.book_id) AS count
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_book b
		   ON country_id = %s
		  AND m.common_subid = b.book_id
		WHERE m.common_id = 9
		  AND b.b.book_url_id <> 0
        GROUP BY m.common_name, b.book_id 
		ORDER BY b.book_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);

	$output = sprintf('
	<h4 class="tbhd4">ガイドブック</h4>  
	<table cellpadding="3" class="tablecss02 ori mg0">
	'
	);
	for ($r = 0; $r < count($books); $r++) {
		$url0 = $books[$r]["url0"];
		$url1 = $books[$r]["url1"];
		$url2 = $books[$r]["url2"];
		if(strpos($_SERVER["HTTP_HOST"], "localhost") !== false)
		{
			$url0 = "";
			$url1 = $books[$r]["val1"];
			$url2 = $books[$r]["val2"];
		}

		if ($books[$r]["url0"] == '') {
			if (strlen($books[$r]["url1"]) > 0 && strlen($books[$r]["url2"]) > 0) {
				$output .= sprintf('
			<tr>
				<td colspan="2"><div class="mg0">%s</div></td>
			</tr>
			<tr>
				<td colspan="2"><div class="mg0">%s</div></td>
			</tr>'
				,$url1
				,$url2
			);
			} else {
				$output .= sprintf('
			<tr>
				<td colspan="2"><div class="mg0">%s</div></td>
			</tr>'
				,(strlen($url2) > 0) ? $url2 : $url1
				);	
			}		
		} else {
			if (strlen($books[$r]["url1"]) > 0 && strlen($books[$r]["url2"]) > 0) {
				$output .= sprintf('
				<tr>
					<td rowspan="2" style="border-right:none;width:130px;"><div class="mg0">%s</div></td>
					<td style="border-left:none;border-bottom:none;"><div class="mg0">%s</div></td>
				</tr>
				<tr>
					<td style="border-left:none;border-top:none;"><div class="mg0">%s</div></td>
				</tr>'
					,$url0
					,$url1
					,$url2
				);
			} else {
				$output .= sprintf('
				<tr>
					<td style="border-right:none;"><div class="mg0">%s</div></td>
					<td style="border-left:none;"><div class="mg0">%s</div></td>
				</tr>'
					,$url0
					,(strlen($url2) > 0) ? $url2 : $url1
				);				
			}
		}
	}

	$output .= '
	</table>';

	return $output;
}


/**
 *
 * アジア各国 国別情報 フライト情報
 *
 */
function get_the_content_country_Flights($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			 f.country_id
			,f.departure		AS departure_code
			,ad.airport_name	AS departure_name
			,f.arrival			AS arrival_code
			,aa.airport_name	AS arrival_name
			,f.schedule_url
		 FROM $wpdb->m_flights f
		LEFT JOIN $wpdb->m_airport ad
		   ON ad.airport_code = f.departure
		LEFT JOIN $wpdb->m_airport aa
		   ON aa.airport_code = f.arrival
		WHERE f.country_id = %s
		ORDER BY ad.airport_id,aa.airport_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$flights = bzb_object2array($results);

	$sql2 = sprintf("
		SELECT 
			  ad.airport_id
			, f.departure
			, COUNT(f.departure) AS count
		 FROM $wpdb->m_flights f
		INNER JOIN $wpdb->m_airport ad
		   ON ad.airport_code = f.departure
		WHERE f.country_id = %s
		GROUP BY ad.airport_id, f.departure
		ORDER BY ad.airport_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);

	$sql3 = "
		SELECT 
		  m.common_val AS url_format
		FROM  $wpdb->m_common m
        WHERE m.common_id = 101
          AND m.common_subid = 3
	";
	$results3 = $wpdb->get_results($sql3);
	$url_formats = bzb_object2array($results3);

	$output = sprintf('
	<h4 class="tbhd4">日本からの直行便情報</h4>');
	if (count($cnts)== 0) {
		$output .= sprintf('
	<table cellpadding="3" class="tablecss01">
		<tr><th></th><td>なし</td></tr>
	</table>'
		);
		return $output;
	}

	$output .= sprintf('
	<table cellpadding="3" class="tablecss01">
		<tr>
			<th>出発地</th>
			<th>到着地</th>
			<th style="margin:0px !important; padding:0px !important;"><img border="0" alt="flyteam" src="http://flyteam.jp/img/logo_ft_88x31.jpg"  style="margin:0px !important; padding:0px !important;text-align:center !important;vertical-align:middle !important;"></th>
		</tr>'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  
			<td class="nowrap" style="width:110px" rowspan="%s">%s(%s)</td>
			<td class="nowrap" style="">%s(%s)</td>
			<td class="nowrap" style="width:200px"><a href="%s" target="_blank" rel="nofollow">%s</a></td>
		</tr>'
		,$cnts[$i]["count"]
		,$flights[$r]["departure_name"]
		,$flights[$r]["departure_code"]
		,$flights[$r]["arrival_name"]
		,$flights[$r]["arrival_code"]
		,sprintf($url_formats[0]["url_format"],$flights[$r]["schedule_url"])
		,"時刻表"
		);

		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr>
			<td class="nowrap">%s(%s)</td>
			<td class="nowrap"><a href="%s" target="_blank" rel="nofollow">%s</a></td>
		</tr>'
			,$flights[$r]["arrival_name"]
			,$flights[$r]["arrival_code"]
			,sprintf($url_formats[0]["url_format"],$flights[$r]["schedule_url"])
			,"時刻表"
			);
			$r++;		
		}
	}

	$output .= '
	</table>';

	return $output;
}


/**
 *
 * アジア各国 国別情報 航空券予約サイト
 *
 */
function get_the_content_country_ticket($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  t.site_id
			, t.city_id
			, t.ticket_url
			, CASE WHEN t.city_id=0 THEN c.country_name ELSE ct.city_name END AS city_name
 		 FROM  $wpdb->m_country c
		 LEFT JOIN $wpdb->m_ticket t
		   ON c.country_id = t.country_id
		 LEFT JOIN $wpdb->m_city ct
		   ON c.country_id = ct.country_id
          AND ct.city_id = t.city_id
		WHERE c.country_id = %s
        ORDER BY t.site_id, t.city_id	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$tickets = bzb_object2array($results);

	$sql2 = sprintf("
		SELECT 
			  m.common_name 
			, t.site_id
			, count(t.site_id) AS count
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_ticket t
		   ON country_id = %s
		  AND m.common_subid = t.site_id
		WHERE m.common_id = 11
        GROUP BY m.common_name, t.site_id 
		ORDER BY t.site_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);
	if (count($cnts)== 0) return "";

	$output = sprintf('
	<h4 class="tbhd4">航空券を探す</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$url = "";
		if(strpos($tickets[$r]["ticket_url"], 'http') === 0)
		{
			$url = sprintf('<a href="%s" target="_blank" rel="nofollow">%s行き航空券を探す</a>'
			, $tickets[$r]["ticket_url"]
			, $tickets[$r]["city_name"]);
		} else {
			$url = localval($tickets[$r]["ticket_url"], $tickets[$r]["city_name"]."行き航空券を探す");	
		}
	
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth afimg" style="">%s</th> <td>%s</td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$url
		);

		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			if(strpos($tickets[$r]["ticket_url"], 'http') === 0)
			{
				$url = sprintf('<a href="%s" target="_blank" rel="nofollow">%s行き航空券を探す</a>'
				, $tickets[$r]["ticket_url"]
				, $tickets[$r]["city_name"]);
			} else {
				$url = localval($tickets[$r]["ticket_url"], $tickets[$r]["city_name"]."行き航空券を探す");	
			}
			$output .= sprintf('
		<tr><td>%s</td></tr>'
			,$url
			);
			$r++;		
		}
	}

	$output .= '
	</table>';
	return $output;
}


/**
 *
 * アジア各国 国別情報 ホテル予約サイト
 *
 */
function get_the_content_country_hotel($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  h.site_id
			, h.city_id
			, h.hotel_url
			, CASE WHEN h.city_id=0 THEN c.country_name ELSE ct.city_name END AS city_name
 		 FROM  $wpdb->m_country c
		 LEFT JOIN $wpdb->m_hotel h
		   ON c.country_id = h.country_id
		 LEFT JOIN $wpdb->m_city ct
		   ON c.country_id = ct.country_id
          AND ct.city_id = h.city_id
		WHERE c.country_id = %s
        ORDER BY h.site_id, h.city_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$hotels = bzb_object2array($results);

	$sql2 = sprintf("
		SELECT 
			  m.common_name 
			, h.site_id
			, count(h.site_id) AS count
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_hotel h
		   ON country_id = %s
		  AND m.common_subid = h.site_id
		WHERE m.common_id = 12
        GROUP BY m.common_name, h.site_id 
		ORDER BY h.site_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);
	if (count($cnts)== 0) return "";

	$output = sprintf('
	<h4 class="tbhd4">ホテルを探す</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$url = localval($hotels[$r]["hotel_url"], $hotels[$r]["city_name"]."のホテルを探す");	
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth afimg" style="">%s</th>  <td>%s</td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$url
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$url = localval($hotels[$r]["hotel_url"], $hotels[$r]["city_name"]."のホテルを探す");	
			$output .= sprintf('
		<tr><td>%s</td></tr>'
			,$url
			);
			$r++;		
		}
	}

	$output .= '
	</table>';

	return $output;
}


/**
 *
 * アジア各国 国別情報 ツアー予約サイト
 *
 */
function get_the_content_country_tour($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  t.site_id
			, t.tour_url
			, t.tour_val
 		 FROM $wpdb->m_tour t
		WHERE t.country_id = %s
        ORDER BY t.site_id, t.site_sub_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$tours = bzb_object2array($results);
	if (count($tours)== 0) return "";

	$sql2 = sprintf("
		SELECT 
			  m.common_name 
			, t.site_id
			, count(t.site_id) AS count
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_tour t
		   ON country_id = %s
		  AND m.common_subid = t.site_id
		WHERE m.common_id = 13
        GROUP BY m.common_name, t.site_id 
		ORDER BY t.site_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);

	$output = sprintf('
	<h4 class="tbhd4">ツアーを探す</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$url = localval($tours[$r]["tour_url"], $tours[$r]["tour_val"]);	
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth afimg" style="">%s</th>  <td>%s</td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$url
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$url = localval($tours[$r]["tour_url"], $tours[$r]["tour_val"]);	
			$output .= sprintf('
		<tr><td>%s</td></tr>'
			,$url
			);
			$r++;		
		}
	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 国別情報 現地オプショナルツアー予約サイト
 *
 */
function get_the_content_country_option($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  o.site_id
			, o.option_url
			, o.option_val
 		 FROM $wpdb->m_option o
		WHERE o.country_id = %s
        ORDER BY o.site_id, o.site_sub_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$options = bzb_object2array($results);
	if (count($options)== 0) return "";

	$sql2 = sprintf("
		SELECT 
			  m.common_name 
			, o.site_id
			, count(o.site_id) AS count
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_option o
		   ON o.country_id = %s
		  AND m.common_subid = o.site_id
		WHERE m.common_id = 14
        GROUP BY m.common_name, o.site_id 
		ORDER BY o.site_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);

	$output = sprintf('
	<h4 class="tbhd4">現地オプショナルツアーを探す</h4>  
	<table cellpadding="3" class="tablecss01 ori">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$url = localval($options[$r]["option_url"], $options[$r]["option_val"]);	
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth afimg" style="">%s</th>  <td>%s</td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$url
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$url = localval($options[$r]["option_url"], $options[$r]["option_val"]);	
			$output .= sprintf('<tr><td>%s</td></tr>' ,$url);
			$r++;		
		}

	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 国別情報 天気予報
 *
 */
function get_the_content_country_weather($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  w.country_id
			, w.city_id
			, c.city_name
			, w.weather_url
 		 FROM $wpdb->m_weather w
		INNER JOIN $wpdb->m_city c
		   ON c.country_id = w.country_id
		  AND c.city_id = w.city_id
		WHERE w.country_id = %s
		  AND w.disp_flg = 1
        ORDER BY w.city_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$weathers = bzb_object2array($results);

	$output = sprintf('
	<h4 class="tbhd4">天気予報</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	foreach ($weathers as $weather) {
		$output .= sprintf('
		<tr>  <th class="tab_no tbth">%s</th>  <td>%s</td></tr>'
		,$weather["city_name"]
		,$weather["weather_url"]
		);
	}

	$output .= '
	</table>';

	return $output;
}
