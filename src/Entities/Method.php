<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionFunction;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;
use function sprintf;

/**
 * @method static string plop(int $value)
 */
class Method
{
    public string $name;

    private bool $isStatic = false;

    /** @var array<string>|null */
    public ?array $docblock = null;

    public ?string $parameters = null;

    public ?string $returnType = null;

    public ?string $source = null;

    public ?int $line = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function new(string $name): self
    {
        return new self($name);
    }

    public static function fromFunction(string $name, ReflectionFunction $function): self
    {
        return self::new($name)
            ->docblock(FunctionReflection::docblock($function))
            ->isStatic(FunctionReflection::isStatic($function))
            ->parameters(FunctionReflection::parameters($function))
            ->returnType(FunctionReflection::returnType($function))
            ->source(FunctionReflection::source($function))
            ->line(FunctionReflection::line($function));
    }

    public static function fromMethod(string $name, ReflectionMethod $method): self
    {
        return self::new($name)
            ->docblock(FunctionReflection::docblock($method))
            ->isStatic($method->isStatic())
            ->parameters(FunctionReflection::parameters($method))
            ->returnType(FunctionReflection::returnType($method));
    }

    /**
     * @param array<string>|null $docblock
     */
    public function docblock(?array $docblock): self
    {
        $this->docblock = $docblock;

        return $this;
    }

    private function isStatic(bool $isStatic): self
    {
        $this->isStatic = $isStatic;

        return $this;
    }

    public function parameters(?string $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function returnType(?string $returnType): self
    {
        $this->returnType = $returnType;

        return $this;
    }

    public function source(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function line(?int $line): self
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        /** @var Collection<int, string> $collection */
        $collection = new Collection();

        return $collection
            ->merge($this->docblockLines())
            ->add($this->definition())
            ->add('{')
            ->add('}')
            ->toArray();
    }

    public function toDocTag(): string
    {
        return sprintf(
            ' * @method %s%s %s(%s)',
            $this->isStatic ? 'static ' : '',
            $this->returnTypeForDocTag() ?? 'mixed',
            $this->name,
            $this->parameters
        );
    }

    public function toLinkTag(): ?string
    {
        if ($this->source === null || $this->line === null) {
            return null;
        }

        return sprintf(
            ' * @see project://%s L%d',
            Str::after($this->source, base_path() . DIRECTORY_SEPARATOR),
            $this->line
        );
    }

    private function returnTypeForDocTag(): ?string
    {
        if ($this->returnType === null) {
            return null;
        }

        if (Str::startsWith($this->returnType, '?')) {
            return Str::of($this->returnType)->after('?')->append('|null')->toString();
        }

        return $this->returnType;
    }

    /**
     * @return Collection<int, string>
     */
    private function docblockLines(): Collection
    {
        if ($this->docblock === null) {
            return new Collection();
        }

        return collect($this->docblock)
            ->prepend('/**')
            ->add('*/');
    }

    private function definition(): string
    {
        $definition = 'public ';

        if ($this->isStatic) {
            $definition .= 'static ';
        }

        $definition .= "function {$this->name}({$this->parameters})";

        if ($this->returnType === null) {
            return $definition;
        }

        return $definition . ": {$this->returnType}";
    }
}
