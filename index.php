<?php

require_once __DIR__ . '/amo.php';

$leads = amo_getLastLeads();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>amgroup kb</title>
</head>
<body>
<h2>Последние 10 сделок</h2>
<ul>
<? foreach ($leads['_embedded']['leads'] as $lead): ?>
    <li>Сделка #<?= $lead['id'] ?>: <?= $lead['name'] ?> (<?= $lead['price'] ?> ₽)</li>
<? endforeach ?>
</ul>
<h2>Ответ API</h2>
<pre>
    <?= json_encode($leads, JSON_PRETTY_PRINT) ?>
</pre>
</body>
</html>
