<?php
/**
 *
 * アジア各国 国旗・首都・主な都市一覧
 *
 */
function get_the_content_city() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.area_id
		, a.area_name
		, c.flag
		, c.capital
		, c.city
		, c.post_name
		FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
	";
	$results2 = $wpdb->get_results($sql2);
	$countrys = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国の首都・主な都市の一覧をまとめてみました。</h4><!--more-->
		<table border="1" cellpadding="3" width="100%">
			<tr>
				<th width="40px">No</th>
				<th width="10px">地域</th>
				<th width="70px">国旗</th>
				<th width="140px">国名<br/>英語国名</th>
				<th width="220px">首都</th>
				<th width="130px">主な都市</th>
			</tr>
	';
	$row = 0;
	$areaid = "";
	foreach ($countrys as $country) {
		$output .= sprintf('
			<tr>
		    	<td style="text-align:right">%1s</td>'
		,$country['country_id']);

		if($areaid != $country['area_id']){
			// $output .= '    <td style="text-align:center" rowspan="';
			// $output .= $areas[$row]['cnt'] .'">' .$country['area_name'] .'</td>';
			$output .= sprintf('
				<td style="text-align:center" rowspan="%1s">%2s</td>'
			,$areas[$row]['cnt']
			,$country['area_name']);

			$areaid = $country['area_id'];
			$row++;
		}

		$img = sprintf('<img class="flag" alt="%s" src="http://travel-a.up.seesaa.net/image/%s ">'
		,$country['flag']
		,$country['flag']					
		);
		$flag = $img;	
		$name = sprintf('%s<br/>%s'
		,$country['country_name'] 
		,$country['english_name'] 
		);
		if (strlen($country['post_name']) > 0) {
			$flag = sprintf('<a href="%s" target="_blank">%s</a>'
			,"/".$country['post_name']
			,$img					
			);

			$name = sprintf('<a href="%s" target="_blank">%s<br/>%s</a>'
			,"/".$country['post_name']
			,$country['country_name'] 
			,$country['english_name'] 
			);
		}

		$output .= sprintf('  
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
		</tr>'
		,$flag
		,$name
		,$country['capital'] 
		,str_replace(",", "<br/>", $country['city'])
		);
	}
	$output .= '</table>';

	return $output;
}

/**
 *
 * アジア各国 時差一覧
 *
 */
function get_the_content_time_difference() {
	global $wpdb;

	$sql = "
		SELECT 
		  area_id
		, COUNT(area_id) AS cnt
		FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_time t
		ON c.country_id = t.country_id
		GROUP BY area_id
	";
	$results = $wpdb->get_results($sql);
 	$areas = bzb_object2array($results);

	$sql1 = "
		SELECT 
		  country_id
		, COUNT(country_id) AS cnt
		FROM $wpdb->m_time t
		GROUP BY country_id
	";
	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.area_id
		, a.area_name
		, t.area
		, t.time_difference
		, t.minus_flg
		, m.common_val
		FROM $wpdb->m_time t
		INNER JOIN $wpdb->m_country c
		ON c.country_id = t.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
		LEFT  JOIN $wpdb->m_common m
        ON CONCAT(IF(t.minus_flg = '1', '-',''),t.time_difference) = m.common_name
        ORDER BY c.country_id
	";
	$results2 = $wpdb->get_results($sql2);
	$times = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国の時差をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地<br/>域</th>
				<th width="130px">国名</th>
				<th width="110px">日本との時差</th>
				<th width="130px">協定世界時<br/>(UTC)</th>
				<th width="150px">国内時差地図へ</th>
			</tr>';

	$row = 0;
	$row_c = 0;
	$areaid = "";
	$contryid = "";
	foreach ($times as $time) {
		$output .= sprintf('
			<tr>');
		if($areaid != $time['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%1s">%2s</td>'
				,$areas[$row]['cnt']
				,$time['area_name']);
			$areaid = $time['area_id'];
			$row++;
		}

		if($contryid != $time['country_id']) {
			$output .=  sprintf('
			    <td rowspan="%1s">%2s</td>'
				,$countrys[$row_c]["cnt"]
				,$time['country_name']);
		}

		$start = explode(":", $time['time_difference']);
		$time_difference = '';
		if($time['minus_flg']=='1') {
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

		$output .=  sprintf('
			    <td style="%s">%s</td>
			    <td style="%s">%s</td>'
			,$time['common_val']
			,$time_difference
			,$time['common_val']
			,$utc);

		if($contryid != $time['country_id']) {
			$city = '';
			if($countrys[$row_c]["cnt"] > 1){
				$city = sprintf('<a href="%1s">%2s</a>'
				,"#" .$time['english_name']
				,$time['country_name'] ."時間へ");
			}
			$output .=  sprintf('
			    <td rowspan="%1s">%2s</td>'
				,$countrys[$row_c]["cnt"]
				,$city);
			$contryid = $time['country_id'];
			$row_c++;
		}
		$output .= '
			</tr>';

	}
	$output .= '
		</table>';

	foreach ($countrys as $country) {
		if($country["cnt"] > 1) {
			$output .= get_the_content_time_difference_city($country["country_id"]);
		}
	}
	return $output;
}

/**
 *
 * アジア各国 時差一覧 国内時差表
 * @param int $country_id.
 *
 */
function get_the_content_time_difference_city($country_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
		  t.country_id
		, c.country_name
		, c.english_name
		, t.picture_file
		, t.area
		, t.color
		, t.color_val
		, t.minus_flg
		, t.time_difference
		, t.utc
		FROM  $wpdb->m_time t
		INNER JOIN $wpdb->m_country c
		ON c.country_id = t.country_id
		WHERE t.country_id = %1s
	",$country_id);
	$results = $wpdb->get_results($sql);
	$areas = bzb_object2array($results);
	if (count($areas)<= 1) return "";

	$output = sprintf('
		<h6 style="padding-top:2em;margin-top:-2em;">
			<span id="%s" style="padding:0 5px;border-left:5px solid #ff8c00;">%s時間</span>
		</h6>
		<table border="1" cellpadding="3">
			<tr>
				<th width="260px">地図</th>
				<th width="65px">色</th>
				<th width="200px">主な都市</th>
				<th width="80px">日本との<br/>時差</th>
				<th width="100px">協定世界時<br/>(UTC)</th>
			</tr>'
	,$areas[0]["english_name"]
	,$areas[0]["country_name"]
	);

	$row = 0;
	foreach ($areas as $area) {
		$output .=  '
			<tr>';
		if($row == 0) {
			$output .= sprintf('
				<td style="text-align:center;" rowspan="%1s">
					%2s
				</td>'
			,count($areas)
			,$area["picture_file"]);
		}

		$start = explode(":", $area['time_difference']);
		$time_difference = '';
		if($area['minus_flg']=='1') {
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
	
		$output .= sprintf('
				<td style="%s">%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>'
		,$area['color_val']
		,$area['color']
		,$area['area']
		,$time_difference
		,$utc
		);
		$row++;
	}
	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 ビザ一覧
 *
 */
function get_the_content_visa() {
	global $wpdb;

	$sql = "
		SELECT 
		  area_id
		, COUNT(area_id) AS cnt
		FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_visa v
		ON c.country_id = v.country_id
		GROUP BY area_id
	";
	$results = $wpdb->get_results($sql);
	$areas = bzb_object2array($results);

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.area_id
		, a.area_name
		, v.necessary
		, v.arrival
		, v.net
		, v.day
		, v.price
		, v.e_site
		, v.note
		FROM $wpdb->m_visa v
		INNER JOIN $wpdb->m_country c
		ON c.country_id = v.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
	";
	$results2 = $wpdb->get_results($sql2);
	$visas = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国のビザの一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="120px">国名</th>
				<th width="55px">ビザ</th>
				<th width="50px">現地<br/>取得</th>
				<th width="70px">ネット<br/>取得</th>
				<th width="70px">ビザ不要<br/>滞在日数</th>
				<th width="70px">観光ビザ<br/>滞在日数</th>
				<th width="110px">観光ビザ価格</th>
			</tr>';
	
	$row = 0;
	$noteCnt = 1;
	$areaid = "";
	foreach ($visas as $visa) {
		$output .= '
			<tr>';

		if($areaid != $visa['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$visa['area_name']);

			$areaid = $visa['area_id'];
			$row++;
		}
		if ($visa['necessary'] == 1) { 
			$output .= sprintf('  
				<td>%s</td>
				<td>%s</td>
				<td style="text-align:center;">%s</td>
				<td style="text-align:center;">%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
			</tr>'
			,$visa['country_name']
			,"不要"
			,$visa['arrival'] 
			,$visa['net'] 
			,$visa['day'] 
			,"-"
			,$visa['price']
			);
			}
		else { 
			$necessary = '<td style="font-weight:bold;color:red;">必要';
			if ($visa['necessary'] != 2) {
				$necessary = sprintf('<td><a href="%s">%s</a>'
							,sprintf("#kome%s",$noteCnt)
							,sprintf("※%sへ",$noteCnt));
				$noteCnt++;
			}
			$arrival = "";
			if ($visa['arrival']==1) {
				$arrival = 'font-weight:bold;color:darkgreen">◯';
			} else if ($visa['arrival']==2) {
				$arrival = 'font-weight:bold;color:red">×';
			} else {
				$arrival = sprintf('"><a href="%s">%s</a>'
				,sprintf("#kome%s",$noteCnt)
				,sprintf("※%sへ",$noteCnt));
				$noteCnt++;
			}
			
			$net = "";
			if ($visa['net']==1) {
				if (strlen($visa['e_site']) > 0) {
					$net = sprintf('"><a href="%s" target="_blank">サイトへ</a>'
							,$visa['e_site']);
				} else {
					$net = 'font-weight:bold;color:darkgreen">◯';
				}
			} else if ($visa['net']==2) {
				$net = 'font-weight:bold;color:red">×';
			} else {
				$net = sprintf('"><a href="%s">%s</a>'
				,sprintf("#kome%s",$noteCnt)
				,sprintf("※%sへ",$noteCnt));
				$noteCnt++;
			}

			$output .= sprintf('  
				<td>%s</td>
				%s</td>
				<td style="text-align:center;%s</td>
				<td style="text-align:center;%s</td>'
			,$visa['country_name']
			,$necessary
			,$arrival 
			,$net
			);
			if ($visa['price']=="*") {
				$output .= sprintf('  
				<td colspan="3">%s</td>
			</tr>'
				,sprintf('<a href="%s">%s</a>'
					,sprintf("#kome%s",$noteCnt)
					,sprintf("※%sへ",$noteCnt))
				);
				$noteCnt++;
			} else {
				$output .= sprintf('  
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
			</tr>'
				,($visa['necessary'] == 2) ? "-" : $visa['day']  
				,$visa['day'] 
				,$visa['price']
				);				
			}
		}
	}

	$output .= '
		</table>';

	$noteCnt = 1;
	foreach ($visas as $visa) {
		if(strlen($visa['note']) > 0) {
			$output .= sprintf('
			<h6 id="%s" style="padding-top:2em;margin-top:-2em;">
				<span style="padding:0 5px;border-left:5px solid #ff8c00;">%s</span>
			</h6>
			<p style="font-weight:bold;">%s</p>'
			,sprintf("kome%s",$noteCnt)
			,sprintf("※%s %sのビザについて",$noteCnt,$visa['country_name'])
			,$visa['note']);
			$noteCnt++;
		}
	}

	return $output;
}

/**
 *
 * アジア各国 通貨一覧
 *
 */
function get_the_content_rate() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.area_id
		, a.area_name
		, r.rate
		, r.english_rate
		FROM $wpdb->m_rate r
		INNER JOIN $wpdb->m_country c
		ON c.country_id = r.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
	";
	$results2 = $wpdb->get_results($sql2);
	$rates = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の通貨の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="120px">国名</th>
				<th width="130px">通貨名</th>
				<th width="55px">通貨<br/>コード</th>
				<th width="70px">1通貨=XX円</th>
				<th width="145px">1ドル=XX通貨</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($rates as $rate) {
		$reg = '/<span class=bld>(.*?) JPY<\/span>/';
		$yhtml = sprintf(
			'https://www.google.com/finance/converter?a=1&from=%s&to=JPY'
			,$rate['english_rate']);
		$get_yhtml = file_get_contents($yhtml);

		$yen="";
		if($get_yhtml === FALSE){
		} else {
			if(preg_match($reg, $get_yhtml, $ymatch)){
				$yen = sprintf('1%s=%s円',$rate['english_rate'] ,$ymatch[1]);
			}
		}

		$dhtml = sprintf(
			'https://www.google.com/finance/converter?a=1&from=USD&to=%s'
			,$rate['english_rate']);
		$get_dhtml = file_get_contents($dhtml);

		$doll="";
		if($get_dhtml === FALSE){
		} else {
			$reg = sprintf('/<span class=bld>(.*?) %s<\/span>/',$rate['english_rate']);
			if(preg_match($reg, $get_dhtml, $match)){
				$doll = sprintf('1ドル=%s%s',$match[1],$rate['english_rate']);
			}
		}

		$output .= '
			<tr>';

		if($areaid != $rate['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$rate['area_name']);

			$areaid = $rate['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>'
		,$rate['country_name']
		,$rate['rate'] 
		,$rate['english_rate'] 
		,$yen
		,$doll
		);
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 公用語一覧
 *
 */
function get_the_content_language() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql1 = "
		SELECT 
		  country_id
		, COUNT(country_id) AS cnt
		FROM $wpdb->m_language
		GROUP BY country_id
	";
	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.english_name
		, c.area_id
		, a.area_name
		, l.language_common_subid
        , IFNULL(m.common_name, l.language_name) AS language_name
        , m.common_val
		FROM $wpdb->m_language l
		INNER JOIN $wpdb->m_country c
		ON c.country_id = l.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
        LEFT  JOIN $wpdb->m_common m
        ON m.common_id = 2
        AND m.common_subid = l.language_common_subid
		ORDER BY l.language_id
	";
	$results2 = $wpdb->get_results($sql2);
	$languages = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の公用語の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="250px">公用語</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($languages); $i++) {
		$output .= '
			<tr>';

		if($areaid != $languages[$i]['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$languages[$i]['area_name']);

			$areaid = $languages[$i]['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td>'
		,$languages[$i]['country_name']);

		$output .= sprintf('<span style="%s">%s</span>'
			,$languages[$i]['common_val'] 
			,$languages[$i]['language_name'] 
		);
		for ($j=1; $j < $countrys[$row_c]["cnt"]; $j++) {
			$i +=1;
			$output .= sprintf(', <span style="%s">%s</span>'
				,$languages[$i]['common_val'] 
				,$languages[$i]['language_name'] 
			);
		}
		$row_c++;
		$output .= '</td>
				</tr>';
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 主な宗教一覧
 *
 */
function get_the_content_religion() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql1 = "
		SELECT 
		  c.country_id
		, COUNT(c.country_id) AS cnt
		FROM $wpdb->m_country c
		LEFT JOIN $wpdb->m_religion r
		ON c.country_id = r.country_id
        GROUP BY c.country_id
	";
	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
		, r.religion_common_subid
        , m.common_name AS religion_name
        , m.common_val
		FROM $wpdb->m_country c
		LEFT JOIN $wpdb->m_religion r
		ON c.country_id = r.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
        LEFT JOIN $wpdb->m_common m
        ON m.common_id = 3
        AND m.common_subid = r.religion_common_subid
		ORDER BY c.country_id, r.religion_id
	";
	$results2 = $wpdb->get_results($sql2);
	$religions = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の主な宗教の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="300px">主な宗教</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($religions); $i++) {
		$output .= '
			<tr>';

		if($areaid != $religions[$i]['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$religions[$i]['area_name']);

			$areaid = $religions[$i]['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td>'
		,$religions[$i]['country_name']);

		if (strlen($religions[$i]['religion_name'] ) > 0) {
			$output .= sprintf('<span style="%s">%s</span>'
				,$religions[$i]['common_val'] 
				,$religions[$i]['religion_name'] 
			);
		}
		for ($j=1; $j < $countrys[$row_c]["cnt"]; $j++) {
			$i +=1;
			$output .= sprintf(', <span style="%s">%s</span>'
				,$religions[$i]['common_val'] 
				,$religions[$i]['religion_name'] 
			);
		}
		$row_c++;
		$output .= '</td>
			</tr>';
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 世界遺産数一覧
 *
 */
function get_the_content_heritages_num() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
        , COUNT(h.heritage_id) AS total
		, SUM(IF(h.heritage_subid=1,1,0)) AS cultural
		, SUM(IF(h.heritage_subid=2,1,0)) AS natural_
		, SUM(IF(h.heritage_subid=3,1,0)) AS mixed
		FROM $wpdb->m_country c
		LEFT JOIN $wpdb->m_heritage h
        ON c.country_id = h.country_id
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
		GROUP BY c.country_id, c.country_name
		ORDER BY c.country_id
	";
	$results2 = $wpdb->get_results($sql2);
	$heritages = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の世界遺産数の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="110px">世界遺産数合計</th>
				<th width="80px">文化遺産</th>
				<th width="80px">自然遺産</th>
				<th width="80px">複合遺産</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($heritages as $heritage) {
		$output .= '
			<tr>';

		if($areaid != $heritage['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$heritage['area_name']);

			$areaid = $heritage['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
				<td style="text-align:right;">%s</td>
			</tr>'
		,$heritage['country_name'] 
		,$heritage['total'] 
		,$heritage['cultural'] 
		,$heritage['natural_'] 
		,$heritage['mixed']);
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 主な電圧・プラグ一覧
 *
 */
function get_the_content_plug() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql1 = "
		SELECT 
		  c.country_id
		, COUNT(c.country_id) AS cnt
		FROM $wpdb->m_country c
		 LEFT JOIN $wpdb->m_plug p
		   ON c.country_id = p.country_id
        GROUP BY c.country_id
	";
	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
		, c.volt
        , p.plug_subid
        , m.common_name AS plug_name
        , m.common_val
		FROM  $wpdb->m_country c
		 LEFT JOIN $wpdb->m_plug p
		   ON c.country_id = p.country_id
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
         LEFT JOIN $wpdb->m_common m
           ON m.common_id = 5
          AND m.common_subid = p.plug_subid
		ORDER BY c.country_id, p.plug_id
	";
	$results2 = $wpdb->get_results($sql2);
	$plugs = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の主な電圧・プラグの一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="100px">主な電圧</th>
				<th width="150px">主なプラグ</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($plugs); $i++) {
		$output .= '
			<tr>';

		if($areaid != $plugs[$i]['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$plugs[$i]['area_name']);

			$areaid = $plugs[$i]['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td>%s</td>
				<td>'
		,$plugs[$i]['country_name'] 
		,$plugs[$i]['volt']);

		if (strlen($plugs[$i]['plug_name'] ) > 0) {
			$output .= sprintf('<span style="%s">%s</span>'
				,$plugs[$i]['common_val'] 
				,$plugs[$i]['plug_name'] 
			);
		}
		for ($j=1; $j < $countrys[$row_c]["cnt"]; $j++) {
			$i +=1;
			$output .= sprintf(', <span style="%s">%s</span>'
				,$plugs[$i]['common_val'] 
				,$plugs[$i]['plug_name'] 
			);
		}
		$row_c++;
		$output .= '</td>
			</tr>';
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 世界平和度指数一覧
 *
 */
function get_the_content_gpi() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
        , g2016.gpi2016
        , m2016.common_val AS color2016
        , g2015.gpi2015
        , m2015.common_val AS color2015
        , g2014.gpi2014
        , m2014.common_val AS color2014
 		 FROM  $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id

        LEFT JOIN(
            SELECT country_id, gpi2016, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 6
              AND common_subid < gpi2016
            GROUP BY country_id, gpi2016
        ) g2016
          ON g2016.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2016
          ON m2016.common_id = 6
         AND m2016.common_subid = g2016.common_subid

        LEFT JOIN(
            SELECT country_id, gpi2015, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 6
              AND common_subid < gpi2015
            GROUP BY country_id,gpi2015
        ) g2015
          ON g2015.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2015
          ON m2015.common_id = 6
         AND m2015.common_subid = g2015.common_subid

        LEFT JOIN(
            SELECT country_id, gpi2014, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 6
              AND common_subid < gpi2014
            GROUP BY country_id,gpi2014
        ) g2014
          ON g2014.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2014
          ON m2014.common_id = 6
         AND m2014.common_subid = g2014.common_subid
		ORDER BY c.country_id
	";
	$results = $wpdb->get_results($sql);
	$peaces = bzb_object2array($results);

	$output = '
		<h4>今回は、アジア各国の世界平和度指数の一覧をまとめてみました。</h4>
		<p style="font-weight:bold;">
			世界平和度指数(Global Peace Index)は、163カ国の各国がどれくらい平和であるかを表す指標とされています。<br/>
			平和度の評価は、国内及び国際紛争、社会の治安や安全、軍事力などの23項目の指標から決められています。<br/>
			<a href="http://static.visionofhumanity.org/#/page/indexes/global-peace-index" target="_blank">詳細はこちら</a>
		</p>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="80px">2016年</th>
				<th width="80px">2015年</th>
				<th width="80px">2014年</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($peaces as $peace) {
		$output .= '
			<tr>';

		if($areaid != $peace['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$peace['area_name']);

			$areaid = $peace['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td style="text-align:right;%s">%s</td>
				<td style="text-align:right;%s">%s</td>
				<td style="text-align:right;%s">%s</td>
			</tr>'
		,$peace['country_name'] 
		,$peace['color2016'] 
		,(strlen($peace['gpi2016']) > 0) ? $peace['gpi2016']."位" : 'ー'
		,$peace['color2015'] 
		,(strlen($peace['gpi2015']) > 0) ? $peace['gpi2015']."位" : 'ー'
		,$peace['color2014'] 
		,(strlen($peace['gpi2014']) > 0) ? $peace['gpi2014']."位" : 'ー');
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 世界テロ指数一覧
 *
 */
function get_the_content_gti() {
	global $wpdb;

	$areas = get_area_cnt();

	$sql = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
        , g2015.gti2015
        , m2015.common_val AS color2015
        , g2014.gti2014
        , m2014.common_val AS color2014
        , g2013.gti2013
        , m2013.common_val AS color2013
 		 FROM  $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id

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

        LEFT JOIN(
            SELECT country_id, gti2014, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 7
              AND common_subid < gti2014
            GROUP BY country_id, gti2014
        ) g2014
          ON g2014.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2014
          ON m2014.common_id = 7
         AND m2014.common_subid = g2014.common_subid

        LEFT JOIN(
            SELECT country_id, gti2013, MAX(common_subid) AS common_subid
             FROM $wpdb->m_gpi_gti
            INNER JOIN $wpdb->m_common
               ON common_id = 7
              AND common_subid < gti2013
            GROUP BY country_id, gti2013
        ) g2013
          ON g2013.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2013
          ON m2013.common_id = 7
         AND m2013.common_subid = g2013.common_subid

		ORDER BY c.country_id
	";
	$results = $wpdb->get_results($sql);
	$peaces = bzb_object2array($results);

	$output = '
		<h4>今回は、アジア各国の世界テロ指数の一覧をまとめてみました。</h4>
		<p style="font-weight:bold;">
			世界テロ指数(Global Terrorism Index)は、163カ国の各国がどれくらいテロの危険度があるかを表す指標とされています。<br/>
			2015年は、130位が同率最下位、2014年・2013年は、124位が同率最下位<br/>
			テロ指数は、テロ事件の発生回数、死者数などの指標から決められています。<br/>
			<a href="http://static.visionofhumanity.org/#/page/indexes/terrorism-index" target="_blank">詳細はこちら</a>
		</p>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="80px">2015年</th>
				<th width="80px">2014年</th>
				<th width="80px">2013年</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($peaces as $peace) {
		$output .= '
			<tr>';

		if($areaid != $peace['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$peace['area_name']);

			$areaid = $peace['area_id'];
			$row++;
		}

		$output .= sprintf('  
				<td>%s</td>
				<td style="text-align:right;%s">%s</td>
				<td style="text-align:right;%s">%s</td>
				<td style="text-align:right;%s">%s</td>
			</tr>'
		,$peace['country_name'] 
		,$peace['color2015'] 
		,(strlen($peace['gti2015']) > 0) ? $peace['gti2015']."位" : 'ー'
		,$peace['color2014'] 
		,(strlen($peace['gti2014']) > 0) ? $peace['gti2014']."位" : 'ー'
		,$peace['color2013'] 
		,(strlen($peace['gti2013']) > 0) ? $peace['gti2013']."位" : 'ー'
		);
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * アジア各国 外務省 危険情報一覧
 *
 */
function get_the_content_safety() {
	global $wpdb;

	$areas = get_area_cnt(true);

	$sql1 = "
		SELECT 
		  m.common_id
        , m.common_name  AS safety_name
        , m.common_val   AS safety_val
        , m2.common_name AS safety_name2
        , m2.common_val  AS safety_val2
		FROM  $wpdb->m_common m
		INNER JOIN $wpdb->m_common m2
           ON m.common_id = 8
		  AND m2.common_id = 8
          AND (m.common_subid + 10) = m2.common_subid
 		ORDER BY m.common_subid";
	$results1 = $wpdb->get_results($sql1);
	$levels = bzb_object2array($results1);

	$output = '
		<h4>今回は、アジア各国の外務省発表の危険情報の一覧をまとめてみました。</h4>';

	$output .= '
		<table border="1" cellpadding="3">
			<tr>
				<th width="100px">レベル</th>
				<th width="150px">説明</th>
			</tr>';

	foreach ($levels as $safety) {
		$output .= sprintf(" 
			<tr>
				<td style='%1\$s'>%2\$s<br/>%3\$s</td>
				<td style='%1\$s'>%4\$s</td>
			</tr>"
		,$safety['safety_val'] 
		,$safety['safety_name'] 
		,$safety['safety_val2'] 
		,$safety['safety_name2'] 
		);
	}

	$output .= '
		</table>';

	$sql2 = "
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
        , s.max_level_id
        , m.common_name AS safety_name
        , m.common_val AS safety_val
		, s.capital_level_id
        , m2.common_name AS safety_name2
        , m2.common_val AS safety_val2
		, s.safety_url_id AS safety_url 
		FROM  $wpdb->m_country c
		INNER JOIN $wpdb->m_safety s
		   ON c.country_id = s.country_id
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
         LEFT JOIN $wpdb->m_common m
           ON m.common_id = 8
          AND m.common_subid = s.max_level_id
         LEFT JOIN $wpdb->m_common m2
           ON m2.common_id = 8
          AND m2.common_subid = s.capital_level_id
		ORDER BY c.country_id
	";
	$results2 = $wpdb->get_results($sql2);
	$safetys = bzb_object2array($results2);

	$sql3 = "
		SELECT 
		  m.common_val AS url_format
		FROM  $wpdb->m_common m
        WHERE m.common_id = 101
          AND m.common_subid = 2
	";
	$results3 = $wpdb->get_results($sql3);
	$url_formats = bzb_object2array($results3);

	$output .= '
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="150px">国内最高危険レベル</th>
				<th width="150px">首都の危険レベル</th>
				<th width="150px">外務省ページ</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($safetys as $safety) {
		$output .= '
			<tr>';

		if($areaid != $safety['area_id']){
			$output .= sprintf('
				<td style="text-align:center" rowspan="%s">%s</td>'
			,$areas[$row]['cnt']
			,$safety['area_name']);

			$areaid = $safety['area_id'];
			$row++;
		}

		$url = "";
		if (strlen($safety['safety_url']) > 0) {
			$url = sprintf('<a href="%s" target="_blank">危険情報</a>'
						, sprintf($url_formats[0]["url_format"],$safety['safety_url'])
			);

		}
		$output .= sprintf('  
				<td>%s</td>
				<td style="%s">%s</td>
				<td style="%s">%s</td>
				<td>%s</td>
			</tr>'
		,$safety['country_name'] 
		,$safety['safety_val'] 
		,$safety['safety_name'] 
		,$safety['safety_val2'] 
		,$safety['safety_name2'] 
		,$url 
		);
	}

	$output .= '
		</table>';

	return $output;
}

/**
 *
 * エリア別件数取得
 *
 */
function get_area_cnt($flg = false) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
		  area_id
		, COUNT(area_id) AS cnt
		FROM $wpdb->m_country c
		%s
		GROUP BY area_id"
	,$flg ? "WHERE country_id <> 1" : "");
	$results = $wpdb->get_results($sql);
	return bzb_object2array($results);
}

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
	,$country['country_name'] 
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

	// $flg = false;
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
		<tr>  <th style="white-space:nowrap">世界平和度指数(2016年)</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
		<tr>  <th style="white-space:nowrap">世界テロ指数(2015年)</th>  <td class="wd100" style="width:100%%;%s">%s</td></tr>
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
		'https://www.google.com/finance/converter?a=1&from=%s&to=JPY'
		,$english_rate);
return "";
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
		'https://www.google.com/finance/converter?a=1&from=USD&to=%s'
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
		   ON country_id = %s
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
	<table cellpadding="3" class="tablecss01">
	'
	,count($sites)
	);
	foreach ($sites as $site) {
		$output .= sprintf('
		<tr>  <th class="tbth">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$site["common_name"]
		,$site["site_url"]
		,$site["site_val"]
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
			, b.book_url
			, b.book_val
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_book b
		   ON country_id = %s
		  AND m.common_subid = b.book_id
		WHERE m.common_id = 9
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
        GROUP BY m.common_name, b.book_id 
		ORDER BY b.book_id
	"
	,$country_id
	);

	$results2 = $wpdb->get_results($sql2);
	$cnts = bzb_object2array($results2);

	$output = sprintf('
	<h4 class="tbhd4">ガイドブック</h4>  
	<table cellpadding="3" class="tablecss01">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth" style="">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$books[$r]["book_url"]
		,$books[$r]["book_val"]
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr><td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
			,$books[$r]["book_url"]
			,$books[$r]["book_val"]
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
	<table class="tablecss01" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth" style="">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$tickets[$r]["ticket_url"]
		,sprintf("%s行き航空券を探す", $tickets[$r]["city_name"])
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr><td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
			,$tickets[$r]["ticket_url"]
			,sprintf("%s行き航空券を探す", $tickets[$r]["city_name"])
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
	<table class="tablecss01" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth" style="">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$hotels[$r]["ticket_url"]
		,sprintf("%sのホテルを探す", $hotels[$r]["city_name"])
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr><td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
			,$tickets[$r]["ticket_url"]
			,sprintf("%sのホテルを探す", $hotels[$r]["city_name"])
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
	<table class="tablecss01" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth" style="">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$tours[$r]["tour_url"]
		,$tours[$r]["tour_val"]
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr><td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
			,$tours[$r]["tour_url"]
			,$tours[$r]["tour_val"]
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
	<table class="tablecss01" cellpadding="3">
	'
	);

	$r = 0;
	for ($i = 0; $i < count($cnts); $i++) {
		$output .= sprintf('
		<tr>  <th rowspan="%s" class="tbth" style="">%s</th>  <td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
		,$cnts[$i]["count"]
		,$cnts[$i]["common_name"]
		,$options[$r]["option_url"]
		,$options[$r]["option_val"]
		);
		$r++;
		for ($j = 1; $j < $cnts[$i]["count"]; $j++) {
			$output .= sprintf('
		<tr><td><a href="%s" target="_blank" rel="nofollow">%s</a></td></tr>'
			,$options[$r]["option_url"]
			,$options[$r]["option_val"]
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
        ORDER BY w.city_id
	"
	,$country_id
	);

	$results = $wpdb->get_results($sql);
	$weathers = bzb_object2array($results);

	$output = sprintf('
	<h4 class="tbhd4">天気予報</h4>  
	<table class="tablecss01" cellpadding="3">
	'
	);

	foreach ($weathers as $weather) {
		$output .= sprintf('
		<tr>  <th class="tbth">%s</th>  <td>%s</td></tr>'
		,$weather["city_name"]
		,$weather["weather_url"]
		);
	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * ページ記事内容取得
 *
 */
function get_the_content_page( $post_name ) {
	global $wpdb;

	$output = '';
	switch ($post_name) {
		case "asia":
			$output .= get_the_content_city();
			break;
		case "time_difference":
			$output .= get_the_content_time_difference();
			break;
		case "visa":
			$output .= get_the_content_visa();
			break;
		case "rate":
			$output .= get_the_content_rate();
			break;
		case "language":
			$output .= get_the_content_language();
			break;
		case "religion":
			$output .= get_the_content_religion();
			break;
		case "heritages_num":
			$output .= get_the_content_heritages_num();
			break;
		case "plug":
			$output .= get_the_content_plug();
			break;
		case "gpi":
			$output .= get_the_content_gpi();
			break;
		case "gti":
			$output .= get_the_content_gti();
			break;
		case "safety":
			$output .= get_the_content_safety();
			break;
		default:
			$sql1 = sprintf("
			SELECT c.country_id
			FROM  $wpdb->m_country c
			WHERE c.post_name = '%s'
			"
			,$post_name
			);

			$countrys = $wpdb->get_results($sql1);

			if (count($countrys) > 0){
				$country_id = $countrys[0]->country_id;
				$output .= get_the_content_country($country_id);
			}
			break;
	}
	return $output;
}

/**
 *
 * トップページ
 *
 */
function get_the_content_country_top() {
	global $wpdb;

	$sql = ("
		SELECT 
			  c.country_id
			, c.country_name
			, c.english_name
			, c.post_name
			, REPLACE(mu.common_val, '%s', flag) AS flag_url
		 FROM $wpdb->posts p
		INNER JOIN $wpdb->m_country c
		   ON p.post_name = c.post_name
		 LEFT JOIN $wpdb->m_common mu
		   ON mu.common_id = 101
		  AND mu.common_subid = 1
		WHERE p.post_status = 'publish'
		ORDER BY c.country_id
	"
	);

	$results = $wpdb->get_results($sql);
	$output = "";
	foreach ($results as $result) {
		$output .= sprintf('
  <article id="post-top-%s" class="popular_post_box post-top-%s post status-publish format-standard hentry category-country tag-8 tag-7 tag-4 tag-5 tag-6">
    <a href="%s" class="wrap-a">
		<p class="p_date" style="background:none;"><img src="%s" alt="%s" style="border:solid 1px #000000;" width="800" height="533"/></p>
		<h3 style="margin-left:60px">%s</h3>
    </a>
  </article>
  		'
		,$result->country_id
		,$result->country_id
		,$result->post_name
		,$result->flag_url
		,$result->english_name
		,$result->country_name
		);
	}
        // <img src="%s" alt="%s" width="800" height="533"/>
//  width="800" height="533"
	return $output;
}
