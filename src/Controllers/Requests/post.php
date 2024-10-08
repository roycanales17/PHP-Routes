<?php

    namespace App\Routing\Controllers\Requests;

    use App\Routing\Interfaces\Methods;
    use App\Routing\Scheme\Requests;

    class post extends Requests implements Methods
    {
        /**
         * Class alias name.
         *
         * Note: Required.
         * @var string
         */
        public static string $name = 'post';

        /**
         * Set up the action function, depends on you want.
         * Note: Just make sure to follow the correct procedure.
         *
         * @param array|string|\Closure $action
         * @return bool
         */
        public function initialize(array|string|\Closure $action): bool
        {
            // Add your logic here if necessary
            // Note: If you use custom logic here, make sure to return `true`.
            return false;
        }
    }