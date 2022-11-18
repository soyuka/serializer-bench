<?php
namespace Soyuka\Tests\Benchmark;

use Soyuka\DataBuilder;
use PhpBench\Attributes as Bench;
use Soyuka\Dto\Collection;
use Soyuka\Dto\Element;
use Symfony\Component\Marshaller\Context\Context;
use Symfony\Component\Marshaller\Context\NativeContextBuilder\HookNativeContextBuilder;
use Symfony\Component\Marshaller\Context\NativeContextBuilder\NullableDataNativeContextBuilder;
use Symfony\Component\Marshaller\Context\NativeContextBuilder\TypeNativeContextBuilder;
use Symfony\Component\Marshaller\Context\Option\HooksOption;
use Symfony\Component\Marshaller\Hook\PhpstanType\PhpstanTypeHookNativeContextBuilder;
use Symfony\Component\Marshaller\Output\OutputStreamOutput;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Marshaller\Marshaller;
use Symfony\Component\Marshaller\MarshallerInterface;
use Symfony\Component\Marshaller\Output\TempStreamOutput;

use function Symfony\Component\Marshaller\marshal_generate;

// use function Symfony\Component\Marshaller\marshal_generate;

class SerializerBench
{
    private SerializerInterface $serializer;
    private MarshallerInterface $marshaller;
    private Context $context;
    public function setUp() {
        DataBuilder::build();
        $this->serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer()], [new JsonEncoder()]);
        $this->marshaller = new Marshaller([new NullableDataNativeContextBuilder(), new TypeNativeContextBuilder(), new PhpstanTypeHookNativeContextBuilder(), new HookNativeContextBuilder()]);

        $hooks = new HooksOption([
            sprintf('%s::$collection', Collection::class) => static function (\ReflectionProperty $property, string $accessor, string $format, array $context): string {
                $context['accessor'] = $accessor;
                $context['enclosed'] = false;

                unset($context['hooks'][Collection::class]);
                
                return $context['property_name_generator']($property, $context['property_separator'], $context).marshal_generate(sprintf('array<int, %s>', Element::class), $format, $context);
            }
        ]);

        $this->context = new Context($hooks);

        $this->marshaller->generate(Collection::class, 'json', $this->context);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSerializer'])]
    public function benchSerialize($params)
    {
        if ('symfony' === $params['serializer']) {
            $this->serializer->serialize(DataBuilder::$data, 'json', ['datetime_format' => 'Yms']);
        }
dd(DataBuilder::$data);
        if ('serge' === $params['serializer']) {
            $this->marshaller->marshal(DataBuilder::$data, 'json', new TempStreamOutput(), $this->context);
        }
    }

    public function provideSerializer() {
        yield ['serializer' => 'symfony'];
        yield ['serializer' => 'serge'];
    }
}
