<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Guild;
use App\Entity\User;
use App\Tests\EntityManageable;
use DateTimeImmutable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GuildTest extends KernelTestCase
{
    use EntityManageable;

    /**
     * @param int|string|null $discordId
     * @param bool $installed
     * @return Guild
     */
    public static function getSubject(int|string|null $discordId = null, ?bool $installed = null): Guild
    {
        $guild = new Guild();

        if ($discordId !== null) {
            $guild->setDiscordId($discordId);
        }

        if ($installed !== null) {
            $guild->setInstalled($installed);
        }

        return $guild;
    }

    /**
     * @return array[]
     */
    public function provider_discordIdValidation(): array
    {
        $notValidError = ['message' => 'This value is not a Discord Snowflake.', 'code' => 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3'];

        return [
            [self::getSubject(installed: true), [
                'message' => 'This value should not be null.',
                'code' => 'ad32d13f-c3d4-423b-909a-857b961eb720'
            ]],
            [self::getSubject(13, installed: false), $notValidError],
            [self::getSubject('failit', installed: true), $notValidError],
            [self::getSubject(175928847299117063, installed: false), null],
            [self::getSubject('175928847299117063', installed: true), null],
            [self::getSubject('1759288472991170630', installed: false), null],
            [self::getSubject('0759288472991170630', installed: true), $notValidError],
            [self::getSubject('17592884729911706300', installed: false), $notValidError],
            [self::getSubject(PHP_INT_MAX, installed: true), null],
            [self::getSubject((string)PHP_INT_MAX, installed: false), null],
            [self::getSubject('9999999999999999999', installed: true), null],
            [self::getSubject('10000000000000000000', installed: false), $notValidError],
            [self::getSubject(PHP_INT_MIN, installed: true), $notValidError],
            [self::getSubject((string)PHP_INT_MIN, installed: false), $notValidError]
        ];
    }

    /**
     * @return array[]
     */
    public function provider_installedValidation(): array
    {
        return [
            [self::getSubject(175928847299117063, true), null],
            [self::getSubject(175928847299117063, false), null],
            [self::getSubject(175928847299117063), [
                'message' => 'This value should not be null.',
                'code' => 'ad32d13f-c3d4-423b-909a-857b961eb720'
            ]]
        ];
    }

    /**
     * @param Guild $guild
     * @param array{
     *     message: string,
     *     code: string
     * }|null $expectedError
     * @return void
     * @dataProvider provider_discordIdValidation
     * @dataProvider provider_installedValidation
     */
    public function test_validation(Guild $guild, ?array $expectedError): void
    {
        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get(ValidatorInterface::class);

        $errors = $validator->validate($guild);

        if ($expectedError === null) {
            self::assertCount(0, $errors);
            return;
        }

        self::assertCount(1, $errors);
        $error = $errors->get(0);
        self::assertSame($expectedError['message'], $error->getMessage());
        self::assertSame($expectedError['code'], $error->getCode());
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_traits(): void
    {
        $guild = self::getSubject(175928847299117063, true);
        $this->entityManager->persist($guild);
        $this->entityManager->flush();

        self::assertIsInt($guild->getId());
        self::assertInstanceOf(DateTimeImmutable::class, $guild->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $guild->getUpdatedAt());
    }
}
