## Test Task
### ToDo-list REST API, built with Symfony 4 framework and FosRestBundle.

There have to be the following possibilities:

1) Create a ToDo list

2) View particular ToDo list

3) List all ToDo lists

Every ToDo can have a list of items. There has to be the possibility to add items to ToDo list, remove items from ToDo list. But it is not needed to update items in ToDo list.

Also, it is not needed to update ToDo list itself (title for example) - just create, view and list all ToDo lists.

But in addition we need to implement the searching feature:

4) Search in all ToDo lists items. For example if there are two ToDos: 
   
   **First ToDo** (`items: Some item, Another item, Third Item`).  
   **Second Important ToDo** (`items: Text, Another item name, Some more item`).    
   If I'm searching for `some` I have to get both ToDos in search results, as in each ToDo list exist Item with word `some` (`Some item` in First ToDo and `Some more item` in Second ToDo).


## References

+ [Symfony](https://github.com/symfony)
+ [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
+ [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle)
+ [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)
+ [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)
+ [SensioFrameworkExtraBundle](https://github.com/sensiolabs/SensioFrameworkExtraBundle)
+ [orm-pack](https://github.com/symfony/orm-pack)
+ [Behat](https://github.com/Behat/Behat)
+ [Behat API Extension](https://github.com/imbo/behat-api-extension)

## License

This work is under MIT license.
