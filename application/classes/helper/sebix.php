<?php defined('SYSPATH') OR die('No direct access allowed.');

class Helper_Sebix {

	/**
	 * Return a HTML-Link with the title-Attribute
	 *
	 * @param   string  URL
	 * @param   string  text to show
	 * @param   string  title for hover
	 * @return  string  HTML-Link
	 */
	static function anchor ($link, $text = '', $title = FALSE) {
		return (html::anchor(Kohana::$base_url . $link, $text, array('title' => ($title ? $title : $text))));
	}

	/**
	 * Redirect function
	 *
	 * @param   string   location
	 * @param   string   location or refresh
	 * @param   int	     respone code (302)
	 * @return  null
	 */
	static function redirect ($uri = '', $method = 'location', $http_response_code = 302) {
		if ( ! preg_match('#^https?://#i', $uri)) {
			$uri = url::site($uri);
		}
		
		switch($method)	{
			case 'refresh': header("Refresh:0;url=".$uri);
			break;
			default: header("Location: ".$uri, TRUE, $http_response_code);
			break;
		}
		
		die ('<html><head><title>Weiterleitung</title>
		<meta http-equiv="refresh" content="0;url=' . $uri . '">
		</head><body>
		<h1>Weiterleitung!</h1>
		<p>Dein Browser scheint Weiterleitungen zu ignorieren!<br />' . self::anchor($uri, 'Zur gewünschten Seite', 'Gehe zur geünschten Seite') .
		'</p></body></html>');

	}

	/**
	 * Decocde BBCode in Post
	 *
	 * @param   string  text to encode
	 * @return  string  encoded text
	
	static function bb ($text) {
		return str_replace(array(
					'[h1]',
					'[h2]',
					'[h3]',
					'[/h1]',
					'[/h2]',
					'[/h3]',
					'[br]',
					'[b]',
					'[/b]',
					'[i]',
					'[/i]',
					'[pre]',
					'[/pre]',
					'[p]',
					'[code]',
					'[/code]',
					'[term]',
					'[/term]',
					'[quote]',
					'[/quote]'),
				array(
					'</p><h4>',
					'</p><h5>',
					'</p><h6>',
					'</h4><p>',
					'</h5><p>',
					'</h6><p>',
					'<br />',
					'<strong>',
					'</strong>',
					'<em>',
					'</em>',
					'</p><pre>',
					'</pre><p>',
					'</p><p>',
					'</p><code>',
					'</code><p>',
					'</p><div class="term">',
					'</div><p>',
					'</p><blockquote>',
					'</blockquote><p>'),
				$text);
	}
	
	/**
	 * Returns string with the full tagcloud
	 *
	 * @param  int		maximum of values (best of course)
	 * @return string	tagcloud, ordered alphabetically
	 */
	static function tagcloud ($max = 0) {
		$db = Database::instance();
		
		$tags_db = DB::query(Database::SELECT, 'SELECT `ETAGS` AS `tags` FROM `entries`')->execute()->as_array();
		
		$tags = array();
		foreach ($tags_db as $val) {
			foreach (explode(',',$val['tags']) as $value) {
				if (isset($tags[$value]))
					$tags[$value]++;
				else
					$tags[$value] = 1;
			}
		}

		ksort($tags);
		$tmp = $tags;
		arsort($tmp);
		$tmp = array_keys($tmp);

		$highest = 40;	// highest font-size
		$tag_values = array_values($tags);
		$tag_keys = array_keys($tags);
		$high = $tags[$tmp[0]];	// highest existence

		$cloud = '';

		for ($i = 0; $i < ( ($max != 0)?$max:count($tags) ); $i++) {
			$size = floor($tag_values[$i]/$high*$highest);	// calculate the size
			$cloud .= '<a style="font-size:' . $size . 'px" href="/tag/' . $tag_keys[$i] . '" title="Der Tag ' . $tag_keys[$i] . '">' . $tag_keys[$i] . '</a> ';
		}
		
		return $cloud;
	}

} // End arr
