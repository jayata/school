<?php

namespace School\FOSchildBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SchoolFOSchildBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
