<?php

    namespace App\Routing\Interfaces;

    interface Methods
    {
        public function initialize(array|string|\Closure $action): bool;
    }