<?php

    namespace App\Routing\Scheme\Helper;

    trait Controller
    {
        private int $totalAdded = 0;

        public function controller(string $className): self
        {
            if (class_exists($className)) {
                $this->totalAdded += 1;
                self::$controllers[] = $className;
            } else {
                throw new \InvalidArgumentException("Class `$className` does not exist");
            }

            return $this;
        }

        protected function updateControllerList(): void
        {
            if ($this->totalAdded) {
                self::$controllers = array_slice(self::$controllers, 0, -$this->totalAdded);
            }
        }
    }