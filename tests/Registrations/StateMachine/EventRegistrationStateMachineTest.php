<?php

namespace TitleDK\Calendar\Tests\Registrations\StateMachine;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Registrations\EventRegistration;
use TitleDK\Calendar\Registrations\StateMachine\EventRegistrationStateMachine;

class EventRegistrationStateMachineTest extends SapphireTest
{
    /** @var EventRegistrationStateMachine */
    private $stateMachine;

    public function setUp()
    {
        $registration = new EventRegistration();
        $registration->write();
        $this->stateMachine = new EventRegistrationStateMachine($registration);
        return parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function test__construct()
    {
        $this->assertEquals('Available', $this->stateMachine->getStatus());
    }

    public function testAwaitingPayment()
    {
        $this->stateMachine->awaitingPayment();
        $this->assertEquals('AwaitingPayment', $this->stateMachine->getStatus());

    }

    public function testPaymentExpired()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentExpired();
        $this->assertEquals('PaymentExpired', $this->stateMachine->getStatus());
    }

    public function testPaymentFailed()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentFailed();
        $this->assertEquals('Unpaid', $this->stateMachine->getStatus());
    }

    public function testTryAgainAfterPaymentFailed()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentFailed();
        $this->stateMachine->tryAgainAfterPaymentFailed();
        $this->assertEquals('AwaitingPayment', $this->stateMachine->getStatus());
    }

    public function testMakeTicketAvailableAfterPaymentTimedOut()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->makeTicketAvailableAfterPaymentTimedOut();
        $this->assertEquals('Available', $this->stateMachine->getStatus());    }

    public function testPaymentSucceeded()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentSucceeded();
        $this->assertEquals('Paid', $this->stateMachine->getStatus());
    }

    public function testBooked()
    {
        $this->stateMachine->awaitingPayment();
        $this->stateMachine->paymentSucceeded();
        $this->stateMachine->booked();
        $this->assertEquals('Booked', $this->stateMachine->getStatus());
    }

}
