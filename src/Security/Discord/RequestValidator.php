<?php

declare(strict_types=1);

namespace App\Security\Discord;

use App\DependencyInjection\Parameters;
use Elliptic\EdDSA;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireInline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestValidator
{
    public const string HEADER_ED25519 = 'X-Signature-Ed25519';
    public const string HEADER_TIMESTAMP = 'X-Signature-Timestamp';

    /**
     * @param EdDSA $ec
     * @param string $publicKey
     */
    public function __construct(
        #[AutowireInline(class: EdDSA::class, arguments: ['ed25519'])] private readonly EdDSA $ec,
        #[Autowire(param: Parameters::DISCORD_APP_PUBLIC_KEY)] private readonly string        $publicKey
    )
    {
    }

    /**
     * @param Request $request
     * @return bool
     * @throws BadRequestHttpException
     */
    public function validate(Request $request): bool
    {
        $signature = $request->headers->get(self::HEADER_ED25519);
        $timestamp = $request->headers->get(self::HEADER_TIMESTAMP);

        if (empty($signature)) {
            throw new BadRequestHttpException('Request header empty: ' . self::HEADER_ED25519);
        }

        if (empty($timestamp)) {
            throw new BadRequestHttpException('Request header empty: ' . self::HEADER_TIMESTAMP);
        }

        $message = [...unpack('C*', $timestamp), ...unpack('C*', $request->getContent())];
        return $this->ec->keyFromPublic($this->publicKey)->verify($message, $signature);
    }
}
