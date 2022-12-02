<?php

declare(strict_types=1);

namespace Soyuka\Tests\Benchmark;

use PhpBench\Attributes as Bench;
use Soyuka\DataBuilder;
use Soyuka\Dto\Collection;
use Soyuka\Dto\Element;
use Symfony\Component\Marshaller\Context\Context;
use Symfony\Component\Marshaller\Context\Option\TypeOption;
use Symfony\Component\Marshaller\Marshaller;
use Symfony\Component\Marshaller\MarshallerInterface;
use Symfony\Component\Marshaller\NativeContext\FormatterAttributeNativeContextBuilder;
use Symfony\Component\Marshaller\NativeContext\HookNativeContextBuilder;
use Symfony\Component\Marshaller\NativeContext\NameAttributeNativeContextBuilder;
use Symfony\Component\Marshaller\NativeContext\TypeFormatterNativeContextBuilder;
use Symfony\Component\Marshaller\Output\MemoryStreamOutput;
use Symfony\Component\Marshaller\Type\PhpstanTypeExtractor;
use Symfony\Component\Marshaller\Type\ReflectionTypeExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerBench
{
    private SerializerInterface $serializer;
    private MarshallerInterface $marshaller;
    private Context $context;

    public function setUp()
    {
        DataBuilder::build();

        $this->serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer()], [new JsonEncoder()]);
        $this->marshaller = new Marshaller(new PhpstanTypeExtractor(new ReflectionTypeExtractor()), [new HookNativeContextBuilder(), new TypeFormatterNativeContextBuilder(), new NameAttributeNativeContextBuilder(), new FormatterAttributeNativeContextBuilder()], sys_get_temp_dir().'/symfony_marshaller');
        $this->context = new Context(new TypeOption(Collection::class.'<'.Element::class.'>'));

        $this->marshaller->marshal(DataBuilder::$data, 'json', new MemoryStreamOutput(), $this->context);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSerializer'])]
    #[Bench\Iterations(10)]
    public function bench($params)
    {
        if ('symfony' === $params['serializer']) {
            $this->serializer->serialize(DataBuilder::$data, 'json', ['datetime_format' => 'Yms']);
        }
        if ('serge' === $params['serializer']) {
            $this->marshaller->marshal(DataBuilder::$data, 'json', new MemoryStreamOutput(), $this->context);
        }
        if ('jsonencode' === $params['serializer']) {
            json_encode(DataBuilder::$data);
        }
    }

    public function provideSerializer()
    {
        yield 'Symfony Serializer' => ['serializer' => 'symfony'];
        yield 'Serge' => ['serializer' => 'serge'];
        yield 'JsonEncode' => ['serializer' => 'jsonencode'];
    }
}
