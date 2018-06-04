<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Nette\Localization\ITranslator,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\NeonLoader;

/**
 * Basic Chat Control
 *
 * @author Jakub Konečný
 * @property ITranslator $translator
 * @property string $lang
 * @property int $messagesPerPage
 * @property string $characterProfileLink
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class ChatControl extends \Nette\Application\UI\Control {
  /** @var IDatabaseAdapter */
  protected $database;
  /** @var ITranslator|Translator|null */
  protected $translator;
  /** @var string */
  protected $lang = "";
  /** @var IChatMessageProcessor[] */
  protected $messageProcessors = [];
  /** @var string*/
  protected $textColumn;
  /** @var string */
  protected $characterColumn;
  /** @var int*/
  protected $textValue;
  /** @var int */
  protected $characterValue;
  /** @var string */
  protected $characterProfileLink = "";
  /** @var string */
  protected $templateFile = __DIR__ . "/chat.latte";
  /** @var int */
  protected $messagesPerPage = 25;
  
  public function __construct(IDatabaseAdapter $databaseAdapter, string $textColumn, int $textValue, string $characterColumn = null, $characterValue = null, ITranslator $translator = null) {
    parent::__construct();
    $this->database = $databaseAdapter;
    $this->translator = $translator;
    $this->textColumn = $textColumn;
    $this->characterColumn = $characterColumn ?? $textColumn;
    $this->textValue = $textValue;
    $this->characterValue = $characterValue ?? $textValue;
  }
  
  public function getTranslator(): ?ITranslator {
    return $this->translator;
  }
  
  public function setTranslator(ITranslator $translator): void {
    $this->translator = $translator;
  }
  
  public function getLang(): string {
    return $this->lang;
  }
  
  public function setLang(string $lang): void {
    $this->lang = $lang;
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
  
  protected function setupTranslator(): void {
    if(is_null($this->translator)) {
      $loader = new NeonLoader();
      $loader->folders = [__DIR__ . "/lang"];
      $this->translator = new Translator($loader);
    }
    if($this->lang !== "") {
      $this->translator->lang = $this->lang;
    }
    $this->template->setTranslator($this->translator);
  }
  
  /**
   * Renders the chat
   */
  public function render(): void {
    $this->setupTranslator();
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