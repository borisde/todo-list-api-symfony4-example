<?php


namespace App\DataFixtures;

use App\Entity\TodoItem;
use App\Entity\TodoList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ListFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $list = new TodoList();
        $list->setTitle('First lists');
        $manager->persist($list);

        $item = new TodoItem();
        $item->setDescription('Item 1');
        $item->setList($list);
        $manager->persist($item);

        $item = new TodoItem();
        $item->setDescription('Some item 2');
        $item->setList($list);
        $manager->persist($item);

        $item = new TodoItem();
        $item->setDescription('New Item 3');
        $item->setList($list);
        $manager->persist($item);

        $list = new TodoList();
        $list->setTitle('Second lists');
        $manager->persist($list);

        $item = new TodoItem();
        $item->setDescription('Lists some');
        $item->setList($list);
        $manager->persist($item);

        $item = new TodoItem();
        $item->setDescription('Just2');
        $item->setList($list);
        $manager->persist($item);

        $list = new TodoList();
        $list->setTitle('Another lists');
        $manager->persist($list);

        $item = new TodoItem();
        $item->setDescription('Olly!!!');
        $item->setList($list);
        $manager->persist($item);

        $list = new TodoList();
        $list->setTitle('Last list');
        $manager->persist($list);

        $manager->flush();
    }
}