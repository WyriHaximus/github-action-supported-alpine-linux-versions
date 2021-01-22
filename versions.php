<?php

$versions = [];

$now = new \DateTimeImmutable();

$d = new DOMDocument();
@$d->loadHTML(file_get_contents('https://alpinelinux.org/releases/'));

/** @var DOMNode $row */
foreach ((new DOMXPath($d))->query('//tbody/tr') as $row) {
    if (trim(@$row->childNodes->item(1)->textContent) === 'Branch') {
        continue;
    }

    if (trim(@$row->childNodes->item(1)->textContent) === 'edge') {
        continue;
    }
    $eol = new \DateTimeImmutable(trim($row->childNodes->item(9)->textContent));
    $version = substr(trim($row->childNodes->item(1)->textContent), 1);
    if ($eol < $now) {
        continue;
    }

    $versions[] = $version;
}

echo 'Found the following versions: ', implode(', ', $versions), PHP_EOL;
echo '::set-output name=versions::', json_encode($versions), PHP_EOL;
