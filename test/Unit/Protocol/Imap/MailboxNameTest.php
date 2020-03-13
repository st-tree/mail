<?php
declare(strict_types=1);

namespace Genkgo\TestMail\Unit\Protocol\Imap;

use Genkgo\Mail\Protocol\Imap\MailboxName;
use Genkgo\TestMail\AbstractTestCase;

final class MailboxNameTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_accepts_default_names(): void
    {
        new MailboxName('INBOX');
        new MailboxName('INBOX.Sent');
        new MailboxName('ARCHIVE');
        new MailboxName('ARCHIVE2018');
        $this->addToAssertionCount(4);
    }

    /**
     * @test
     */
    public function it_accepts_spaces_if_quoted(): void
    {
        new MailboxName('"Archive 2018"');
        new MailboxName('INBOX."Archive 2018"');
        $this->addToAssertionCount(2);
    }

    /**
     * @test
     */
    public function it_does_not_allow_unquoted_space(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('Archive 2018');
    }

    /**
     * @test
     */
    public function it_does_not_allow_unquoted_wildcard_percentage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('Archive%2018');
    }

    /**
     * @test
     */
    public function it_does_not_allow_unquoted_wildcard_asterisk(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('Archive*2018');
    }

    /**
     * @test
     */
    public function it_allows_quoted_wildcard_asterisk(): void
    {
        new MailboxName('"Archive*2018"');
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function it_can_be_casted_to_string(): void
    {
        $this->assertSame('"Archive*2018"', (string)new MailboxName('"Archive*2018"'));
    }

    /**
     * @test
     */
    public function it_throws_an_empty_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('');
    }

    /**
     * @test
     */
    public function it_throws_when_unfinished_literal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('Archive."Archive 2018');
    }

    /**
     * @test
     */
    public function it_throws_when_using_8bit_name_when_quoted(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MailboxName('Archive."' . "\u{1000}" . '"');
    }
}
