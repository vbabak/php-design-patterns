<?php

declare(strict_types = 1);

namespace Psr\Container {
    /**
     * Describes the interface of a container that exposes methods to read its entries.
     */
    interface ContainerInterface
    {
        /**
         * Finds an entry of the container by its identifier and returns it.
         *
         * @param string $id Identifier of the entry to look for.
         *
         * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
         * @throws ContainerExceptionInterface Error while retrieving the entry.
         *
         * @return mixed Entry.
         */
        public function get($id);

        /**
         * Returns true if the container can return an entry for the given identifier.
         * Returns false otherwise.
         *
         * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
         * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
         *
         * @param string $id Identifier of the entry to look for.
         *
         * @return bool
         */
        public function has($id);
    }

    /**
     * Base interface representing a generic exception in a container.
     */
    interface ContainerExceptionInterface
    {
    }

    /**
     * No entry was found in the container.
     */
    interface NotFoundExceptionInterface extends ContainerExceptionInterface
    {
    }
}


namespace DependencyInjection {

    use Exception;
    use function PHPSTORM_META\type;
    use Psr\Container\ContainerExceptionInterface;
    use Psr\Container\ContainerInterface;
    use Psr\Container\NotFoundExceptionInterface;

    interface ContainerArgumentsExceptionInterface extends ContainerExceptionInterface
    {
    }

    class ContainerException extends Exception implements ContainerExceptionInterface
    {
    }

    class NotFoundException extends Exception implements NotFoundExceptionInterface
    {
    }

    class ContainerArgumentsException extends Exception implements ContainerArgumentsExceptionInterface
    {
    }

    abstract class ContainerAbstract implements ContainerInterface
    {
        /**
         * @param string $dependency_alias String, register dependency name in the container
         * @param array $instance_options Array with description of required instance and it parameters
         *
         * $instance_options keys are:
         *  'class' - string, this is associated class or $dependency_alias
         *  'constructor_args' - array, this is array with parameters that will be passed into constructor
         *  'dependencies' - array, this is key-value array with another dependencies
         *  'public' - bool, treat instance as public (shared) or create new instance on every call
         *
         * @return ContainerAbstract
         */
        abstract public function set(string $dependency_alias, array $instance_options): ContainerAbstract;
    }

    class Container extends ContainerAbstract
    {
        protected $instances = [];

        protected $config = [];

        public function has($id)
        {
            $id = $this->normalize($id);

            return isset($this->instances[$id]);
        }

        public function set(string $dependency_alias, array $instance_options): ContainerAbstract
        {
            $id = $this->normalize($dependency_alias);

            $class = $instance_options['class'] ?? null;

            if (gettype($class) !== 'string') {
                throw new ContainerArgumentsException;
            }

            $constructor_args = $instance_options['constructor_args'] ?? [];

            if (!is_array($constructor_args)) {
                throw new ContainerArgumentsException;
            }

            $dependencies = $instance_options['dependencies'] ?? [];

            if (!is_array($dependencies)) {
                throw new ContainerArgumentsException;
            }

            $public = $instance_options['public'] ?? false;

            if (!is_bool($public)) {
                throw new ContainerArgumentsException;
            }

            $this->config[$id] = [
                'class' => $class,
                'constructor_args' => $constructor_args,
                'dependencies' => $dependencies,
                'public' => $public
            ];

            return $this;
        }

        public function get($id)
        {
            $id = $this->normalize($id);
            if (!isset($this->config[$id])) {
                throw new NotFoundException;
            }

            $instance = null;

            $config = $this->config[$id];

            $instance_class = $config['class'];
            $constructor_args = $config['constructor_args'];
            $is_public = $config['public'];
            if ($is_public && $this->has($id)) {
                $instance = $this->instances[$id];

            } else {
                foreach ($constructor_args as $c_key => $c_arg) {
                    if (is_string($c_arg) && strpos($c_arg, '$') === 0) {
                        $required_dependency = substr($c_arg, 1);
                        $constructor_args[$c_key] = $this->get($required_dependency);
                    }
                }
                $instance = new $instance_class(...$constructor_args);
            }

            if ($is_public) {
                $this->instances[$id] = $instance;
            }


            return $instance;
        }

        protected function normalize(string $id): string
        {
            $id = trim($id);
            $id = strtolower($id);
            $id = str_replace(array('\\', '/'), '', $id);

            return $id;
        }
    }

    /**
     * Testing DI
     */
    interface KitchenTableInterface
    {
        public function __construct(int $width, int $length, int $height);

        public function setHeight(int $h);

        public function getDimensions(): array;
    }

    interface KitchenInterface
    {
        public function __construct(KitchenTableInterface $t);

        public function getKitchenTableDimensions(): array;
    }

    class KitchenTable implements KitchenTableInterface
    {
        protected $width;
        protected $length;
        protected $height;

        public function __construct(int $width, int $length, int $height)
        {
            $this->width = $width;
            $this->length = $length;
            $this->height = $height;
        }

        public function setHeight(int $h)
        {
            $this->height = $h;

            return $this;
        }

        public function getDimensions(): array
        {
            return [
                'w' => $this->width,
                'h' => $this->height,
                'l' => $this->length,
            ];
        }
    }

    class Kitchen implements KitchenInterface
    {
        protected $ktable;

        public function __construct(KitchenTableInterface $t)
        {
            $this->ktable = $t;
        }

        public function getKitchenTableDimensions(): array
        {
            return $this->ktable->getDimensions();
        }
    }

    $container = new Container();
    $container->set('Kitchen', [
        'class' => '\DependencyInjection\Kitchen',
        'constructor_args' => ['$KTable'], // $KTable is definition from $container->set('KTable', ...
        'public' => true
    ]);
    $container->set('KTable', [
        'class' => '\DependencyInjection\KitchenTable',
        'constructor_args' => [120, 200, 80],
        'public' => true
    ]);

    /** @var KitchenTableInterface $ktable */
    $ktable = $container->get('KTable');
    $ktable->setHeight(110); // height is 110, testing public instance

    $kitchen = $container->get('Kitchen');
    var_dump($kitchen->getKitchenTableDimensions()); // h is 110
}

