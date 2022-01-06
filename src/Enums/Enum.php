<?php

    declare(strict_types=1);

    namespace IOL\SSO\SDK\Enums;

    use IOL\SSO\SDK\Exceptions\EnumException;

    class Enum implements \JsonSerializable
    {
        protected string $value;

        /**
         * @throws EnumException
         */
        public function __construct(string $value)
        {
            $this->value = $value;

            $reflection = new \ReflectionClass(get_called_class());

            if(!in_array($this->value, array_values($reflection->getConstants()))){
                throw new EnumException('Value "'.$this->value.'" is not allowed.', 2084);
            }
        }

        public function jsonSerialize(): string
        {
            return $this->value;
        }

        public static function getAllValues(): array
        {
            $reflection = new \ReflectionClass(get_called_class());

           return array_values($reflection->getConstants());
        }
    }
