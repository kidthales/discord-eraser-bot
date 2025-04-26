<?php

namespace App\HttpClient;

use App\DependencyInjection\Parameters;
use App\Dto\Discord\Api\Channel;
use App\Dto\Discord\Api\PartialGuild;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class DiscordApi
{
    /**
     * @param HttpClientInterface $discordApiClient
     * @param SerializerInterface $serializer
     * @param string $accept
     * @param string $baseUri
     */
    public function __construct(
        private HttpClientInterface                                                $discordApiClient,
        private SerializerInterface                                                $serializer,
        #[Autowire(param: Parameters::DISCORD_API_CLIENT_ACCEPT)] private string   $accept,
        #[Autowire(param: Parameters::DISCORD_API_CLIENT_BASE_URI)] private string $baseUri
    )
    {
    }

    /**
     * @param string $token
     * @return DiscordApi
     */
    public function withBearerToken(string $token): DiscordApi
    {
        return new DiscordApi(
            HttpClient::createForBaseUri($this->baseUri, [
                'headers' => [
                    'Accept' => $this->accept,
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]),
            $this->serializer,
            $this->accept,
            $this->baseUri
        );
    }

    /**
     * @param int|string|null $before
     * @param int|string|null $after
     * @param int|null $limit
     * @param bool|null $withCounts
     * @return PartialGuild[]
     * @see https://discord.com/developers/docs/resources/user#get-current-user-guilds
     */
    public function getCurrentUserGuilds(
        int|string|null $before = null,
        int|string|null $after = null,
        ?int            $limit = null,
        ?bool           $withCounts = null
    ): array
    {
        $params = [];

        if (null !== $before) {
            $params['before'] = $before;
        }

        if (null !== $after) {
            $params['after'] = $after;
        }

        if (null !== $limit) {
            $params['limit'] = $limit;
        }

        if (null !== $withCounts) {
            $params['with_counts'] = $withCounts;
        }

        return $this->serializer->deserialize(
            $this->request('GET', 'users/@me/guilds', ['query' => $params]),
            PartialGuild::class . '[]',
            'json'
        );
    }

    /**
     * @param string $guildId
     * @return array
     */
    public function getGuildChannels(string $guildId): array
    {
        return $this->serializer->deserialize(
            $this->request('GET', sprintf('guilds/%s/channels', $guildId)),
            Channel::class . '[]',
            'json'
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return string
     */
    public function request(string $method, string $url, array $options = []): string
    {
        try {
            return $this->discordApiClient->request($method, $url, $options)->getContent();
        } catch (Throwable $e) {
            throw new RuntimeException(message: 'Encountered an error while performing request', previous: $e);
        }
    }
}
