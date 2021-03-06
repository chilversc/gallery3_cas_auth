<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-error">
  <h1>
    <?= t("Dang...  Page not found!") ?>
  </h1>
  <? if ($is_guest): ?>
    <h2>
      <?= t("Hey wait, you're not signed in yet!") ?>
    </h2>
    <p>
       <?= t("Maybe the page exists, but is only visible to authorized users.") ?>
       <a href="<?= url::site("cas/login") ?>"><?= t("Please sign in to find out.") ?></a>
    </p>
  <? else: ?>
    <p>
      <?= t("Maybe the page exists, but is only visible to authorized users.") ?>
      <?= t("If you think this is an error, talk to your Gallery administrator!") ?>
    </p>
 <? endif; ?>
</div>
