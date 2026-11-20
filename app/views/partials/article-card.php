<?php /** @var array $a published article row with cat_name */ ?>
<li class="article-card">
  <div class="media"><img src="<?= e(Article::coverUrl($a['cover_path'])) ?>" alt="" width="1600" height="1067" loading="lazy"></div>
  <h3><a href="<?= e(url('/edukasi/' . $a['slug'])) ?>"><?= e($a['title']) ?></a></h3>
  <p><?= e($a['excerpt']) ?></p>
  <p class="article-meta"><span class="cat"><?= e($a['cat_name']) ?></span><span><?= (int) $a['read_minutes'] ?> menit baca</span></p>
</li>
