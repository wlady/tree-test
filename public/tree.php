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

$stmt = $pdo->prepare('SELECT parent_category_id id, (SELECT name FROM category WHERE id=c.parent_category_id) parent, GROUP_CONCAT(c.id) children, GROUP_CONCAT(c.name) path FROM category c WHERE c.parent_category_id>0 GROUP BY c.parent_category_id');
$stmt->execute();
$rows = $stmt->fetchAll();

$tree = [
    'id' => 0,
    'text' => 'Каталог',
    'nodes' => [],
];

foreach ($rows as $key => $row) {
    if ($key) {
        searchLevel($tree[0], $row);
    } else {
        fillLevel($tree[0], $row);
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
    $children = explode(',', $data->children);
    $names = explode(',', $data->path);
    $level = [
        'id' => $data->id,
        'text' => $data->parent,
        'nodes' => array_map(
            function ($id, $name) {
                return [
                    'id' => $id,
                    'text' => $name,
                ];
            },
            $children,
            $names
        )
    ];
}

header('Content-Type: text/json');
die(json_encode($tree));
