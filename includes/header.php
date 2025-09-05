<?php require_once __DIR__.'/db_connect.php'; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Professional HMS</title><script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="/hms-pro/assets/css/styles.css"></head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
<nav class="bg-indigo-600/90 backdrop-blur text-white"><div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
  <a class="font-bold">🏥 Professional HMS</a>
  <div class="space-x-4 text-sm"><?php if(!empty($_SESSION['user'])): ?>
    <span class="opacity-90">Hi, <?= e($_SESSION['user']['username']) ?> (<?= e($_SESSION['user']['role']) ?>)</span>
    <a class="underline" href="/hms-pro/logout.php">Logout</a><?php endif; ?>
  </div></div></nav><main class="max-w-6xl mx-auto px-4 py-6">