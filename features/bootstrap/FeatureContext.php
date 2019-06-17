<?php

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Entity\TodoList;
use App\Entity\TodoItem;

/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    use KernelDictionary;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|object
     */
    private $em;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * Truncates all tables even if foreign keys exists
     *
     * @BeforeScenario
     */
    public function clearData()
    {

        $this->em->getConnection()->prepare("SET FOREIGN_KEY_CHECKS = 0;")->execute();

        $tableNames = $this->em->getConnection()->getSchemaManager()->listTableNames();
        foreach ($tableNames as $tableName) {
            $sql = 'TRUNCATE TABLE ' . $tableName;
            $this->em->getConnection()->prepare($sql)->execute();
        }

        $this->em->getConnection()->prepare("SET FOREIGN_KEY_CHECKS = 1;")->execute();
    }

    /**
     * @Given there are :arg1 Lists with :arg2 Items each
     */
    public function thereAreListsWithItemsEach(int $listsCount, int $itemsCount)
    {
        for ($i = 0; $i < $listsCount; $i++) {
            $list = new TodoList();
            $list->setTitle('List '.($i+1));

            $this->em->persist($list);

            for ($j = 0; $j < $itemsCount; $j++) {
                $item = new TodoItem();
                $item->setDescription($list->getTitle().' Item '.($j+1));
                $item->setList($list);

                $this->em->persist($item);
            }
        }

        $this->em->flush();
    }
}

