<?php
namespace Test\App\Contact;

use App\Contact\ContactAction;
use PHPUnit\Framework\TestCase;

class ContactActionTest extends TestCase
{

    /**
     * @var ContactAction
     */
    private $action;


    public function setUp()
    {
        $this->action = new ContactAction();
    }
}
