<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;

/**
 * NewChatMessageFormFactory
 *
 * @author Jakub Konečný
 */
final class NewChatMessageFormFactory {
  /** @var ITranslator */
  protected $translator;
  
  public function __construct(ITranslator $translator) {
    $this->translator = $translator;
  }
  
  public function create(ChatControl $chatControl): Form {
    $form = new Form();
    $form->setTranslator($this->translator);
    $form->addText("message", "")
      ->setRequired("chat.newMessageForm.messageField.empty");
    $form->addSubmit("send", "chat.newMessageForm.submitButton.label");
    $form->addComponent($chatControl, "chat");
    $form->onSuccess[] = function(Form $form, array $values): void {
      /** @var ChatControl $chat */
      $chat = $form->getComponent("chat");
      $chat->newMessage($values["message"]);
    };
    return $form;
  }
}
?>