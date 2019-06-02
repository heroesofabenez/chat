Chat
==============

Simple chat component for Nette applications.

Links
-----

Primary repository: https://gitlab.com/heroesofabenez/chat
Github repository: https://github.com/heroesofabenez/chat
Packagist: https://packagist.org/packages/heroesofabenez/chat

Installation
------------
The best way to install it is via Composer. Just add **heroesofabenez/chat** to your dependencies.

Overview
--------

This package helps you with creating chats for your Nette application. It contains abstract component with basic template, data structures for message and person and DI extension which with minimal configuration registers and configure all the services for you. You just need to write some database logic.

Usage
-----

### Chat controls and factories

Firstly, you need to create a component (and factory for it) for every chat you want in application. Do not worry, you will not end up with zillion classes for every possible room because this package works with types of chat. E. g. you can have some global chat for everyone and group chat for a group of users. In this situation you will define only 2 types of chat (group of user will probably be saved as a field in table of users). So for the group chat you will need this:

```php
<?php
declare(strict_types=1);

namespace App\Chat;

use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\IDatabaseAdapter;

class GroupChatControl extends ChatControl {
  public function __construct(IDatabaseAdapter $databaseAdapter, \Nette\Security\User $user) {
    $groupId = $user->identity->group;
    parent::__construct($databaseAdapter, "group", $groupId);
  }
}

interface IGroupChatControlFactory {
  public function create(): GroupChatControl;
}
?>
```

With this code, we have created group chat. It isn't much of code, is it? Let's examine it closely now.

We create a new class which extends the abstract component from this package. In your own chats you usually need to define just constructor, the base class handles all remaining logic. The constructor of the base class requires database adapter (it will be described later), names and values for fields which identify texts and people for this chat (if both are identified by same field and value, you pass one pair and then nulls).

The factory for component is pretty straightforward: an interface with method create.

### Database adapter

Next step is creating a database adapter. It is responsible for getting list of messages and people for current chat and saving new messages. The adapter has to implement **HeroesofAbenez\Chat\IDatabaseAdapter** interface. It consists of 3 methods (1 for each task). Methods **getTexts** and **getCharacters** has to return **HeroesofAbenez\Chat\ChatMessagesCollection** or **HeroesofAbenez\Chat\ChatCharactersCollection** which are collections of messages/people in the current chat. Example for Nextras ORM:

```php
<?php
declare(strict_types=1);

namespace App\Chat;

use App\Orm\Model as ORM;
use App\Orm\ChatMessage as ChatMessageEntity;
use HeroesofAbenez\Chat\IDatabaseAdapter;
use HeroesofAbenez\Chat\ChatMessagesCollection;
use HeroesofAbenez\Chat\ChatMessage;
use HeroesofAbenez\Chat\ChatCharactersCollection;
use HeroesofAbenez\Chat\ChatCharacter;

/**
 * NextrasOrmAdapter
 *
 * @author Jakub Konečný
 */
final class NextrasOrmAdapter implements IDatabaseAdapter {
  /** @var ORM */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(ORM $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  public function getTexts(string $column, $value, int $limit): ChatMessagesCollection {
    $count = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->countStored();
    $paginator = new \Nette\Utils\Paginator();
    $paginator->setItemCount($count);
    $paginator->setItemsPerPage($limit);
    $paginator->setPage($paginator->pageCount);
    $messages = $this->orm->chatMessages->findBy([
      $column => $value,
    ])->limitBy($paginator->length, $paginator->offset);
    $collection = new ChatMessagesCollection();
    foreach($messages as $message) {
      $character = new ChatCharacter($message->user->id, $message->user->name);
      $collection[] = new ChatMessage($message->id, $message->message, $message->when, $character);
    }
    return $collection;
  }
  
  public function getCharacters(string $column, $value): ChatCharactersCollection {
    $characters = $this->orm->users->findBy([
      $column => $value, "lastActive>=" => time() - 60 * 5
    ]);
    $collection = new ChatCharactersCollection();
    foreach($characters as $character) {
      $collection[] = new ChatCharacter($character->id, $character->name);
    }
    return $collection;
  }
  
  public function addMessage(string $message, string $filterColumn, int $filterValue): void {
    $chatMessage = new ChatMessageEntity();
    $chatMessage->message = $message;
    $this->orm->chatMessages->attach($chatMessage);
    $chatMessage->user = $this->user->id;
    $chatMessage->{$filterColumn} = $filterValue;
    $this->orm->chatMessages->persistAndFlush($chatMessage);
  }
}
?>
```

### Nette DI extension

Everything is put together with Nette DI extension. Minimal working example:

```yaml
extensions:
    chat: HeroesofAbenez\Chat\DI\ChatExtension

chat:
    databaseAdapter: App\Chat\NextrasOrmAdapter
    chats:
        group: App\Chat\ITownChatControlFactory
```

The extension necessary needs only name of class for database adapter and factories for each chat type.

### Presenter and template

Now, all is left to do is adding the component to your presenter and template.

```php
<?php
declare(strict_types=1);

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;

class ChatPresenter extends Presenter {
  /** @var \App\Chat\IGroupChatControlFactory @inject */
  public $groupChatFactory;
  /** @var \HeroesofAbenez\Chat\NewChatMessageFormFactory @inject */
  public $newMessageFormFactory;
  
  protected function createComponentGroupChat() {
    return $this->groupChatFactory->create();
  }
  
  protected function createComponentNewChatMessageForm(): Form {
    /** @var \App\Chat\GroupChatControl $chat */
    $chat = $this->groupChatFactory->create();
    return $this->newMessageFormFactory->create($chat);
  }
}
?>
```

```latte
{control groupChat}
{control newChatMessageForm}
```

Advanced usage
--------------

### Custom template

By default, the chats use a very basic template with no style. So if you want to change it, just set property **templateFile** in your chat control.

### Links to people's profiles

The chat contains list people in current room at the very top of page. But it does not have to be a plain list. You can make every name a link to that person's profile (if your application has user profiles). You can do that in the extension's configuration:

```yaml
chat:
    characterProfileLink: ":Module:Presenter:action"
```

. The person's id will be passed as parameter to that presenter action. Do note that this is used in a component so the link has to start with :.

Alternatively, you can set property **characterProfileLink** in the chat control.

### Chat message processors

Before a new message is saved to database, you are able to examine it and decide that it should not be saved and other some other action should be done. E. g. if it contains some forbidden words, you show a warning to the user and don't save their message.

This can be done via a chat message processor. The processor has to implement **HeroesofAbenez\Chat\IChatMessageProcessor** interface. It contains just 1 method **parse** which takes the message as parameter and return a string which will be shown to the user or null if the processor is not applicable.

Processors are added via DI extension to all chat types.

```yaml
chat:
    messageProcessors:
        commandsProcessor: HeroesofAbenez\Chat\ChatCommandsProcessor
```

You can also manually them to a chat control via method **addMessageProcessor**.

As an example see the bundled **ChatCommandsProcessor**.
