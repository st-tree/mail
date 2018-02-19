<?php
declare(strict_types=1);

namespace Genkgo\TestMail\Unit\Protocol\Imap\MessageData;

use Genkgo\Mail\Protocol\Imap\MessageData\Item\NameItem;
use Genkgo\Mail\Protocol\Imap\MessageData\ItemList;
use Genkgo\TestMail\AbstractTestCase;

final class ItemListTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_is_immutable()
    {
        $list = new ItemList();

        $this->assertNotSame($list, $list->withItem(new NameItem('TEST')));
        $this->assertNotSame($list, $list->withOctet(10));
        $this->assertNotSame($list, $list->withBody('test'));
    }

    /**
     * @test
     */
    public function it_stores_items_by_name()
    {
        $item = new NameItem('TEST');
        $list = new ItemList();
        $list = $list->withItem($item);

        $this->assertSame($item, $list->getItem('TEST'));
    }

    /**
     * @test
     */
    public function it_stores_last_item()
    {
        $list = new ItemList();

        $item1 = new NameItem('TEST');
        $list = $list->withItem($item1);
        $this->assertSame($item1, $list->last());

        $item2 = new NameItem('OTHER');
        $list = $list->withItem($item2);
        $this->assertSame($item2, $list->last());
    }

    /**
     * @test
     */
    public function it_throw_when_empty_list()
    {
        $this->expectException(\OutOfBoundsException::class);

        $list = new ItemList();
        $list->last();
    }

    /**
     * @test
     */
    public function it_casts_to_string_with_item()
    {
        $list = new ItemList();
        $list = $list->withItem(new NameItem('TEST'));

        $this->assertSame('TEST', (string)$list);
    }

    /**
     * @test
     */
    public function it_casts_to_string_with_item_octet()
    {
        $list = new ItemList();
        $list = $list->withItem(new NameItem('TEST'));
        $list = $list->withOctet(100);

        $this->assertSame('TEST {100}', (string)$list);
    }

    /**
     * @test
     */
    public function it_casts_to_string_with_item_octet_body()
    {
        $list = new ItemList();
        $list = $list->withItem(new NameItem('TEST'));
        $list = $list->withOctet(100);
        $list = $list->withBody('Hello World.');

        $this->assertSame("(TEST {100}\nHello World.)", (string)$list);
    }

    /**
     * @test
     */
    public function it_parses_string_to_list()
    {
        $list = ItemList::fromString("(TEST[HEADER] {100}\nHello World.)");

        $this->assertSame('TEST[HEADER]', (string)$list->getItem('TEST'));
        $this->assertSame('Hello World.', (string)$list->getBody());
    }

    /**
     * @test
     */
    public function it_throws_when_parsing_empty_string()
    {
        $this->expectException(\InvalidArgumentException::class);
        ItemList::fromString('');
    }

}