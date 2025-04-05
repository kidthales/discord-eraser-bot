<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserTest extends KernelTestCase
{
    /**
     * @var EntityManager|null
     */
    private ?EntityManager $entityManager;

    /**
     * @param int|string|null $discordId
     * @param array|null $roles
     * @return User
     */
    public static function getSubject(int|string|null $discordId = null, ?array $roles = null): User
    {
        $user = new User();

        if ($discordId !== null) {
            $user->setDiscordId($discordId);
        }

        if ($roles !== null) {
            $user->setRoles($roles);
        }

        return $user;
    }

    /**
     * @return array[]
     */
    public function provider_discordIdValidation(): array
    {
        $notValidError = ['message' => 'This value is not a Discord Snowflake.', 'code' => 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3'];

        return [
            [self::getSubject(), [
                'message' => 'This value should not be null.',
                'code' => 'ad32d13f-c3d4-423b-909a-857b961eb720'
            ]],
            [self::getSubject(13), $notValidError],
            [self::getSubject('failit'), $notValidError],
            [self::getSubject(175928847299117063), null],
            [self::getSubject('175928847299117063'), null],
            [self::getSubject('1759288472991170630'), null],
            [self::getSubject('0759288472991170630'), $notValidError],
            [self::getSubject('17592884729911706300'), $notValidError],
            [self::getSubject(PHP_INT_MAX), null],
            [self::getSubject((string)PHP_INT_MAX), null],
            [self::getSubject('9999999999999999999'), null],
            [self::getSubject('10000000000000000000'), $notValidError],
            [self::getSubject(PHP_INT_MIN), $notValidError],
            [self::getSubject((string)PHP_INT_MIN), $notValidError]
        ];
    }

    /**
     * @return array[]
     */
    public function provider_roleValidation(): array
    {
        $errorMessagePrefix = 'This value should satisfy at least one of the following constraints: [1] This collection should contain exactly 0 elements. ';
        $errorCode = 'f27e6d6c-261a-4056-b391-6673a623531c';

        $errorSuperOnly = [
            'message' =>$errorMessagePrefix . "[2] This collection should contain only 'ROLE_SUPER_ADMIN'.",
            'code' => $errorCode
        ];

        return [
            [self::getSubject(175928847299117063), null],
            [self::getSubject(175928847299117063, []), null],
            [self::getSubject(175928847299117063, ['failit']), $errorSuperOnly],
            [self::getSubject(175928847299117063, [User::ROLE_USER]), $errorSuperOnly],
            [self::getSubject(175928847299117063, [User::ROLE_SUPER_ADMIN, User::ROLE_USER]), [
                'message' => $errorMessagePrefix . '[2] This collection should contain exactly 1 element.',
                'code' => $errorCode
            ]],
            [self::getSubject(175928847299117063, [User::ROLE_SUPER_ADMIN]), null],
        ];
    }

    /**
     * @param User $user
     * @param array{
     *     message: string,
     *     code: string
     * }|null $expectedError
     * @return void
     * @dataProvider provider_discordIdValidation
     * @dataProvider provider_roleValidation
     */
    public function test_validation(User $user, ?array $expectedError): void
    {
        self::bootKernel();
        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get(ValidatorInterface::class);

        $errors = $validator->validate($user);

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
     * @return array[]
     */
    public function provider_getRolesBehavior(): array
    {
        return [
            [self::getSubject(175928847299117063), [User::ROLE_USER]],
            [self::getSubject(175928847299117063, [User::ROLE_SUPER_ADMIN]), [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_USER
            ]],
            [
                self::getSubject(175928847299117063, [
                    User::ROLE_SUPER_ADMIN,
                    'test',
                    User::ROLE_SUPER_ADMIN,
                    'test',
                    User::ROLE_USER,
                    'test2'
                ]),
                [User::ROLE_SUPER_ADMIN, 'test', User::ROLE_USER, 'test2']
            ],
            [
                self::getSubject(175928847299117063, [
                    'test',
                    User::ROLE_SUPER_ADMIN,
                    'test',
                    User::ROLE_SUPER_ADMIN,
                    'test2'
                ]),
                ['test', User::ROLE_SUPER_ADMIN, 'test2', User::ROLE_USER]
            ]
        ];
    }

    /**
     * @param User $user
     * @param string[] $expected
     * @return void
     * @dataProvider provider_getRolesBehavior
     */
    public function test_getRolesBehavior(User $user, array $expected): void
    {
        $roles = $user->getRoles();
        self::assertSameSize($expected, $roles);
        for ($i = 0; $i < count($expected); ++$i) {
            self::assertSame($expected[$i], $roles[$i]);
        }
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_traits(): void
    {
        $user = self::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        self::assertIsInt($user->getId());
        self::assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
        self::assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->entityManager = self::bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
