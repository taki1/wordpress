<?php

/**
 *
 * アジア各国 世界遺産
 *
 */
 function get_the_content_heritage($heritage_id) {
	global $wpdb;

	$sql = sprintf("
	SELECT 
		  h.heritage_subid
		, h.country_id
		, h.city_id
		, c.city_name
		, m.common_name AS common_name
		, h.heritage_name 
		, h.registration_year
		, h.text
		, h.ticket_city_id
	 FROM $wpdb->m_heritage h
	INNER JOIN $wpdb->m_common m
	   ON m.common_id = 4
	  AND m.common_subid = h.heritage_subid
	 LEFT JOIN $wpdb->m_city c
	   ON c.country_id = h.country_id
	  AND c.city_id = h.city_id
	WHERE h.heritage_id=%s
	"
	,$heritage_id
	);

	$results = $wpdb->get_results($sql);
	$heritage = $results[0];
	
	$output .= sprintf('
	<h4 class="tbhd4">基本情報</h4>
	<table cellpadding="3" class="tablecss01" style"display:block;">
		<tr>  <th>世界遺産名</th>  <td>%s</td></tr>
		<tr>  <th>都市</th>  <td>%s</td></tr>
		<tr>  <th>種類</th>  <td>%s</td></tr>
		<tr>  <th>登録年</th>  <td>%s年</td></tr>
		<tr>  <th>構成遺産</th>  <td>%s</td></tr>
	</table>	
	'
	,$heritage->heritage_name
	,$heritage->city_name
	,$heritage->common_name
	,$heritage->registration_year
	,""
	); 	

	$output .= get_the_content_heritage_spot($heritage_id);
	$output .= get_the_content_heritage_safety($heritage->country_id, $heritage->city_id);
	$output .= get_the_content_adsbygoogle();	
	$output .= get_the_content_heritage_ticket($heritage->country_id, $heritage->ticket_city_id);
	$output .= get_the_content_heritage_hotel($heritage->country_id, $heritage->city_id);
	$output .= get_the_content_heritage_option($heritage_id);
	$output .= get_the_content_heritage_introduction($heritage->country_id, $heritage->city_id);
	$output .= get_the_content_heritage_weather($heritage->country_id, $heritage->city_id);
	$output .= get_the_content_heritage_map($heritage->country_id, $heritage->city_id);
	
	return $output;	
}

/**
 *
 * アジア各国 世界遺産 見どころ
 *
 */
 function get_the_content_heritage_spot($heritage_id) {
	global $wpdb;

	$sql = sprintf("
	SELECT 
		  s.heritage_id
		, s.spot_id
		, s.spot_name 
		, s.spot_photo
		, s.spot_description
	 FROM $wpdb->m_spot s
	WHERE s.heritage_id=%s
	ORDER BY s.heritage_id, s.spot_id
	"
	,$heritage_id
	);

	$spots = $wpdb->get_results($sql);
	if (count($spots) == 0) return "";

	if ($spots[0]->spot_id == 0) {
		$output .= sprintf('
	<h4 class="tbhd4">%sとは</h4>
	<table cellpadding="3" class="tablecss01 ori" style"display:block;">
		<tr>	<td style="text-align:center">%s</td>	</tr>
		<tr>	<td>%s</td>	</tr>
	</table>
		'
		,$spots[0]->spot_name
		,$spots[0]->spot_photo
		,$spots[0]->spot_description
		); 			
	}	

	if (count($spots) > 1){
		$output .= '
	<h4 class="tbhd4">主な見どころ</h4>
	<table cellpadding="3" class="tablecss01 ori" style"display:block;">
	';

		foreach ($spots as $spot) {
			if ($spot->spot_id != 0) {
				$output .= sprintf('
		<tr>  
			<th>%s</th>
		</tr>
		<tr>  
			<td style="text-align:center">%s</td>
		</tr>
		<tr>  
			<td>%s</td>
		</tr>'
				,$spot->spot_name
				,$spot->spot_photo
				,$spot->spot_description
				); 				
			}
		}
		$output .= '
	</table>';
	}

	return $output;
}

/**
 *
 * アジア各国 世界遺産 危険情報
 *
 */
function get_the_content_heritage_safety($country_id, $city_id) {
	global $wpdb;

	$sql = sprintf("
	SELECT 
	  c.country_id
	, c.safety_level_id
	, m.common_name AS safety_name
	, m.common_val AS safety_color
	, m2.common_val AS url_format
	, s.safety_url_id AS safety_url 
	 FROM $wpdb->m_city c
	INNER JOIN $wpdb->m_safety s
	   ON c.country_id = s.country_id
	 LEFT JOIN $wpdb->m_common m
	   ON m.common_id = 8
	  AND m.common_subid = c.safety_level_id
	 LEFT JOIN $wpdb->m_common m2
	   ON m2.common_id = 101
	  AND m2.common_subid = 2
	WHERE c.country_id = %s
	  AND c.city_id = %s"
	,$country_id
	,$city_id
	);

	$safetys = $wpdb->get_results($sql);
	if (count($safetys) == 0) return "";

	$safety = $safetys[0];
	$url = "";
	if (strlen($safety->safety_url) > 0) {
		$url = sprintf('<a href="%s" target="_blank">危険情報</a>'
				, sprintf($safety->url_format,$safety->safety_url)
		);
	}

	$output .= sprintf('
	<h4 class="tbhd4">危険情報</h4>
	<table cellpadding="3" class="tablecss01" style"display:block;">
		<tr>  <th style="white-space:nowrap">外務省ページ</th>  <td>%s</td></tr>
		<tr>  <th style="white-space:nowrap">危険レベル</th>  <td style="%s">%s</td></tr>
	</table>'			
	,$url
	,$safety->safety_color
	,$safety->safety_name 
	);

	return $output;
}


/**
 *
 * アジア各国 世界遺産 紹介サイト
 *
 */
function get_the_content_heritage_introduction($country_id, $city_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  m.common_name 
			, s.site_url
			, s.site_val
		 FROM $wpdb->m_common m
		INNER JOIN $wpdb->m_site s
		   ON s.country_id = %s
		  AND s.city_id = %s
		  AND m.common_subid = s.site_id
		WHERE m.common_id = 10
		ORDER BY s.site_id
	"
	,$country_id
	,$city_id
	);

	$sites = $wpdb->get_results($sql);
	if (count($sites)== 0) return "";

	$output = sprintf('
	<h4 class="tbhd4">旅行ガイド・まとめサイト %s</h4>  
	<table cellpadding="3" class="tablecss01 ori">
	'
	,count($sites)
	);
	foreach ($sites as $site) {
		$url = $site->site_url;
		if(mb_substr($site->site_url,0,1)=="<") {
			if(strpos($_SERVER["HTTP_HOST"], "localhost") !== false) {
				$url = $site->site_val;
			}
		} else {
			$url = sprintf('<a href="%s" target="_blank" rel="nofollow">%s</a>'
			,$site->site_url
			,$site->site_val
			);
		}

		$output .= sprintf('
		<tr>  <th class="tbth afimg">%s</th>  <td>%s</td></tr>'
		,$site->common_name
		,$url
		);
	}

	$output .= '
	</table>';

	return $output;
}


/**
 *
 * アジア各国 世界遺産 航空券予約サイト
 *
 */
function get_the_content_heritage_ticket($country_id, $ticket_city_id) {
	global $wpdb;

	$sql = sprintf("
	SELECT 
		  t.site_id
		, t.city_id
		, t.ticket_url
		, c.city_name
		, a.airport_code
		, m.common_name AS site_name 
	 FROM  $wpdb->m_city c
	 LEFT JOIN $wpdb->m_ticket t
	   ON c.country_id = t.country_id
	  AND c.city_id = t.city_id
	 LEFT JOIN $wpdb->m_airport a
	  ON c.country_id = a.country_id
	 AND c.city_id = a.airport_id
	 LEFT JOIN $wpdb->m_common m	 
	  ON m.common_id = 11
	 AND m.common_subid = t.site_id
	WHERE c.country_id = %s
	  AND c.city_id = %s"
	 ,$country_id
	 ,$ticket_city_id
	);

	$tickets = $wpdb->get_results($sql);
	if (count($tickets) > 0) {

	$output .= sprintf('
	<h4 class="tbhd4">航空券を探す</h4>  
	<table class="tablecss01 ori" cellpadding="3">
		<tr>
			<th class="tbth">最寄り空港</th>
			<td class="nowrap" style="">%s(%s)</td>
		</tr>
	'
	, $tickets[0]->city_name
	, $tickets[0]->airport_code
	);

	foreach ($tickets as $ticket) {
		$url = "";
		if(strpos($ticket->ticket_url, 'http') === 0)
		{
			$url = sprintf('<a href="%s" target="_blank" rel="nofollow">%s行き航空券を探す</a>'
			, $ticket->ticket_url
			, $ticket->city_name);
		} else {
			$url = localval($ticket->ticket_url, $ticket->city_name."行き航空券を探す");	
		}

		$output .= sprintf('
		<tr>
		  <th class="tbth afimg" style="">%s</th>
		  <td>%s</td>
		</tr>'
		,$ticket->site_name
		,$url);
		}
		$output .= '
	</table>';
	}

	return $output;
}


/**
 *
 * アジア各国 世界遺産 ホテル予約サイト
 *
 */
function get_the_content_heritage_hotel($country_id, $city_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  h.site_id
			, h.city_id
			, h.hotel_url
			, c.city_name
			, m.common_name AS site_name 
		 FROM  $wpdb->m_city c
		 LEFT JOIN $wpdb->m_hotel h
		   ON c.country_id = h.country_id
		  AND c.city_id = h.city_id
		 LEFT JOIN $wpdb->m_common m	 
		   ON m.common_id = 12
		  AND m.common_subid = h.site_id
		WHERE c.country_id = %s
		  AND c.city_id = %s
		ORDER BY h.site_id, h.city_id
	"
	,$country_id
	,$city_id
	);

	$hotels = $wpdb->get_results($sql);
	if (count($hotels)== 0) return "";
	
	$output = sprintf('
	<h4 class="tbhd4">ホテルを探す</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	foreach ($hotels as $hotel) {
		$url = localval($hotel->hotel_url, $hotel->city_name."のホテルを探す");	
		$output .= sprintf('
		<tr>  <th class="tbth afimg" style="">%s</th>  <td>%s</td></tr>'
		,$hotel->site_name
		,$url
		);
	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 世界遺産 現地オプショナルツアー予約サイト
 *
 */
function get_the_content_heritage_option($heritage_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  o.site_id
			, o.option_url
			, o.option_val
			, m.common_name AS site_name
 		 FROM $wpdb->m_option o
		INNER JOIN $wpdb->m_common m
		  ON m.common_id = 14
		 AND m.common_subid = o.site_id
		WHERE o.country_id = %s
        ORDER BY o.site_id, o.site_sub_id
	"
	,$heritage_id
	);

	$options = $wpdb->get_results($sql);
	if (count($options)== 0) return "";

	$output = sprintf('
	<h4 class="tbhd4">現地オプショナルツアーを探す</h4>  
	<table cellpadding="3" class="tablecss01 ori">
	'
	);

	foreach ($options as $option) {		
		$url = localval($option->option_url, $option->option_val);	
		$output .= sprintf('
		<tr>  <th class="tbth afimg" style="">%s</th>  <td>%s</td></tr>'
		,$option->site_name
		,$url
		);
	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 世界遺産 天気予報
 *
 */
function get_the_content_heritage_weather($country_id, $city_id) {
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
		  AND w.city_id = %s
	"
	,$country_id
	,$city_id
	);
	$weathers = $wpdb->get_results($sql);
	if (count($weathers)== 0) return "";
	
	$output = sprintf('
	<h4 class="tbhd4">天気予報</h4>  
	<table class="tablecss01 ori" cellpadding="3">
	'
	);

	foreach ($weathers as $weather) {
		$output .= sprintf('
		<tr>  <th class="tab_no tbth">%s</th>  <td>%s</td></tr>'
		,$weather->city_name
		,$weather->weather_url
		);
	}

	$output .= '
	</table>';

	return $output;
}

/**
 *
 * アジア各国 世界遺産 MAP
 *
 */
 function get_the_content_heritage_map($country_id, $city_id) {
	global $wpdb;

	$sql = sprintf("
		SELECT 
			  m.country_id
			, m.city_id
			, m.map_url
 		 FROM $wpdb->m_map m
		WHERE m.country_id = %s
		  AND m.city_id = %s
	"
	,$country_id
	,$city_id
	);

	$maps = $wpdb->get_results($sql);
	if (count($maps)== 0) return "";
	
	$output .= sprintf('
	<h4 class="tbhd4">MAP</span></h4>  
	<table class="tablecss01 ori" cellpadding="3"> 
		<tr>
			<td>
	  			<div class="map_wrapper">
	  				<div class="googlemap">
	  					%s
	  				</div>
	  			</div>
			</td>
		</tr>
	</table>
  ',$maps[0]->map_url); 	
	  
	$output .= '
	</table>';

	return $output;
}