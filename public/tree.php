<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 23.11.16
 * Time: 19:14
 */

$pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$stmt = $pdo->prepare('SELECT (SELECT parent_category_id FROM category WHERE id=c.parent_category_id) id, (SELECT name FROM category WHERE id=c.parent_category_id) parent, GROUP_CONCAT(c.id) ids, GROUP_CONCAT(c.name) path FROM `category` c WHERE c.parent_category_id>0 GROUP BY c.parent_category_id');
$stmt->execute();
$rows = $stmt->fetchAll();

$tree = [];

foreach ($rows as $row) {
    if (!$row->id) {
        $tree[0] = [];
        $tree[0]['text'] = $row->parent;
        fillLevel($tree[0], $row);
    } else {
        searchLevel($tree[0], $row);
    }
}

function searchLevel(&$level, $data)
{
    if (isset($level['nodes'])) {
        return searchLevel($level['nodes'], $data);
    }
    foreach ($level as &$elem) {
        if (isset($elem['nodes'])) {
            searchLevel($elem['nodes'], $data);
        }
        if ($elem['text'] == $data->parent) {
            fillLevel($elem, $data);
        }
    }
}

function fillLevel(&$level, $data)
{
    $names = explode(',', $data->path);
    $level['text'] = $data->parent;
    $level['nodes'] =
        array_map(
            function ($item) {
                return ['text' => $item];
            },
            $names
        );
}

header('Content-Type: text/json');
die(json_encode($tree));
