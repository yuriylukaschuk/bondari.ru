<?php

namespace Tests\Feature;

use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StackTest extends TestCase
{
    public function test_orders_can_be_shipped(): void
    {
        Mail::fake();

        // Выполните доставку заказа...

        // Утверждение, что ни одно письмо не было отправлено...
        Mail::assertNothingSent();

        // Утверждение, что было отправлено одно письмо...
        Mail::assertSent(OrderShipped::class);

        // Утверждение, что было отправлено два письма...
        Mail::assertSent(OrderShipped::class, 2);

        // Утверждение, что почтовое сообщение было отправлено на адрес электронной почты...
        Mail::assertSent(OrderShipped::class, 'example@laravel.com');

        // Утверждение, что почтовое сообщение было отправлено на несколько адресов электронной почты...
        Mail::assertSent(OrderShipped::class, ['example@laravel.com', '...']);

        // Утверждение, что другое письмо не было отправлено...
        Mail::assertNotSent(AnotherMailable::class);

        // Утверждение, что всего было отправлено 3 письма...
        Mail::assertSentCount(3);
    }
}
