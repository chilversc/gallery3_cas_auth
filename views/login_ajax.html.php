<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-login">
  <h2>
    <?= t("You're currently not signed in.") ?>
  </h2>
  <p>
     <?= t("Maybe the page exists, but is only visible to authorized users.") ?>
     <a href="<?= url::site("cas/login") ?>"><?= t("Please sign in to find out.") ?></a>
  </p>
</div>
