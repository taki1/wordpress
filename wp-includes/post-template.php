<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 */
function the_ID() {
	echo get_the_ID();
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 *
 * @return int|false The ID of the current item in the WordPress Loop. False if $post is not set.
 */
function get_the_ID() {
	$post = get_post();
	return ! empty( $post ) ? $post->ID : false;
}

/**
 * Display or retrieve the current post title with optional markup.
 *
 * @since 0.71
 *
 * @param string $before Optional. Markup to prepend to the title. Default empty.
 * @param string $after  Optional. Markup to append to the title. Default empty.
 * @param bool   $echo   Optional. Whether to echo or return the title. Default true for echo.
 * @return string|void Current post title if $echo is false.
 */
function the_title( $before = '', $after = '', $echo = true ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like the_title(), except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and esc_attr()
 * before it is passed to the user or displayed. The default as with the_title(),
 * is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args {
 *     Title attribute arguments. Optional.
 *
 *     @type string  $before Markup to prepend to the title. Default empty.
 *     @type string  $after  Markup to append to the title. Default empty.
 *     @type bool    $echo   Whether to echo or return the title. Default true for echo.
 *     @type WP_Post $post   Current post object to retrieve the title for.
 * }
 * @return string|void String when echo is false.
 */
function the_title_attribute( $args = '' ) {
	$defaults = array( 'before' => '', 'after' =>  '', 'echo' => true, 'post' => get_post() );
	$r = wp_parse_args( $args, $defaults );

	$title = get_the_title( $r['post'] );

	if ( strlen( $title ) == 0 ) {
		return;
	}

	$title = $r['before'] . $title . $r['after'];
	$title = esc_attr( strip_tags( $title ) );

	if ( $r['echo'] ) {
		echo $title;
	} else {
		return $title;
	}
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string
 */
function get_the_title( $post = 0 ) {
	$post = get_post( $post );

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {

			/**
			 * Filters the text prepended to the post title for protected posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Protected: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s' ), $post );
			$title = sprintf( $protected_title_format, $title );
		} elseif ( isset( $post->post_status ) && 'private' == $post->post_status ) {

			/**
			 * Filters the text prepended to the post title of private posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Private: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$private_title_format = apply_filters( 'private_title_format', __( 'Private: %s' ), $post );
			$title = sprintf( $private_title_format, $title );
		}
	}

	/**
	 * Filters the post title.
	 *
	 * @since 0.71
	 *
	 * @param string $title The post title.
	 * @param int    $id    The post ID.
	 */
	return apply_filters( 'the_title', $title, $id );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as a link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * URL is escaped to make it XML-safe.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Default is global $post.
 */
function the_guid( $post = 0 ) {
	$post = get_post( $post );

	$guid = isset( $post->guid ) ? get_the_guid( $post ) : '';
	$id   = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the escaped Global Unique Identifier (guid) of the post.
	 *
	 * @since 4.2.0
	 *
	 * @see get_the_guid()
	 *
	 * @param string $guid Escaped Global Unique Identifier (guid) of the post.
	 * @param int    $id   The post ID.
	 */
	echo apply_filters( 'the_guid', $guid, $id );
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Default is global $post.
 * @return string
 */
function get_the_guid( $post = 0 ) {
	$post = get_post( $post );

	$guid = isset( $post->guid ) ? $post->guid : '';
	$id   = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the Global Unique Identifier (guid) of the post.
	 *
	 * @since 1.5.0
	 *
	 * @param string $guid Global Unique Identifier (guid) of the post.
	 * @param int    $id   The post ID.
	 */
	return apply_filters( 'get_the_guid', $guid, $id );
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool   $strip_teaser   Optional. Strip teaser content before the more text. Default is false.
 */
function the_content( $more_link_text = null, $strip_teaser = false) {
	$content = get_the_content( $more_link_text, $strip_teaser );

	// switch (str_replace(home_url(), "", get_permalink())) {
	// 	case "/city/":
	// 		$content = get_the_content_city( $more_link_text, $strip_teaser );
	// 		break;
	// 	case "/time_difference/":
	// 		$content = get_the_content_time_difference( $more_link_text, $strip_teaser );
	// 		break;
	// 	default:
	// 		$content = get_the_content( $more_link_text, $strip_teaser );
	// 		break;
	// }

	/**
	 * Filters the post content.
	 *
	 * @since 0.71
	 *
	 * @param string $content Content of the current post.
	 */
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	echo $content;	
}


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
		FROM $wpdb->m_country c
		INNER JOIN $wpdb->m_area a
		ON a.area_id = c.area_id
	";
	$results2 = $wpdb->get_results($sql2);
	$countrys = bzb_object2array($results2);

	$output = 
		'<h4>今回は、アジア各国の首都・主な都市の一覧をまとめてみました。</h4>
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

		$output .= sprintf('  
			<td>
				<img class="flag" alt="%1s" src="http://travel-a.up.seesaa.net/image/%2s ">
			</td>
			<td>%3s<br/>%4s</td>
			<td>%5s</td>
			<td>%6s</td>
		</tr>'
		,$country['flag']
		,$country['flag']
		,$country['country_name'] 
		,$country['english_name'] 
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
				<th width="120px">国名</th>
				<th width="110px">日本との時差</th>
				<th width="130px">協定世界時(UTC)</th>
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
			<span id="%s" style="padding:0 5px;border-left:5px solid #ff8c00;">
			%s時間
			</span>
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
				<td style="text-align:center" rowspan="%1s">
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
				<span style="padding:0 5px;border-left:5px solid #ff8c00;">
				%s
				</span>
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
		<!--<tr>
			<th colspan="2" class="tbhd">基本情報</th>
		</tr>-->
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
		<!--<tr>
			<th colspan="2" class="tbhd">危険情報</th>
		</tr>-->
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
		<!--<tr>
			<th colspan="2" class="tbhd">通貨</th>
		</tr>-->
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
		<!--<tr>
			<th colspan="2" class="tbhd">ビザ</th>
		</tr>-->';

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
		<span id="kome1" style="padding:0 5px;border-left:5px solid #ff8c00;">
			ビザについて
		</span>
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
		<!--<tr>
			<th colspan="2" class="tbhd">世界遺産</th>
		</tr>-->
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
		<!--<tr>
			<th colspan="2" class="tbhd">旅行ガイド・まとめサイト</th>
		</tr>-->
		<!--<tr>  <th class="tbth">tripadvisor</th>  <td></td></tr>
		<tr>  <th>Wikipedia</th>  <td></td></tr>
		<tr>  <th>Wikitravel</th>  <td></td></tr>
		<tr>  <th>NAVER まとめ</th>  <td></td></tr>
		<tr>  <th>フォートラベル</th>  <td></td></tr>
		<tr>  <th>地球の歩き方</th>  <td></td></tr>
		<tr>  <th>るるぶ</th>  <td></td></tr>
		<tr>  <th>ことりっぷ</th>  <td></td></tr>
		<tr>  <th>wondertrip</th>  <td></td></tr>
		<tr>  <th>RETRIP</th>  <td></td></tr>
		<tr>  <th>TABIZINE</th>  <td></td></tr>
		<tr>  <th>GOTRIP!</th>  <td></td></tr>
		<tr>  <th>トラベルjp</th>  <td></td></tr>
		<tr>  <th>Compathy</th>  <td></td></tr>
		<tr>  <th>世界新聞</th>  <td></td></tr>
		<tr>  <th>tabit</th>  <td></td></tr>
	</table>-->'
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
		<!--<tr>
			<th colspan="2" class="tbhd">ガイドブック</th>
		</tr>-->
		<!--<tr>  <th class="tbth">地球の歩き方</th>  <td></td></tr>
		<tr>  <th>るるぶ</th>  <td></td></tr>
		<tr>  <th>まっぷる</th>  <td></td></tr>
		<tr>  <th>ことりっぷ</th>  <td></td></tr>
		<tr>  <th>タビトモ</th>  <td></td></tr>
		<tr>  <th>トラベルデイズ</th>  <td></td></tr>
		<tr>  <th>aruco</th>  <td></td></tr>
		<tr>  <th>TRANSIT</th>  <td></td></tr>
		<tr>  <th>指さし会話帳</th>  <td></td></tr>
	</table>-->'
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
	<!--<table border="1" cellpadding="3" width="100%%">-->
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
			<td class="nowrap" style="width:310px">%s(%s)</td>
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
		<!--<tr>
			<th colspan="2" class="tbhd">航空券を探す</th>
		</tr>-->'
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
		<!--<tr>
			<th colspan="2" class="tbhd">ホテルを探す</th>
		</tr>-->'
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
		<!--<tr>
			<th colspan="2" class="tbhd">ツアーを探す</th>
		</tr>-->'
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
		<!--<tr>
			<th colspan="2" class="tbhd">現地オプショナルツアーを探す</th>
		</tr>-->'
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
		<!--<tr>
			<th colspan="2" class="tbhd">天気予報</th>
		</tr>-->
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
 * Retrieve the post content.
 *
 * @since 0.71
 *
 * @global int   $page      Page number of a single post/page.
 * @global int   $more      Boolean indicator for whether single post/page is being viewed.
 * @global bool  $preview   Whether post/page is in preview mode.
 * @global array $pages     Array of all pages in post/page. Each array element contains part of the content separated by the <!--nextpage--> tag.
 * @global int   $multipage Boolean indicator for whether multiple pages are in play.
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool   $strip_teaser   Optional. Strip teaser content before the more text. Default is false.
 * @return string
 */
function get_the_content( $more_link_text = null, $strip_teaser = false ) {
	global $page, $more, $preview, $pages, $multipage, $wpdb;

	$post = get_post();

	if ( null === $more_link_text ) {
		$more_link_text = sprintf(
			'<span aria-label="%1$s">%2$s</span>',
			sprintf(
				/* translators: %s: Name of current post */
				__( 'Continue reading %s' ),
				the_title_attribute( array( 'echo' => false ) )
			),
			__( '(more&hellip;)' )
		);
	}

	$output = '';
	$has_teaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required( $post ) )
		return get_the_password_form( $post );

	if ( $page > count( $pages ) ) // if the requested page doesn't exist
		$page = count( $pages ); // give them the highest numbered page that DOES exist

	$content = $pages[$page - 1];
	if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
		$content = explode( $matches[0], $content, 2 );
		if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
			$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );

		$has_teaser = true;
	} else {
		$content = array( $content );
	}

	if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) )
		$strip_teaser = true;

	$teaser = $content[0];

	if ( $more && $strip_teaser && $has_teaser )
		$teaser = '';

	// $output .= $teaser;

	$sql1 = sprintf("
	SELECT c.country_id
	FROM  $wpdb->m_country c
	WHERE c.post_name = '%s'
	"
	,$post->post_name
	);

	$results1 = $wpdb->get_results($sql1);
	$countrys = bzb_object2array($results1);

	switch ($post->post_name) {
		case "city":
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
		// case "taiwan":
		// 	$output .= get_the_content_country(3);
		// 	break;
		// case "srilanka":
		// 	$output .= get_the_content_country(21);
		// 	break;
		default:
			if (count($countrys) > 0){
				$country_id = $countrys[0]["country_id"];
				$output .= get_the_content_country($country_id);
			} else{
				$output .= $teaser;;
			}
			break;
	}
	// switch ($post->ID) {
	// 	case 46:
	// 		$output .= get_the_content_city();
	// 		break;
	// 	case 87:
	// 		$output .= get_the_content_time_difference();
	// 		break;
	// 	case 92:
	// 		$output .= get_the_content_visa();
	// 		break;
	// 	case 99:
	// 		$output .= get_the_content_rate();
	// 		break;
	// 	case 102:
	// 		$output .= get_the_content_language();
	// 		break;
	// 	case 105:
	// 		$output .= get_the_content_religion();
	// 		break;
	// 	default:
	// 		$output .= $teaser;;
	// 		break;
	// }

	if ( count( $content ) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty( $more_link_text ) )

				/**
				 * Filters the Read More link text.
				 *
				 * @since 2.8.0
				 *
				 * @param string $more_link_element Read More link element.
				 * @param string $more_link_text    Read More text.
				 */
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			$output = force_balance_tags( $output );
		}
	}

	if ( $preview ) // Preview fix for JavaScript bug with foreign languages.
		$output =	preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );

	return $output;
}

/**
 * Preview fix for JavaScript bug with foreign languages.
 *
 * @since 3.1.0
 * @access private
 *
 * @param array $match Match array from preg_replace_callback.
 * @return string
 */
function _convert_urlencoded_to_entities( $match ) {
	return '&#' . base_convert( $match[1], 16, 10 ) . ';';
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 */
function the_excerpt() {

	/**
	 * Filters the displayed post excerpt.
	 *
	 * @since 0.71
	 *
	 * @see get_the_excerpt()
	 *
	 * @param string $post_excerpt The post excerpt.
	 */
	echo apply_filters( 'the_excerpt', get_the_excerpt() );
}

/**
 * Retrieves the post excerpt.
 *
 * @since 0.71
 * @since 4.5.0 Introduced the `$post` parameter.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string Post excerpt.
 */
function get_the_excerpt( $post = null ) {
	if ( is_bool( $post ) ) {
		_deprecated_argument( __FUNCTION__, '2.3.0' );
	}

	$post = get_post( $post );
	if ( empty( $post ) ) {
		return '';
	}

	if ( post_password_required( $post ) ) {
		return __( 'There is no excerpt because this is a protected post.' );
	}

	/**
	 * Filters the retrieved post excerpt.
	 *
	 * @since 1.2.0
	 * @since 4.5.0 Introduced the `$post` parameter.
	 *
	 * @param string $post_excerpt The post excerpt.
	 * @param WP_Post $post Post object.
	 */
	return apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );
}

/**
 * Whether post has excerpt.
 *
 * @since 2.3.0
 *
 * @param int|WP_Post $id Optional. Post ID or post object.
 * @return bool
 */
function has_excerpt( $id = 0 ) {
	$post = get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param int|WP_Post  $post_id Optional. Post ID or post object. Defaults to the global `$post`.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieves the classes for the post div as an array.
 *
 * The class names are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. If the post has a
 * post thumbnail, 'has-post-thumbnail' is added as a class. For each taxonomy that
 * the post belongs to, a class will be added of the format '{$taxonomy}-{$slug}' -
 * eg 'category-foo' or 'my_custom_taxonomy-bar'.
 *
 * The 'post_tag' taxonomy is a special
 * case; the class has the 'tag-' prefix instead of 'post_tag-'. All classes are
 * passed through the filter, {@see 'post_class'}, with the list of classes, followed by
 * $class parameter value, with the post ID as the last parameter.
 *
 * @since 2.7.0
 * @since 4.2.0 Custom taxonomy classes were added.
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param int|WP_Post  $post_id Optional. Post ID or post object.
 * @return array Array of classes.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post( $post_id );

	$classes = array();

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	if ( ! $post ) {
		return $classes;
	}

	$classes[] = 'post-' . $post->ID;
	if ( ! is_admin() )
		$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && !is_wp_error($post_format) )
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		else
			$classes[] = 'format-standard';
	}

	$post_password_required = post_password_required( $post->ID );

	// Post requires password.
	if ( $post_password_required ) {
		$classes[] = 'post-password-required';
	} elseif ( ! empty( $post->post_password ) ) {
		$classes[] = 'post-password-protected';
	}

	// Post thumbnails.
	if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post->ID ) && ! is_attachment( $post ) && ! $post_password_required ) {
		$classes[] = 'has-post-thumbnail';
	}

	// sticky for Sticky Posts
	if ( is_sticky( $post->ID ) ) {
		if ( is_home() && ! is_paged() ) {
			$classes[] = 'sticky';
		} elseif ( is_admin() ) {
			$classes[] = 'status-sticky';
		}
	}

	// hentry for hAtom compliance
	$classes[] = 'hentry';

	// All public taxonomies
	$taxonomies = get_taxonomies( array( 'public' => true ) );
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
			foreach ( (array) get_the_terms( $post->ID, $taxonomy ) as $term ) {
				if ( empty( $term->slug ) ) {
					continue;
				}

				$term_class = sanitize_html_class( $term->slug, $term->term_id );
				if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
					$term_class = $term->term_id;
				}

				// 'post_tag' uses the 'tag' prefix for backward compatibility.
				if ( 'post_tag' == $taxonomy ) {
					$classes[] = 'tag-' . $term_class;
				} else {
					$classes[] = sanitize_html_class( $taxonomy . '-' . $term_class, $taxonomy . '-' . $term->term_id );
				}
			}
		}
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS classes for the current post.
	 *
	 * @since 2.7.0
	 *
	 * @param array $classes An array of post classes.
	 * @param array $class   An array of additional classes added to the post.
	 * @param int   $post_id The post ID.
	 */
	$classes = apply_filters( 'post_class', $classes, $class, $post->ID );

	return array_unique( $classes );
}

/**
 * Display the classes for the body element.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @global WP_Query $wp_query
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query;

	$classes = array();

	if ( is_rtl() )
		$classes[] = 'rtl';

	if ( is_front_page() )
		$classes[] = 'home';
	if ( is_home() )
		$classes[] = 'blog';
	if ( is_archive() )
		$classes[] = 'archive';
	if ( is_date() )
		$classes[] = 'date';
	if ( is_search() ) {
		$classes[] = 'search';
		$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	}
	if ( is_paged() )
		$classes[] = 'paged';
	if ( is_attachment() )
		$classes[] = 'attachment';
	if ( is_404() )
		$classes[] = 'error404';

	if ( is_singular() ) {
		$post_id = $wp_query->get_queried_object_id();
		$post = $wp_query->get_queried_object();
		$post_type = $post->post_type;

		if ( is_page_template() ) {
			$classes[] = "{$post_type}-template";

			$template_slug  = get_page_template_slug( $post_id );
			$template_parts = explode( '/', $template_slug );

			foreach ( $template_parts as $part ) {
				$classes[] = "{$post_type}-template-" . sanitize_html_class( str_replace( array( '.', '/' ), '-', basename( $part, '.php' ) ) );
			}
			$classes[] = "{$post_type}-template-" . sanitize_html_class( str_replace( '.', '-', $template_slug ) );
		} else {
			$classes[] = "{$post_type}-template-default";
		}

		if ( is_single() ) {
			$classes[] = 'single';
			if ( isset( $post->post_type ) ) {
				$classes[] = 'single-' . sanitize_html_class( $post->post_type, $post_id );
				$classes[] = 'postid-' . $post_id;

				// Post Format
				if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
					$post_format = get_post_format( $post->ID );

					if ( $post_format && !is_wp_error($post_format) )
						$classes[] = 'single-format-' . sanitize_html_class( $post_format );
					else
						$classes[] = 'single-format-standard';
				}
			}
		}

		if ( is_attachment() ) {
			$mime_type = get_post_mime_type($post_id);
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[] = 'attachmentid-' . $post_id;
			$classes[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
		} elseif ( is_page() ) {
			$classes[] = 'page';

			$page_id = $wp_query->get_queried_object_id();

			$post = get_post($page_id);

			$classes[] = 'page-id-' . $page_id;

			if ( get_pages( array( 'parent' => $page_id, 'number' => 1 ) ) ) {
				$classes[] = 'page-parent';
			}

			if ( $post->post_parent ) {
				$classes[] = 'page-child';
				$classes[] = 'parent-pageid-' . $post->post_parent;
			}
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$classes[] = 'post-type-archive';
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) )
				$post_type = reset( $post_type );
			$classes[] = 'post-type-archive-' . sanitize_html_class( $post_type );
		} elseif ( is_author() ) {
			$author = $wp_query->get_queried_object();
			$classes[] = 'author';
			if ( isset( $author->user_nicename ) ) {
				$classes[] = 'author-' . sanitize_html_class( $author->user_nicename, $author->ID );
				$classes[] = 'author-' . $author->ID;
			}
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$classes[] = 'category';
			if ( isset( $cat->term_id ) ) {
				$cat_class = sanitize_html_class( $cat->slug, $cat->term_id );
				if ( is_numeric( $cat_class ) || ! trim( $cat_class, '-' ) ) {
					$cat_class = $cat->term_id;
				}

				$classes[] = 'category-' . $cat_class;
				$classes[] = 'category-' . $cat->term_id;
			}
		} elseif ( is_tag() ) {
			$tag = $wp_query->get_queried_object();
			$classes[] = 'tag';
			if ( isset( $tag->term_id ) ) {
				$tag_class = sanitize_html_class( $tag->slug, $tag->term_id );
				if ( is_numeric( $tag_class ) || ! trim( $tag_class, '-' ) ) {
					$tag_class = $tag->term_id;
				}

				$classes[] = 'tag-' . $tag_class;
				$classes[] = 'tag-' . $tag->term_id;
			}
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( isset( $term->term_id ) ) {
				$term_class = sanitize_html_class( $term->slug, $term->term_id );
				if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
					$term_class = $term->term_id;
				}

				$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
				$classes[] = 'term-' . $term_class;
				$classes[] = 'term-' . $term->term_id;
			}
		}
	}

	if ( is_user_logged_in() )
		$classes[] = 'logged-in';

	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
		$classes[] = 'no-customize-support';
	}

	if ( get_background_color() !== get_theme_support( 'custom-background', 'default-color' ) || get_background_image() )
		$classes[] = 'custom-background';

	if ( has_custom_logo() ) {
		$classes[] = 'wp-custom-logo';
	}

	$page = $wp_query->get( 'page' );

	if ( ! $page || $page < 2 )
		$page = $wp_query->get( 'paged' );

	if ( $page && $page > 1 && ! is_404() ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() )
			$classes[] = 'single-paged-' . $page;
		elseif ( is_page() )
			$classes[] = 'page-paged-' . $page;
		elseif ( is_category() )
			$classes[] = 'category-paged-' . $page;
		elseif ( is_tag() )
			$classes[] = 'tag-paged-' . $page;
		elseif ( is_date() )
			$classes[] = 'date-paged-' . $page;
		elseif ( is_author() )
			$classes[] = 'author-paged-' . $page;
		elseif ( is_search() )
			$classes[] = 'search-paged-' . $page;
		elseif ( is_post_type_archive() )
			$classes[] = 'post-type-paged-' . $page;
	}

	if ( ! empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS body classes for the current post or page.
	 *
	 * @since 2.8.0
	 *
	 * @param array $classes An array of body classes.
	 * @param array $class   An array of additional classes added to the body.
	 */
	$classes = apply_filters( 'body_class', $classes, $class );

	return array_unique( $classes );
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|WP_Post|null $post An optional post. Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	$post = get_post($post);

	if ( empty( $post->post_password ) ) {
		/** This filter is documented in wp-includes/post.php */
		return apply_filters( 'post_password_required', false, $post );
	}

	if ( ! isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
		/** This filter is documented in wp-includes/post.php */
		return apply_filters( 'post_password_required', true, $post );
	}

	$hasher = new PasswordHash( 8, true );

	$hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	if ( 0 !== strpos( $hash, '$P$B' ) ) {
		$required = true;
	} else {
		$required = ! $hasher->CheckPassword( $post->post_password, $hash );
	}

	/**
	 * Filters whether a post requires the user to supply a password.
	 *
	 * @since 4.7.0
	 *
	 * @param bool    $required Whether the user needs to supply a password. True if password has not been
	 *                          provided or is incorrect, false if password has been supplied or is not required.
	 * @param WP_Post $post     Post data.
	 */
	return apply_filters( 'post_password_required', $required, $post );
}

//
// Page Template Functions for usage in Themes
//

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * @since 1.2.0
 *
 * @global int $page
 * @global int $numpages
 * @global int $multipage
 * @global int $more
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string       $before           HTML or text to prepend to each link. Default is `<p> Pages:`.
 *     @type string       $after            HTML or text to append to each link. Default is `</p>`.
 *     @type string       $link_before      HTML or text to prepend to each link, inside the `<a>` tag.
 *                                          Also prepended to the current item, which is not linked. Default empty.
 *     @type string       $link_after       HTML or text to append to each Pages link inside the `<a>` tag.
 *                                          Also appended to the current item, which is not linked. Default empty.
 *     @type string       $next_or_number   Indicates whether page numbers should be used. Valid values are number
 *                                          and next. Default is 'number'.
 *     @type string       $separator        Text between pagination links. Default is ' '.
 *     @type string       $nextpagelink     Link text for the next page link, if available. Default is 'Next Page'.
 *     @type string       $previouspagelink Link text for the previous page link, if available. Default is 'Previous Page'.
 *     @type string       $pagelink         Format string for page numbers. The % in the parameter string will be
 *                                          replaced with the page number, so 'Page %' generates "Page 1", "Page 2", etc.
 *                                          Defaults to '%', just the page number.
 *     @type int|bool     $echo             Whether to echo or not. Accepts 1|true or 0|false. Default 1|true.
 * }
 * @return string Formatted output in HTML.
 */
function wp_link_pages( $args = '' ) {
	global $page, $numpages, $multipage, $more;

	$defaults = array(
		'before'           => '<p>' . __( 'Pages:' ),
		'after'            => '</p>',
		'link_before'      => '',
		'link_after'       => '',
		'next_or_number'   => 'number',
		'separator'        => ' ',
		'nextpagelink'     => __( 'Next page' ),
		'previouspagelink' => __( 'Previous page' ),
		'pagelink'         => '%',
		'echo'             => 1
	);

	$params = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used in retrieving page links for paginated posts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params An array of arguments for page links for paginated posts.
	 */
	$r = apply_filters( 'wp_link_pages_args', $params );

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $r['next_or_number'] ) {
			$output .= $r['before'];
			for ( $i = 1; $i <= $numpages; $i++ ) {
				$link = $r['link_before'] . str_replace( '%', $i, $r['pagelink'] ) . $r['link_after'];
				if ( $i != $page || ! $more && 1 == $page ) {
					$link = _wp_link_page( $i ) . $link . '</a>';
				}
				/**
				 * Filters the HTML output of individual page number links.
				 *
				 * @since 3.6.0
				 *
				 * @param string $link The page number HTML output.
				 * @param int    $i    Page number for paginated posts' page links.
				 */
				$link = apply_filters( 'wp_link_pages_link', $link, $i );

				// Use the custom links separator beginning with the second link.
				$output .= ( 1 === $i ) ? ' ' : $r['separator'];
				$output .= $link;
			}
			$output .= $r['after'];
		} elseif ( $more ) {
			$output .= $r['before'];
			$prev = $page - 1;
			if ( $prev > 0 ) {
				$link = _wp_link_page( $prev ) . $r['link_before'] . $r['previouspagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
			}
			$next = $page + 1;
			if ( $next <= $numpages ) {
				if ( $prev ) {
					$output .= $r['separator'];
				}
				$link = _wp_link_page( $next ) . $r['link_before'] . $r['nextpagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $next );
			}
			$output .= $r['after'];
		}
	}

	/**
	 * Filters the HTML output of page links for paginated posts.
	 *
	 * @since 3.6.0
	 *
	 * @param string $output HTML output of paginated posts' page links.
	 * @param array  $args   An array of arguments.
	 */
	$html = apply_filters( 'wp_link_pages', $output, $args );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param int $i Page number.
 * @return string Link.
 */
function _wp_link_page( $i ) {
	global $wp_rewrite;
	$post = get_post();
	$query_args = array();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}

	if ( is_preview() ) {

		if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
			$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
			$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
		}

		$url = get_preview_post_link( $post, $query_args, $url );
	}

	return '<a href="' . esc_url( $url ) . '">';
}

//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return false|string|array Array of values or single value, if only one element exists. False will be returned if key does not exist.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( !isset( $custom[$key] ) )
		return false;
	elseif ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}

/**
 * Display list of post custom fields.
 *
 * @since 1.2.0
 *
 * @internal This will probably change at some point...
 *
 */
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( (array) $keys as $key ) {
			$keyt = trim($key);
			if ( is_protected_meta( $keyt, 'post' ) )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');

			/**
			 * Filters the HTML output of the li element in the post custom fields list.
			 *
			 * @since 2.2.0
			 *
			 * @param string $html  The HTML output for the li element.
			 * @param string $key   Meta key.
			 * @param string $value Meta value.
			 */
			echo apply_filters( 'the_meta_key', "<li><span class='post-meta-key'>$key:</span> $value</li>\n", $key, $value );
		}
		echo "</ul>\n";
	}
}

//
// Pages
//

/**
 * Retrieve or display list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 * @since 4.2.0 The `$value_field` argument was added.
 * @since 4.3.0 The `$class` argument was added.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a pages drop-down element.
 *
 *     @type int          $depth                 Maximum depth. Default 0.
 *     @type int          $child_of              Page ID to retrieve child pages of. Default 0.
 *     @type int|string   $selected              Value of the option that should be selected. Default 0.
 *     @type bool|int     $echo                  Whether to echo or return the generated markup. Accepts 0, 1,
 *                                               or their bool equivalents. Default 1.
 *     @type string       $name                  Value for the 'name' attribute of the select element.
 *                                               Default 'page_id'.
 *     @type string       $id                    Value for the 'id' attribute of the select element.
 *     @type string       $class                 Value for the 'class' attribute of the select element. Default: none.
 *                                               Defaults to the value of `$name`.
 *     @type string       $show_option_none      Text to display for showing no pages. Default empty (does not display).
 *     @type string       $show_option_no_change Text to display for "no change" option. Default empty (does not display).
 *     @type string       $option_none_value     Value to use when no page is selected. Default empty.
 *     @type string       $value_field           Post field used to populate the 'value' attribute of the option
 *                                               elements. Accepts any valid post field. Default 'ID'.
 * }
 * @return string HTML content, if not displaying.
 */
function wp_dropdown_pages( $args = '' ) {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'id' => '',
		'class' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => '',
		'value_field' => 'ID',
	);

	$r = wp_parse_args( $args, $defaults );

	$pages = get_pages( $r );
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty( $r['id'] ) ) {
		$r['id'] = $r['name'];
	}

	if ( ! empty( $pages ) ) {
		$class = '';
		if ( ! empty( $r['class'] ) ) {
			$class = " class='" . esc_attr( $r['class'] ) . "'";
		}

		$output = "<select name='" . esc_attr( $r['name'] ) . "'" . $class . " id='" . esc_attr( $r['id'] ) . "'>\n";
		if ( $r['show_option_no_change'] ) {
			$output .= "\t<option value=\"-1\">" . $r['show_option_no_change'] . "</option>\n";
		}
		if ( $r['show_option_none'] ) {
			$output .= "\t<option value=\"" . esc_attr( $r['option_none_value'] ) . '">' . $r['show_option_none'] . "</option>\n";
		}
		$output .= walk_page_dropdown_tree( $pages, $r['depth'], $r );
		$output .= "</select>\n";
	}

	/**
	 * Filters the HTML output of a list of pages as a drop down.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 `$r` and `$pages` added as arguments.
	 *
	 * @param string $output HTML output for drop down list of pages.
	 * @param array  $r      The parsed arguments array.
	 * @param array  $pages  List of WP_Post objects returned by `get_pages()`
 	 */
	$html = apply_filters( 'wp_dropdown_pages', $output, $r, $pages );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 * @since 4.7.0 Added the `item_spacing` argument.
 *
 * @see get_pages()
 *
 * @global WP_Query $wp_query
 *
 * @param array|string $args {
 *     Array or string of arguments. Optional.
 *
 *     @type int          $child_of     Display only the sub-pages of a single page by ID. Default 0 (all pages).
 *     @type string       $authors      Comma-separated list of author IDs. Default empty (all authors).
 *     @type string       $date_format  PHP date format to use for the listed pages. Relies on the 'show_date' parameter.
 *                                      Default is the value of 'date_format' option.
 *     @type int          $depth        Number of levels in the hierarchy of pages to include in the generated list.
 *                                      Accepts -1 (any depth), 0 (all pages), 1 (top-level pages only), and n (pages to
 *                                      the given n depth). Default 0.
 *     @type bool         $echo         Whether or not to echo the list of pages. Default true.
 *     @type string       $exclude      Comma-separated list of page IDs to exclude. Default empty.
 *     @type array        $include      Comma-separated list of page IDs to include. Default empty.
 *     @type string       $link_after   Text or HTML to follow the page link label. Default null.
 *     @type string       $link_before  Text or HTML to precede the page link label. Default null.
 *     @type string       $post_type    Post type to query for. Default 'page'.
 *     @type string|array $post_status  Comma-separated list or array of post statuses to include. Default 'publish'.
 *     @type string       $show_date    Whether to display the page publish or modified date for each page. Accepts
 *                                      'modified' or any other value. An empty value hides the date. Default empty.
 *     @type string       $sort_column  Comma-separated list of column names to sort the pages by. Accepts 'post_author',
 *                                      'post_date', 'post_title', 'post_name', 'post_modified', 'post_modified_gmt',
 *                                      'menu_order', 'post_parent', 'ID', 'rand', or 'comment_count'. Default 'post_title'.
 *     @type string       $title_li     List heading. Passing a null or empty value will result in no heading, and the list
 *                                      will not be wrapped with unordered list `<ul>` tags. Default 'Pages'.
 *     @type string       $item_spacing Whether to preserve whitespace within the menu's HTML. Accepts 'preserve' or 'discard'.
 *                                      Default 'preserve'.
 *     @type Walker       $walker       Walker instance to use for listing pages. Default empty (Walker_Page).
 * }
 * @return string|void HTML list of pages.
 */
function wp_list_pages( $args = '' ) {
	$defaults = array(
		'depth'        => 0,
		'show_date'    => '',
		'date_format'  => get_option( 'date_format' ),
		'child_of'     => 0,
		'exclude'      => '',
		'title_li'     => __( 'Pages' ),
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
		'link_before'  => '',
		'link_after'   => '',
		'item_spacing' => 'preserve',
		'walker'       => '',
	);

	$r = wp_parse_args( $args, $defaults );

	if ( ! in_array( $r['item_spacing'], array( 'preserve', 'discard' ), true ) ) {
		// invalid value, fall back to default.
		$r['item_spacing'] = $defaults['item_spacing'];
	}

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();

	/**
	 * Filters the array of pages to exclude from the pages list.
	 *
	 * @since 2.1.0
	 *
	 * @param array $exclude_array An array of page IDs to exclude.
	 */
	$r['exclude'] = implode( ',', apply_filters( 'wp_list_pages_excludes', $exclude_array ) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages( $r );

	if ( ! empty( $pages ) ) {
		if ( $r['title_li'] ) {
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
		}
		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
			$current_page = get_queried_object_id();
		} elseif ( is_singular() ) {
			$queried_object = get_queried_object();
			if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
				$current_page = $queried_object->ID;
			}
		}

		$output .= walk_page_tree( $pages, $r['depth'], $current_page, $r );

		if ( $r['title_li'] ) {
			$output .= '</ul></li>';
		}
	}

	/**
	 * Filters the HTML output of the pages to list.
	 *
	 * @since 1.5.1
	 * @since 4.4.0 `$pages` added as arguments.
	 *
	 * @see wp_list_pages()
	 *
	 * @param string $output HTML output of the pages list.
	 * @param array  $r      An array of page-listing arguments.
	 * @param array  $pages  List of WP_Post objects returned by `get_pages()`
	 */
	$html = apply_filters( 'wp_list_pages', $output, $r, $pages );

	if ( $r['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Displays or retrieves a list of pages with an optional home link.
 *
 * The arguments are listed below and part of the arguments are for wp_list_pages()} function.
 * Check that function for more info on those arguments.
 *
 * @since 2.7.0
 * @since 4.4.0 Added `menu_id`, `container`, `before`, `after`, and `walker` arguments.
 * @since 4.7.0 Added the `item_spacing` argument.
 *
 * @param array|string $args {
 *     Optional. Arguments to generate a page menu. See wp_list_pages() for additional arguments.
 *
 *     @type string          $sort_column  How to short the list of pages. Accepts post column names.
 *                                         Default 'menu_order, post_title'.
 *     @type string          $menu_id      ID for the div containing the page list. Default is empty string.
 *     @type string          $menu_class   Class to use for the element containing the page list. Default 'menu'.
 *     @type string          $container    Element to use for the element containing the page list. Default 'div'.
 *     @type bool            $echo         Whether to echo the list or return it. Accepts true (echo) or false (return).
 *                                         Default true.
 *     @type int|bool|string $show_home    Whether to display the link to the home page. Can just enter the text
 *                                         you'd like shown for the home link. 1|true defaults to 'Home'.
 *     @type string          $link_before  The HTML or text to prepend to $show_home text. Default empty.
 *     @type string          $link_after   The HTML or text to append to $show_home text. Default empty.
 *     @type string          $before       The HTML or text to prepend to the menu. Default is '<ul>'.
 *     @type string          $after        The HTML or text to append to the menu. Default is '</ul>'.
 *     @type string          $item_spacing Whether to preserve whitespace within the menu's HTML. Accepts 'preserve' or 'discard'. Default 'discard'.
 *     @type Walker          $walker       Walker instance to use for listing pages. Default empty (Walker_Page).
 * }
 * @return string|void HTML menu
 */
function wp_page_menu( $args = array() ) {
	$defaults = array(
		'sort_column'  => 'menu_order, post_title',
		'menu_id'      => '',
		'menu_class'   => 'menu',
		'container'    => 'div',
		'echo'         => true,
		'link_before'  => '',
		'link_after'   => '',
		'before'       => '<ul>',
		'after'        => '</ul>',
		'item_spacing' => 'discard',
		'walker'       => '',
	);
	$args = wp_parse_args( $args, $defaults );

	if ( ! in_array( $args['item_spacing'], array( 'preserve', 'discard' ) ) ) {
		// invalid value, fall back to default.
		$args['item_spacing'] = $defaults['item_spacing'];
	}

	if ( 'preserve' === $args['item_spacing'] ) {
		$t = "\t";
		$n = "\n";
	} else {
		$t = '';
		$n = '';
	}

	/**
	 * Filters the arguments used to generate a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param array $args An array of page menu arguments.
	 */
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= wp_list_pages( $list_args );

	$container = sanitize_text_field( $args['container'] );

	// Fallback in case `wp_nav_menu()` was called without a container.
	if ( empty( $container ) ) {
		$container = 'div';
	}

	if ( $menu ) {

		// wp_nav_menu doesn't set before and after
		if ( isset( $args['fallback_cb'] ) &&
			'wp_page_menu' === $args['fallback_cb'] &&
			'ul' !== $container ) {
			$args['before'] = "<ul>{$n}";
			$args['after'] = '</ul>';
		}

		$menu = $args['before'] . $menu . $args['after'];
	}

	$attrs = '';
	if ( ! empty( $args['menu_id'] ) ) {
		$attrs .= ' id="' . esc_attr( $args['menu_id'] ) . '"';
	}

	if ( ! empty( $args['menu_class'] ) ) {
		$attrs .= ' class="' . esc_attr( $args['menu_class'] ) . '"';
	}

	$menu = "<{$container}{$attrs}>" . $menu . "</{$container}>{$n}";

	/**
	 * Filters the HTML output of a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param string $menu The HTML output.
	 * @param array  $args An array of arguments.
	 */
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

//
// Page helpers
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 *
 * @param array $pages
 * @param int   $depth
 * @param int   $current_page
 * @param array $r
 * @return string
 */
function walk_page_tree( $pages, $depth, $current_page, $r ) {
	if ( empty($r['walker']) )
		$walker = new Walker_Page;
	else
		$walker = $r['walker'];

	foreach ( (array) $pages as $page ) {
		if ( $page->post_parent )
			$r['pages_with_children'][ $page->post_parent ] = true;
	}

	$args = array($pages, $depth, $r, $current_page);
	return call_user_func_array(array($walker, 'walk'), $args);
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 *
 * @return string
 */
function walk_page_dropdown_tree() {
	$args = func_get_args();
	if ( empty($args[2]['walker']) ) // the user's options are the third parameter
		$walker = new Walker_PageDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array($walker, 'walk'), $args);
}

//
// Attachments
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int|WP_Post $id Optional. Post ID or post object.
 * @param bool        $fullsize     Optional, default is false. Whether to use full size.
 * @param bool        $deprecated   Deprecated. Not used.
 * @param bool        $permalink    Optional, default is false. Whether to include permalink.
 */
function the_attachment_link( $id = 0, $fullsize = false, $deprecated = false, $permalink = false ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.5.0' );

	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @since 4.4.0 The `$id` parameter can now accept either a post ID or `WP_Post` object.
 *
 * @param int|WP_Post  $id        Optional. Post ID or post object.
 * @param string|array $size      Optional. Image size. Accepts any valid image size, or an array
 *                                of width and height values in pixels (in that order).
 *                                Default 'thumbnail'.
 * @param bool         $permalink Optional, Whether to add permalink to image. Default false.
 * @param bool         $icon      Optional. Whether the attachment is an icon. Default false.
 * @param string|false $text      Optional. Link text to use. Activated by passing a string, false otherwise.
 *                                Default false.
 * @param array|string $attr      Optional. Array or string of attributes. Default empty.
 * @return string HTML content.
 */
function wp_get_attachment_link( $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false, $attr = '' ) {
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' !== $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) ) {
		return __( 'Missing Attachment' );
	}

	if ( $permalink ) {
		$url = get_attachment_link( $_post->ID );
	}

	if ( $text ) {
		$link_text = $text;
	} elseif ( $size && 'none' != $size ) {
		$link_text = wp_get_attachment_image( $_post->ID, $size, $icon, $attr );
	} else {
		$link_text = '';
	}

	if ( '' === trim( $link_text ) ) {
		$link_text = $_post->post_title;
	}

	if ( '' === trim( $link_text ) ) {
		$link_text = esc_html( pathinfo( get_attached_file( $_post->ID ), PATHINFO_FILENAME ) );
	}
	/**
	 * Filters a retrieved attachment page link.
	 *
	 * @since 2.7.0
	 *
	 * @param string       $link_html The page link HTML output.
	 * @param int          $id        Post ID.
	 * @param string|array $size      Size of the image. Image size or array of width and height values (in that order).
	 *                                Default 'thumbnail'.
	 * @param bool         $permalink Whether to add permalink to image. Default false.
	 * @param bool         $icon      Whether to include an icon. Default false.
	 * @param string|bool  $text      If string, will be link text. Default false.
	 */
	return apply_filters( 'wp_get_attachment_link', "<a href='" . esc_url( $url ) . "'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}

/**
 * Wrap attachment in paragraph tag before content.
 *
 * @since 2.0.0
 *
 * @param string $content
 * @return string
 */
function prepend_attachment($content) {
	$post = get_post();

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	if ( wp_attachment_is( 'video', $post ) ) {
		$meta = wp_get_attachment_metadata( get_the_ID() );
		$atts = array( 'src' => wp_get_attachment_url() );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$atts['width'] = (int) $meta['width'];
			$atts['height'] = (int) $meta['height'];
		}
		if ( has_post_thumbnail() ) {
			$atts['poster'] = wp_get_attachment_url( get_post_thumbnail_id() );
		}
		$p = wp_video_shortcode( $atts );
	} elseif ( wp_attachment_is( 'audio', $post ) ) {
		$p = wp_audio_shortcode( array( 'src' => wp_get_attachment_url() ) );
	} else {
		$p = '<p class="attachment">';
		// show the medium sized image representation of the attachment if available, and link to the raw file
		$p .= wp_get_attachment_link(0, 'medium', false);
		$p .= '</p>';
	}

	/**
	 * Filters the attachment markup to be prepended to the post content.
	 *
	 * @since 2.0.0
	 *
	 * @see prepend_attachment()
	 *
	 * @param string $p The attachment HTML output.
	 */
	$p = apply_filters( 'prepend_attachment', $p );

	return "$p\n$content";
}

//
// Misc
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form( $post = 0 ) {
	$post = get_post( $post );
	$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
	<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
	<p><label for="' . $label . '">' . __( 'Password:' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>
	';

	/**
	 * Filters the HTML output for the protected post password form.
	 *
	 * If modifying the password field, please note that the core database schema
	 * limits the password field to 20 characters regardless of the value of the
	 * size attribute in the form input.
	 *
	 * @since 2.7.0
	 *
	 * @param string $output The password form HTML output.
	 */
	return apply_filters( 'the_password_form', $output );
}

/**
 * Whether currently in a page template.
 *
 * This template tag allows you to determine if you are in a page template.
 * You can optionally provide a template name or array of template names
 * and then the check will be specific to that template.
 *
 * @since 2.5.0
 * @since 4.2.0 The `$template` parameter was changed to also accept an array of page templates.
 * @since 4.7.0 Now works with any post type, not just pages.
 *
 * @param string|array $template The specific template name or array of templates to match.
 * @return bool True on success, false on failure.
 */
function is_page_template( $template = '' ) {
	if ( ! is_singular() ) {
		return false;
	}

	$page_template = get_page_template_slug( get_queried_object_id() );

	if ( empty( $template ) )
		return (bool) $page_template;

	if ( $template == $page_template )
		return true;

	if ( is_array( $template ) ) {
		if ( ( in_array( 'default', $template, true ) && ! $page_template )
			|| in_array( $page_template, $template, true )
		) {
			return true;
		}
	}

	return ( 'default' === $template && ! $page_template );
}

/**
 * Get the specific template name for a given post.
 *
 * @since 3.4.0
 * @since 4.7.0 Now works with any post type, not just pages.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string|false Page template filename. Returns an empty string when the default page template
 * 	is in use. Returns false if the post does not exist.
 */
function get_page_template_slug( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$template = get_post_meta( $post->ID, '_wp_page_template', true );

	if ( ! $template || 'default' == $template ) {
		return '';
	}

	return $template;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @since 2.6.0
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool       $link     Optional, default is true. Link to revisions's page?
 * @return string|false i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	/* translators: revision date format, see https://secure.php.net/date */
	$datef = _x( 'F j, Y @ H:i:s', 'revision date format' );
	/* translators: %s: revision date */
	$autosavef = __( '%s [Autosave]' );
	/* translators: %s: revision date */
	$currentf  = __( '%s [Current Revision]' );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	if ( !wp_is_post_revision( $revision ) )
		$date = sprintf( $currentf, $date );
	elseif ( wp_is_post_autosave( $revision ) )
		$date = sprintf( $autosavef, $date );

	return $date;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @since 3.6.0
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool       $link     Optional, default is true. Link to revisions's page?
 * @return string|false gravatar, user, i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title_expanded( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	$author = get_the_author_meta( 'display_name', $revision->post_author );
	/* translators: revision date format, see https://secure.php.net/date */
	$datef = _x( 'F j, Y @ H:i:s', 'revision date format' );

	$gravatar = get_avatar( $revision->post_author, 24 );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	$revision_date_author = sprintf(
		/* translators: post revision title: 1: author avatar, 2: author name, 3: time ago, 4: date */
		__( '%1$s %2$s, %3$s ago (%4$s)' ),
		$gravatar,
		$author,
		human_time_diff( strtotime( $revision->post_modified ), current_time( 'timestamp' ) ),
		$date
	);

	/* translators: %s: revision date with author avatar */
	$autosavef = __( '%s [Autosave]' );
	/* translators: %s: revision date with author avatar */
	$currentf  = __( '%s [Current Revision]' );

	if ( !wp_is_post_revision( $revision ) )
		$revision_date_author = sprintf( $currentf, $revision_date_author );
	elseif ( wp_is_post_autosave( $revision ) )
		$revision_date_author = sprintf( $autosavef, $revision_date_author );

	/**
	 * Filters the formatted author and date for a revision.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $revision_date_author The formatted string.
	 * @param WP_Post $revision             The revision object.
	 * @param bool    $link                 Whether to link to the revisions page, as passed into
	 *                                      wp_post_revision_title_expanded().
	 */
	return apply_filters( 'wp_post_revision_title_expanded', $revision_date_author, $revision, $link );
}

/**
 * Display list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * @since 2.6.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
 * @param string      $type    'all' (default), 'revision' or 'autosave'
 */
function wp_list_post_revisions( $post_id = 0, $type = 'all' ) {
	if ( ! $post = get_post( $post_id ) )
		return;

	// $args array with (parent, format, right, left, type) deprecated since 3.6
	if ( is_array( $type ) ) {
		$type = ! empty( $type['type'] ) ? $type['type']  : $type;
		_deprecated_argument( __FUNCTION__, '3.6.0' );
	}

	if ( ! $revisions = wp_get_post_revisions( $post->ID ) )
		return;

	$rows = '';
	foreach ( $revisions as $revision ) {
		if ( ! current_user_can( 'read_post', $revision->ID ) )
			continue;

		$is_autosave = wp_is_post_autosave( $revision );
		if ( ( 'revision' === $type && $is_autosave ) || ( 'autosave' === $type && ! $is_autosave ) )
			continue;

		$rows .= "\t<li>" . wp_post_revision_title_expanded( $revision ) . "</li>\n";
	}

	echo "<div class='hide-if-js'><p>" . __( 'JavaScript must be enabled to use this feature.' ) . "</p></div>\n";

	echo "<ul class='post-revisions hide-if-no-js'>\n";
	echo $rows;
	echo "</ul>";
}
