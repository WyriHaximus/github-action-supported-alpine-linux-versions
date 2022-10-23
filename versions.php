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
    $eol = \DateTimeImmutable::createFromFormat('Y-m-d', trim(explode(' ', $row->childNodes->item(9)->textContent)[1]));
    $version = substr(trim($row->childNodes->item(1)->textContent), 1);
    if ($eol < $now) {
        continue;
    }

    $versions[] = $version;
}

$filteredVersion = [];
for ($i = 0; $i < (int)(getenv('INPUT_MAXVERSIONS') ?: count($versions)); $i++) {
    $filteredVersion[] = $versions[$i];
}

echo 'Found the following versions: ', implode(', ', $filteredVersion), PHP_EOL;
file_put_contents(getenv('GITHUB_OUTPUT'), 'versions=' . json_encode($versions) . PHP_EOL, FILE_APPEND);
