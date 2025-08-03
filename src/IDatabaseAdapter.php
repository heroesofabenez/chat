<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

if(false) {
  /** @deprecated use DatabaseAdapter */
  interface IDatabaseAdapter extends DatabaseAdapter {
  }
} else {
  class_alias(DatabaseAdapter::class, IDatabaseAdapter::class);
}
?>