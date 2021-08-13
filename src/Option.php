<?php

namespace Jungi\Core;

/**
 * @template T
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class Option
{
    public static function Some($value): self
    {
        return new Some($value);
    }

    public static function None(): self
    {
        return new None();
    }

    /**
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * @return bool
     */
    abstract public function isNone(): bool;

    /**
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Option<U>
     */
    abstract public function andThen(callable $fn): self;

    /**
     * @template U
     *
     * @param callable(T): Option<U> $fn
     *
     * @return Option<U>
     */
    abstract public function andThenTo(callable $fn): self;

    /**
     * @return T
     */
    abstract public function unwrap();

    /**
     * @param T $value
     *
     * @return T
     */
    abstract public function unwrapOr($value);

    /**
     * @param callable(): T $fn
     *
     * @return T
     */
    abstract public function unwrapOrElse(callable $fn);

    /**
     * @template E
     * @param E $err
     */
    abstract public function asOkOr($err): Result;
}

/**
 * @template T
 *
 * @internal
 * @see Option::Some()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class Some extends Option
{
    /** @var T */
    private $value;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isSome(): bool
    {
        return true;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function andThen(callable $fn): Option
    {
        return new self($fn($this->value));
    }

    public function andThenTo(callable $fn): Option
    {
        return $fn($this->value);
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return $this->value;
    }

    /**
     * @param T $value
     *
     * @return T
     */
    public function unwrapOr($value)
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $fn)
    {
        return $this->value;
    }

    /**
     * @template E
     * @param E $err
     */
    public function asOkOr($err): Result
    {
        return Result::Ok($this->value);
    }
}

/**
 * @internal
 * @see Option::None()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class None extends Option
{
    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function andThen(callable $fn): Option
    {
        return $this;
    }

    public function andThenTo(callable $fn): Option
    {
        return $this;
    }

    public function unwrap()
    {
        throw new \LogicException('Called on an "None" value.');
    }

    public function unwrapOr($value)
    {
        return $value;
    }

    public function unwrapOrElse(callable $fn)
    {
        return $fn();
    }

    /**
     * @template E
     * @param E $err
     */
    public function asOkOr($err): Result
    {
        return Result::Err($err);
    }
}
