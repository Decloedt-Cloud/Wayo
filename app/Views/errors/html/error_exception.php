<?php
/**
 * CI4 Error Exception View (Development Mode)
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title><?= esc($title) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        .header {
            background: #c0392b;
            color: #fff;
            padding: 1.5rem 2rem;
            margin-bottom: 1rem;
            border-radius: 6px;
        }
        .header h1 { font-size: 1.4rem; font-weight: 600; }
        .header p { margin-top: .5rem; opacity: 0.9; font-size: 1rem; }
        .source {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .source h2 { font-size: 1rem; color: #555; margin-bottom: .5rem; }
        .source .file { color: #888; font-size: 0.85rem; }
        .trace {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .trace h2 { font-size: 1rem; color: #555; margin-bottom: 1rem; }
        .trace-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 0.85rem;
        }
        .trace-item:last-child { border-bottom: none; }
        .trace-file { color: #2980b9; }
        .trace-line { color: #e74c3c; font-weight: bold; }
        .trace-function { color: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= esc(get_class($exception)) ?></h1>
            <p><?= nl2br(esc($exception->getMessage())) ?></p>
        </div>

        <div class="source">
            <h2>Source</h2>
            <p class="file">
                <?= esc($exception->getFile()) ?> : <strong><?= $exception->getLine() ?></strong>
            </p>
        </div>

        <div class="trace">
            <h2>Backtrace</h2>
            <?php foreach ($exception->getTrace() as $i => $row): ?>
                <div class="trace-item">
                    <span>#<?= $i ?></span>
                    <?php if (isset($row['file'])): ?>
                        <span class="trace-file"><?= esc($row['file']) ?></span>
                        : <span class="trace-line"><?= $row['line'] ?? '?' ?></span>
                    <?php else: ?>
                        <span class="trace-file">[internal]</span>
                    <?php endif; ?>
                    &mdash;
                    <span class="trace-function">
                        <?= isset($row['class']) ? esc($row['class'] . $row['type']) : '' ?><?= esc($row['function'] ?? '') ?>()
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
