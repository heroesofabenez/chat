<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * Basic Chat Control
 *
 * @author Jakub Konečný
 * @property int $messagesPerPage
 * @property string $characterProfileLink
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class ChatControl extends \Nette\Application\UI\Control {
  protected IDatabaseAdapter $database;
  /** @var IChatMessageProcessor[] */
  protected array $messageProcessors = [];
  protected string $textColumn;
  protected string $characterColumn;
  protected int $textValue;
  /** @var int */
  protected $characterValue;
  protected string $characterProfileLink = "";
  protected string $templateFile = __DIR__ . "/chat.latte";
  protected int $messagesPerPage = 25;

  /**
   * @param mixed $characterValue
   */
  public function __construct(IDatabaseAdapter $databaseAdapter, string $textColumn, int $textValue, string $characterColumn = null, $characterValue = null) {
    $this->database = $databaseAdapter;
    $this->textColumn = $textColumn;
    $this->characterColumn = $characterColumn ?? $textColumn;
    $this->textValue = $textValue;
    $this->characterValue = $characterValue ?? $textValue;
  }

  public function getMessagesPerPage(): int {
    return $this->messagesPerPage;
  }
  
  public function setMessagesPerPage(int $messagesPerPage): void {
    if($messagesPerPage < 0) {
      $messagesPerPage = 0;
    }
    $this->messagesPerPage = $messagesPerPage;
  }
  
  public function getCharacterProfileLink(): string {
    return $this->characterProfileLink;
  }
  
  public function setCharacterProfileLink(string $characterProfileLink): void {
    $this->characterProfileLink = $characterProfileLink;
  }
  
  public function addMessageProcessor(IChatMessageProcessor $processor): void {
    $this->messageProcessors[] = $processor;
  }
  
  /**
   * Gets texts for the current chat
   */
  public function getTexts(): ChatMessagesCollection {
    return $this->database->getTexts($this->textColumn, $this->textValue, $this->messagesPerPage);
  }
  
  /**
   * Gets characters in the current chat
   */
  public function getCharacters(): ChatCharactersCollection {
    return $this->database->getCharacters($this->characterColumn, $this->characterValue);
  }

  /**
   * Renders the chat
   */
  public function render(): void {
    $this->template->setFile($this->templateFile);
    $this->template->characters = $this->getCharacters();
    $this->template->texts = $this->getTexts();
    $this->template->characterProfileLink = $this->characterProfileLink;
    $this->template->render();
  }
  
  protected function processMessage(string $message): ?string {
    foreach($this->messageProcessors as $processor) {
      $result = $processor->parse($message);
      if(is_string($result)) {
        return $result;
      }
    }
    return null;
  }
  
  /**
   * Submits new message
   */
  public function newMessage(string $message): void {
    $result = $this->processMessage($message);
    if(!is_null($result)) {
      $this->presenter->flashMessage($result);
    } else {
      $this->database->addMessage($message, $this->textColumn, $this->textValue);
    }
    $this->presenter->redirect("this");
  }
}
?>