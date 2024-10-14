<?php

    namespace App\Routing\Scheme\Helper;

    trait Prefix
    {
        private int $totalAdded = 0;

        /**
         * Add a prefix to the route URI.
         *
         * This method appends a given prefix to the route's URI, which can be useful for
         * grouping routes under a common namespace or path.
         */
        public function prefix(string $prefix): self
        {
            if ($prefix) {
                $this->totalAdded += 1;
                self::$prefixes[] = $prefix;
            }
            return $this;
        }

        protected function updatePrefixList(): void
        {
            if ($this->totalAdded) {
                self::$prefixes = array_slice(self::$prefixes, 0, -$this->totalAdded);
            }
        }
    }