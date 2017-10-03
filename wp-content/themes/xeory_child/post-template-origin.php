<?php

/**
 *
 * ページ記事内容取得
 *
 */
function get_the_content_page( $post_name ) {
	global $wpdb;

	$output = '<div class="con_info">';
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
			} else {
				$sql2 = sprintf("
				SELECT h.heritage_id
				FROM  $wpdb->m_heritage h
				WHERE h.post_name = '%s'"
				,$post_name
				);
				$heritages = $wpdb->get_results($sql2);
				if (count($heritages) > 0){
					$heritage_id = $heritages[0]->heritage_id;
					$output .= get_the_content_heritage($heritage_id);
				} else {
					return "";
				}
			}
			break;
	}
	$output .= '
		</div>';
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
		LIMIT 30
	"
	);

	$results = $wpdb->get_results($sql);
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
	return $output;
}

/**
 *
 * localhostの場合、valを返す
 * アフィリエイトリンクをlocalhostで表示しないようにするため
 */
function localval($url, $val) {
	$output = $url;
	if(strpos($_SERVER["HTTP_HOST"], "localhost") !== false)
	{
		$output = $val;			
	}	
	return $output;
}

/**
 *
 * googleアドセンス内容取得
 *
 */function get_the_content_adsbygoogle() {
	$url = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- res -->
	<ins class="adsbygoogle"
		 style="display:block"
		 data-ad-client="ca-pub-1780702416565463"
		 data-ad-slot="9275678635"
		 data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>';

	return sprintf('
	<table cellpadding="3" class="tablecss01 ori" style"display:block;">
		<tr><td style="padding:5px;">%s</td></tr>
	</table>'
	,localval($url, $val)
	);
} 
