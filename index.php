<?php
	include('config.php');
	$page = !empty($_GET['p']) ? $_GET['p'] : 1;
	$query = !empty($_GET['q']) ? strtolower($_GET['q']) : '';
	$query = !empty($_GET['s']) ? strtolower($_GET['s']) : $query;
	$page_title = !empty($query) ? ucwords(implode(' ', explode('-', $query))) : $site_title;
	if (!empty($query))
		$page_limit = 2000;
	$output = $og_image = '';
	$images = [];
	$images = array_merge(glob('*.jpg'), $images);
	$images = array_merge(glob('*.png'), $images);
	$images = array_merge(glob('*.gif'), $images);
	if ($sort_by == 'time') {
		usort($images, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
	}
	$image_names = implode("\n", $images) . "\n";
	$fp = fopen('image_name_cache.txt', 'w');
	fwrite($fp, $image_names);
	$images = file('image_name_cache.txt', FILE_SKIP_EMPTY_LINES);
	if ($sort_by == 'title') arsort($images);
	$display_count = 0;
	foreach (array_slice($images, ($page-1)*$page_limit, $page_limit) as $image) {
		if ($display_count >= $page_limit) break;
		if (!empty($query)) {
			if (strpos(strtolower($image), $query) === false) continue;
		}
		$display_count += 1;
		foreach (['.jpg', '.png', '.gif'] as $ext) {
			if (strpos($image, $ext)) {
				$slug = str_replace($ext, '', $image);
			}
		}
		$output .= '<div><h3>';
		$output .= '<a href="' . $site_root . '?s=' . $slug . '">' . ucwords(implode(' ', explode('-', $slug))) . '</a><br />';
		$output .= '</h3><a href="' . $site_root . '?s=' . $slug . '"><img width="60%" src="' . $image . '"/></a></div><hr>';
		$og_image = $image;
	}
	if (empty($query)) {
		if ($page - 1 > 0) {
			$output .= '<a href="' . $site_root . '?p=' . ($page - 1) . '">Prev</a> -- ';
		}
		$page_total = ceil(count($images) / $page_limit);
		$output .=  "$page of $page_total pages";
		if ($page + 1 <= $page_total) {
			$output .= ' -- <a href="' . $site_root . '?p=' . ($page + 1) . '">Next</a>';
		}
	}
?>

<head>
	<meta name="twitter:card" content="photo" />
	<meta name="twitter:image:src" content="<?=$site_root.str_replace("\n", '', $og_image)?>" />
	<meta name="twitter:title" content="<?=$page_title?>" />
	<meta name="twitter:description" content="<?=$site_description?>" />
	<meta property="og:image" content="<?=$site_root.str_replace("\n", '', $og_image)?>" />
	<meta property="og:title" content="<?=$page_title?>" />
	<meta property="og:description" content="<?=$site_description?>" />
	<meta property="description" content="<?=$site_description?>" />
	<title><?=$page_title?></title>
	<style>
		body {margin: 5% auto; color: #444444; font-family: 'Courier New'; font-size: 14px; line-height: 1.6; text-shadow: 0 1px 0 #ffffff; max-width: 73%;}
		code {background: white;}
		a {border-bottom: 1px solid #444444; color: #444444; text-decoration: none;}
		a:hover {border-bottom: 0;}
		h1, h2 {line-height: 1.3;}
	</style>
</head>
<body>
<h1><a href="<?=$site_root?>"><?=$site_title?></a></h1>
<h2<?=$page_title?></h2>

<form action="." method="get">
<input class="text" type="text" name="q" />
<input class="btn" type="submit" value="search" />
</form><br>
<p><?=$output?></p>
<p><?=$footer?></p>
</body>
