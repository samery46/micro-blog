<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('unlike_icon')]
final class UnLikeIconComponent
{
    public string $class;
}
