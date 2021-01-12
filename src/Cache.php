<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira;

use Amp\Cache\ArrayCache;
use Amp\Cache\NullCache;
use Amp\Cache\SerializedCache;
use Amp\Http\Client\Request;
use Amp\Promise;
use Amp\Serialization\NativeSerializer;
use function Amp\call;

/**
 * Class Cache
 *
 * Using a cache through network/application interceptors was failed
 * because Response does not support serialization.
 *
 * @package Spacetab\SDK\Jira
 */
final class Cache
{
    public const DEFAULT_TTL = 60 * 5;

    private SerializedCache $cache;
    private int $ttl;

    /**
     * Cache constructor.
     *
     * @param SerializedCache $cache
     * @param int $ttl – in seconds.
     */
    public function __construct(SerializedCache $cache, int $ttl = self::DEFAULT_TTL)
    {
        $this->cache = $cache;
        $this->ttl   = $ttl;
    }

    public static function disabled(): self
    {
        return new Cache(new SerializedCache(new NullCache(), new NativeSerializer()));
    }

    public static function enabled(int $ttl = self::DEFAULT_TTL): self
    {
        return new Cache(new SerializedCache(new ArrayCache(), new NativeSerializer()), $ttl);
    }

    public function memorize(Request $request, callable $promise): Promise
    {
        return call(function () use ($request, $promise) {
            $key = md5(serialize([$request->getUri(), $request->getBody()]));

            $cached = yield $this->cache->get($key);

            if ($cached !== null) {
                return $cached;
            }

            $data = yield $promise();
            yield $this->cache->set($key, $data, $this->ttl);

            return $data;
        });
    }
}
