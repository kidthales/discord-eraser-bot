<?php

declare(strict_types=1);

namespace App\Tests\Security\Discord;

use App\Security\Discord\RequestValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

final class RequestValidatorTest extends KernelTestCase
{
    /**
     * @return RequestValidator
     */
    static private function getSubject(): RequestValidator
    {
        return self::getContainer()->get(RequestValidator::class);
    }

    /**
     * @return void
     */
    public function test_validate_throw_bad_request_http_exception(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $request = Request::create('/');

        try {
            $subject->validate($request);
            self::fail('Request header missing exception not thrown (' . RequestValidator::HEADER_ED25519 . ')');
        } catch (Throwable $e) {
            self::assertInstanceOf(BadRequestHttpException::class, $e);
            self::assertSame('Request header empty: ' . RequestValidator::HEADER_ED25519, $e->getMessage());
        }

        $request->headers->set(RequestValidator::HEADER_ED25519, 'test-ed25519');

        try {
            $subject->validate($request);
            self::fail('Request header missing exception not thrown (' . RequestValidator::HEADER_TIMESTAMP . ')');
        } catch (Throwable $e) {
            self::assertInstanceOf(BadRequestHttpException::class, $e);
            self::assertSame('Request header empty: ' . RequestValidator::HEADER_TIMESTAMP, $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function test_validate_throw_unknown_error(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $request = Request::create(uri: '/', method: 'POST', content: '"Test"');
        $request->headers->set(RequestValidator::HEADER_ED25519, 'test-ed25519');
        $request->headers->set(RequestValidator::HEADER_TIMESTAMP, 'test-timestamp');

        try {
            $subject->validate($request);
            self::fail('Discord request header validation exception not thrown');
        } catch (Throwable $e) {
            self::assertSame('hex2bin(): Input string must be hexadecimal string', $e->getMessage());
        }
    }
}
