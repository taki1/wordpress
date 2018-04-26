<?php
/**
 *
 * アジア各国 国旗・首都・主な都市一覧
 *
 */
function get_the_content_city() {
	global $wpdb;

	$area_no = 1;
	$areas = get_area_cnt($area_no);
	
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
		, REPLACE(mu.common_val, '%s', c.flag) AS flag_url
		 FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
		 LEFT JOIN $wpdb->m_common mu
		   ON mu.common_id = 101
		  AND mu.common_subid = 1
		WHERE a.area_no = ".$area_no;
	$results2 = $wpdb->get_results($sql2);
	$countrys = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国の首都・主な都市の一覧をまとめてみました。</h4><!--more-->
		<table border="1" cellpadding="3" width="100%">
			<tr>
				<th>No</th>
				<th width="10px" class="tab_no">地域</th>
				<th width="70px">国旗</th>
				<th class="nowrap">国名<br/>英語国名</th>
				<th>首都</th>
				<th width="160px">主な都市</th>
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
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%1s">%2s</td>'
			,$areas[$row]['cnt']
			,$country['area_name']);

			$areaid = $country['area_id'];
			$row++;
		}

		$img = sprintf('<img class="flag" alt="%s" src="%s">'
		,$country['flag']
		,$country['flag_url']					
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
	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 時差一覧
 *
 */
function get_the_content_time_difference() {
	global $wpdb;

	$area_no = 1;
	$sql = sprintf("
		SELECT 
		  c.area_id
		, COUNT(c.area_id) AS cnt
		 FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
	    INNER JOIN $wpdb->m_time t
		   ON c.country_id = t.country_id
		WHERE a.area_no = %s
		   GROUP BY c.area_id
	", $area_no);
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

	$sql2 = sprintf("
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
		 LEFT JOIN $wpdb->m_common m
		   ON CONCAT(IF(t.minus_flg = '1', '-',''),t.time_difference) = m.common_name
		WHERE a.area_no = %s
        ORDER BY c.country_id
	", $area_no);
	$results2 = $wpdb->get_results($sql2);
	$times = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国の時差をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th width="10px" class="tab_no" style="text-align:center">地域</th>
				<th>国名</th>
				<th>日本との時差</th>
				<th>協定世界時<br/>(UTC)</th>
				<th>国内時差地図へ</th>
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
				<td class="tab_no" style="text-align:center" rowspan="%1s">%2s</td>'
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
				<th>色</th>
				<th>主な都市</th>
				<th>日本との<br/>時差</th>
				<th>協定世界時<br/>(UTC)</th>
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
				<td class="ptimg" width="260px" style="text-align:center;" rowspan="%1s">
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
		<table border="1" cellpadding="3" class="font14">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>ビザ</th>
				<th>現地<br/>取得</th>
				<th>ネット<br/>取得</th>
				<th>ビザ不要<br/>滞在日数</th>
				<th>観光ビザ<br/>滞在日数</th>
				<th>観光ビザ価格</th>
			</tr>';
	
	$row = 0;
	$noteCnt = 1;
	$areaid = "";
	foreach ($visas as $visa) {
		$output .= '
			<tr>';

		if($areaid != $visa['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

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
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>通貨名</th>
				<th>通貨<br/>コード</th>
				<th>1通貨=XX円</th>
				<th>1ドル=XX通貨</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($rates as $rate) {
		$reg = '/<span class=bld>(.*?) JPY<\/span>/';
		$yhtml = sprintf(
			'https://finance.google.com/finance/converter?a=1&from=%s&to=JPY'
			,$rate['english_rate']);
		$get_yhtml = file_get_contents($yhtml);

		$yen="";
		if($get_yhtml === FALSE){
		} else {
			if(preg_match($reg, $get_yhtml, $ymatch)){
				$yen = sprintf('1%s <br/>=%s円',$rate['english_rate'] ,round($ymatch[1],3));
			}
		}

		$dhtml = sprintf(
			'https://finance.google.com/finance/converter?a=1&from=USD&to=%s'
			,$rate['english_rate']);
		$get_dhtml = file_get_contents($dhtml);

		$doll="";
		if($get_dhtml === FALSE){
		} else {
			$reg = sprintf('/<span class=bld>(.*?) %s<\/span>/',$rate['english_rate']);
			if(preg_match($reg, $get_dhtml, $match)){
				$doll = sprintf('1ドル <br/>=%s%s',round($match[1],3),$rate['english_rate']);
			}
		}

		$output .= '
			<tr>';

		if($areaid != $rate['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

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
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>公用語</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($languages); $i++) {
		$output .= '
			<tr>';

		if($areaid != $languages[$i]['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

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

	$sql2 = sprintf("
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
		  AND a.area_no = %s
         LEFT JOIN $wpdb->m_common m
           ON m.common_id = 3
          AND m.common_subid = r.religion_common_subid
		ORDER BY c.country_id, r.religion_id
	", $area_no);
	$results2 = $wpdb->get_results($sql2);
	$religions = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の主な宗教の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>主な宗教</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($religions); $i++) {
		$output .= '
			<tr>';

		if($areaid != $religions[$i]['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

	$sql2 = sprintf("
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
		  AND a.area_no = %s
		GROUP BY c.country_id, c.country_name
		ORDER BY c.country_id
	", $area_no);
	$results2 = $wpdb->get_results($sql2);
	$heritages = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の世界遺産数の一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th width="130px">国名</th>
				<th width="120px">世界遺産数合計</th>
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
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

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

	$sql2 = sprintf("
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
		  AND a.area_no = %s
         LEFT JOIN $wpdb->m_common m
           ON m.common_id = 5
          AND m.common_subid = p.plug_subid
		ORDER BY c.country_id, p.plug_id
	", $area_no);
	$results2 = $wpdb->get_results($sql2);
	$plugs = bzb_object2array($results2);

	$output = '
		<h4>今回は、アジア各国の主な電圧・プラグの一覧をまとめてみました。</h4>
		<table border="1" cellpadding="3">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>主な電圧</th>
				<th>主なプラグ</th>
			</tr>';
	
	$row = 0;
	$row_c = 0;
	$areaid = "";
	for ($i=0; $i < count($plugs); $i++) {
		$output .= '
			<tr>';

		if($areaid != $plugs[$i]['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

	$sql = sprintf("
	SELECT 
	c.country_id
  , c.country_name
  , c.area_id
  , a.area_name
  , gpi1.year AS year1
  , gpi1.rank AS gpi1
  , m1.common_val AS color1
  , gpi2.year AS year2
  , gpi2.rank AS gpi2
  , m2.common_val AS color2
  , gpi3.year AS year3
  , gpi3.rank AS gpi3
  , m3.common_val AS color3
	FROM  $wpdb->m_country c
  INNER JOIN $wpdb->m_area a
	 ON a.area_id = c.area_id
	AND a.area_no = %s
  LEFT JOIN(
	  SELECT country_id, year, rank, MAX(common_subid) AS common_subid
	   FROM $wpdb->m_gpi_gti
	  INNER JOIN $wpdb->m_common
		 ON common_id = 6
		AND common_subid < rank
	  WHERE gpi_gti_flg = 1
		AND year = (SELECT MAX(year) FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 1)
	  GROUP BY country_id, year, rank
  ) gpi1
	ON gpi1.country_id = c.country_id
  LEFT JOIN $wpdb->m_common m1
	ON m1.common_id = 6
   AND m1.common_subid = gpi1.common_subid

  LEFT JOIN(
	  SELECT country_id, year, rank, MAX(common_subid) AS common_subid
	   FROM $wpdb->m_gpi_gti
	  INNER JOIN $wpdb->m_common
		 ON common_id = 6
		AND common_subid < rank
	  WHERE gpi_gti_flg = 1
		AND year = (SELECT MAX(year) - 1 FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 1)
	  GROUP BY country_id, year, rank
  ) gpi2
	ON gpi2.country_id = c.country_id
  LEFT JOIN $wpdb->m_common m2
	ON m2.common_id = 6
   AND m2.common_subid = gpi2.common_subid

  LEFT JOIN(
	  SELECT country_id, year, rank, MAX(common_subid) AS common_subid
	   FROM $wpdb->m_gpi_gti
	  INNER JOIN $wpdb->m_common
		 ON common_id = 6
		AND common_subid < rank
	  WHERE gpi_gti_flg = 1
		AND year = (SELECT MAX(year) - 2 FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 1)
	  GROUP BY country_id,year, rank
  ) gpi3
	ON gpi3.country_id = c.country_id
  LEFT JOIN $wpdb->m_common m3
	ON m3.common_id = 6
   AND m3.common_subid = gpi3.common_subid
  ORDER BY c.country_id
	", $area_no);
	$results = $wpdb->get_results($sql);
	$peaces = bzb_object2array($results);

	$output = sprintf('
		<h4>今回は、アジア各国の世界平和度指数の一覧をまとめてみました。</h4>
		<p style="font-weight:bold;">
			世界平和度指数(Global Peace Index)は、163カ国の各国がどれくらい平和であるかを表す指標とされています。<br/>
			平和度の評価は、国内及び国際紛争、社会の治安や安全、軍事力などの23項目の指標から決められています。<br/>
			<a href="http://static.visionofhumanity.org/#/page/indexes/global-peace-index" target="_blank">詳細はこちら</a>
		</p>
		<table border="1" cellpadding="3">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>%s年</th>
				<th>%s年</th>
				<th>%s年</th>
			</tr>'
	,$peaces[0]['year1']
	,$peaces[0]['year2']
	,$peaces[0]['year3']
	);
	
	$row = 0;
	$areaid = "";
	foreach ($peaces as $peace) {
		$output .= '
			<tr>';

		if($areaid != $peace['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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
		,$peace['color1'] 
		,(strlen($peace['gpi1']) > 0) ? $peace['gpi1']."位" : 'ー'
		,$peace['color2'] 
		,(strlen($peace['gpi2']) > 0) ? $peace['gpi2']."位" : 'ー'
		,$peace['color3'] 
		,(strlen($peace['gpi3']) > 0) ? $peace['gpi3']."位" : 'ー');
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

	$area_no = 1;
	$areas = get_area_cnt($area_no);

	$sql = sprintf("
		SELECT 
		  c.country_id
		, c.country_name
		, c.area_id
		, a.area_name
		, gti1.year AS year1
		, gti1.rank AS gti1
		, m1.common_val AS color1
		, gti2.year AS year2
		, gti2.rank AS gti2
		, m2.common_val AS color2
		, gti3.year AS year3
		, gti3.rank AS gti3
		, m3.common_val AS color3
		FROM  $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
		  AND a.area_no = %s

        LEFT JOIN(
			SELECT country_id, year, rank, MAX(common_subid) AS common_subid
			FROM $wpdb->m_gpi_gti
		   INNER JOIN $wpdb->m_common
			  ON common_id = 7
			 AND common_subid < rank
		   WHERE gpi_gti_flg = 2
			 AND year = (SELECT MAX(year) FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 2)
		   GROUP BY country_id, year, rank	 
        ) gti1
          ON gti1.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m1
          ON m1.common_id = 7
         AND m1.common_subid = gti1.common_subid

        LEFT JOIN(
			SELECT country_id, year, rank, MAX(common_subid) AS common_subid
			FROM $wpdb->m_gpi_gti
		   INNER JOIN $wpdb->m_common
			  ON common_id = 7
			 AND common_subid < rank
		   WHERE gpi_gti_flg = 2
			 AND year = (SELECT MAX(year) - 1 FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 2)
		   GROUP BY country_id, year, rank	 
        ) gti2
          ON gti2.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m2
          ON m2.common_id = 7
         AND m2.common_subid = gti2.common_subid

        LEFT JOIN(
			SELECT country_id, year, rank, MAX(common_subid) AS common_subid
			FROM $wpdb->m_gpi_gti
		   INNER JOIN $wpdb->m_common
			  ON common_id = 7
			 AND common_subid < rank
		   WHERE gpi_gti_flg = 2
			 AND year = (SELECT MAX(year) - 2 FROM $wpdb->m_gpi_gti WHERE gpi_gti_flg = 2)
		   GROUP BY country_id, year, rank	 
        ) gti3
          ON gti3.country_id = c.country_id
        LEFT JOIN $wpdb->m_common m3
          ON m3.common_id = 7
         AND m3.common_subid = gti3.common_subid

		ORDER BY c.country_id
	", $area_no);
	$results = $wpdb->get_results($sql);
	$peaces = bzb_object2array($results);

	$output = sprintf('
		<h4>今回は、アジア各国の世界テロ指数の一覧をまとめてみました。</h4>
		<p style="font-weight:bold;">
			世界テロ指数(Global Terrorism Index)は、163カ国の各国がどれくらいテロの危険度があるかを表す指標とされています。<br/>
			<!--2015年は、130位が同率最下位、2014年・2013年は、124位が同率最下位<br/>-->
			テロ指数は、テロ事件の発生回数、死者数などの指標から決められています。<br/>
			<a href="http://static.visionofhumanity.org/#/page/indexes/terrorism-index" target="_blank">詳細はこちら</a>
		</p>
		<table border="1" cellpadding="3">
			<tr>
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>%s年</th>
				<th>%s年</th>
				<th>%s年</th>
			</tr>'
	,$peaces[0]['year1']
	,$peaces[0]['year2']
	,$peaces[0]['year3']
	);
			
	$row = 0;
	$areaid = "";
	foreach ($peaces as $peace) {
		$output .= '
			<tr>';

		if($areaid != $peace['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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
		,$peace['color1'] 
		,(strlen($peace['gti1']) > 0) ? $peace['gti1']."位" : 'ー'
		,$peace['color2'] 
		,(strlen($peace['gti2']) > 0) ? $peace['gti2']."位" : 'ー'
		,$peace['color3'] 
		,(strlen($peace['gti3']) > 0) ? $peace['gti3']."位" : 'ー'
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

	$area_no = 1;
	$areas = get_area_cnt($area_no, true);

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
				<th class="tab_no" width="10px" style="text-align:center">地域</th>
				<th>国名</th>
				<th>国内最高危険レベル</th>
				<th>首都の危険レベル</th>
				<th>外務省ページ</th>
			</tr>';
	
	$row = 0;
	$areaid = "";
	foreach ($safetys as $safety) {
		$output .= '
			<tr>';

		if($areaid != $safety['area_id']){
			$output .= sprintf('
				<td class="tab_no" style="text-align:center" rowspan="%s">%s</td>'
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
function get_area_cnt($area_no, $flg = false) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
		  c.area_id
		, COUNT(c.area_id) AS cnt
		 FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		   ON a.area_id = c.area_id
		WHERE a.area_no = %s
		%s
		GROUP BY c.area_id"
	,$area_no
	,$flg ? "  AND country_id <> 1" : "");
	$results = $wpdb->get_results($sql);
	return bzb_object2array($results);
}
