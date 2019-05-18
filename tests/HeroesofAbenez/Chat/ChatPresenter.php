<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Nette\Application\UI\Form;

final class ChatPresenter extends \Nette\Application\UI\Presenter {
  /** @var IExampleChatControlFactory @inject */
  public $chatFactory;
  /** @var NewChatMessageFormFactory @inject */
  public $newChatMessageFormFactory;

  public function formatTemplateFiles(): array {
    return [__DIR__ . "/chat.latte"];
  }

  protected function createComponentNewMessageForm(): Form {
    return $this->newChatMessageFormFactory->create($this->chatFactory->create());
  }
}
?>