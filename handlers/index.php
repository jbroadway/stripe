<?php

/**
 * This is the default admin area.
 */

$this->require_acl ('admin', 'stripe');

$page->title = __ ('Stripe Payments');
$page->layout = 'admin';

$limit = 20;
$num = isset ($_GET['offset']) ? $_GET['offset'] : 1;
$offset = ($num - 1) * $limit;

$list = stripe\Payment::query ()
	->order ('ts desc')
	->fetch_orig ($limit, $offset);
$count = stripe\Payment::query ()->count ();

// Fetch list of usernames in one query
$ids = array_map (function ($row) { return $row->user_id; }, $list);
$ids = array_unique ($ids);

$names = User::query ('id, name')
	->where ('id in(' . join (', ', $ids) . ')')
	->fetch_assoc ('id', 'name');

foreach ($list as $k => $row) {
	$list[$k]->user_name = $names[$row->user_id];
}

// Render the list
echo $tpl->render (
	'stripe/index',
	array (
		'limit' => $limit,
		'total' => $count,
		'list' => $list,
		'count' => count ($list),
		'url' => '/stripe/index?offset=%d'
	)
);

?>